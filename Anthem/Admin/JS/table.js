/**
 * TableAdminPage Javascript.
 */
var TableAdmin = {
  id: null,
  last_hash: null,
  mass_actions_update_pending: false,
  form_last_state: null,
  filters_validation: null,
  user_hash: false,

  /**
   * Page initilization.
   */
  init: function ()
  {
    // If the page has been requested with a hash, do not normalize it immediately
    // to avoid breaking Back button.
    if (document.location.hash.length) TableAdmin.user_hash = true;

    // Initial page load
    TableAdmin.updateTableFromHash(function () {
      // Set up hash part of URL change handler
      window.setInterval(function () {
        if (document.location.hash != TableAdmin.last_hash)
        {
          if (document.location.hash.length) TableAdmin.user_hash = true;
          TableAdmin.updateTableFromHash();
        }
      }, 250);

      // Initial sizing
      TableAdmin.fitTableSizes();
    });

    // Prevent clicking on disabled links
    $('.global-actions a').click(function (e) {
      if ($(this).hasClass('disabled')) e.preventDefault();
    });

    // Resizing
    var resize_pending = false;
    $(window).on('resize', function (e) {
      if (resize_pending) return;
      resize_pending = true;
      window.setTimeout(function () {
        TableAdmin.fitTableSizes();
        resize_pending = false;
      }, 150);
    });

    // Exiting page
    $(window).on('beforeunload', function (e) {
      if (TableAdmin.id !== null)
      {
        $('.receive-update').trigger('form-update');
        if (TableAdmin.form_last_state &&
            TableAdmin.form_last_state != $('#form-container').serialize())
        {
          e.returnValue = _t('Admin.UNSAVED_CHANGES');
          e.stopPropagation();
          return e.returnValue;
        }
      }
    });
  },

  /**
   * Fits table height to viewport.
   */
  fitTableSizes: function ()
  {
    // Fit main table viewport height to window
    var h = $(window).height();
    h -= $('.table-inner').offset().top;
    h -= $('.container-fluid.footer').height();
    $('.table-inner').css('height', h + 'px');

    // Fit headings width to main table width.  Might be necessary because of scrollbar appearing/disappearing.
    $('#thead').css('width', $('#table').width() + 1 + 'px');
  },

  /**
   * Updates global buttons (link/actions) state according to an array.
   *
   * @param buttons
   */
  updateGlobalButtons: function (buttons)
  {
    for (var button in buttons)
    {
      var status = buttons[button];
      $button = $('.global-actions      a[data-id=' + button + '], ' +
                  '.global-actions button[data-id=' + button + ']');
      if (status)
        $button.removeClass('disabled').removeAttr('disabled');
      else
        $button.addClass('disabled').attr('disabled', 'disabled');
    }
  },

  /**
   * Exactly what it says on the tin.
   */
  toggleFilters: function () {
    if (!$('#filters').is(':visible'))
    {
      $('.global-actions [data-id=search]').addClass('active');
      $('#filters').show();
    }
    else
    {
      $('.global-actions [data-id=search]').removeClass('active');
      $('#filters').hide();
      TableAdmin.resetFilters();
    }
    TableAdmin.fitTableSizes();
  },

  /**
   * Loads the table grid.
   *
   * @param parameters Parameters to pass to server.
   * @param callback   Optional callback function ran on successful update.
   */
  updateTable: function (parameters, callback)
  {
    if (typeof parameters === 'undefined') parameters = {};

    // Override some settings for print view
    if ($('body').hasClass('print'))
    {
      parameters.page = 1;
      parameters.per_page = $('#limit-results').attr('data-limit');
      parameters.print = 1;
      parameters.no_save = 1;
    }

    $.get(
      ajaxBaseURL + 'table', parameters, function(response) {
        // Check for errors
        if (response.error || !response.table)
        {
          Admin.ready();
          Admin.modal(_t('Admin.ERROR'), response.error ? response.error : _t('Admin.INTERNAL_ERROR'), { 'ok': null });
          return;
        }

        // Update table
        $('#table-container').html(response.table);
        $('.table-inner').scrollTop(0);

        // Show limit message for print view
        if ($('body').hasClass('print') && response.pages > 1)
          $('#limit-results').show();

        // Set up mass selection/actions stuff
        TableAdmin.resetMassActions();
        TableAdmin.setupMassSelect();

        // Show/hide "onhover" parts of cells as appropriate
        $('.table-inner td').mouseenter(function(e) {
          $('.onhover', $(this)).show();
        });
        $('.table-inner td').mouseleave(function(e) {
          $('.onhover', $(this)).hide();
        });

        // Update pager
        $('#pager-container').html(response.pager);

        // Update filters
        $('#filters').html(response.filters);
        if (response.has_filters)
        {
          if ($('#filters-container').length)
            $('#filters-container').show();
          else
            $('#filters').show();
          $('.global-actions [data-id=search]').addClass('active');
        }
        $('.apply-filters-on-enter').keypress(function (e) {
          if (e.key == 'enter')
          {
            TableAdmin.applyFilters();
            e.stopPropagation();
            return false;
          }
        });
        $('.apply-filters-on-change').change(TableAdmin.applyFilters);
        // Filters validation
        // TODO

        // Validation for in-place edit fields, if any
        // TODO

        // Update global link/action status
        TableAdmin.updateGlobalButtons(response.buttons);

        // Update URL hash part.  Also make filters pretty
        if (!TableAdmin.user_hash && !$('body').hasClass('print'))
          document.location.hash = response.canonical_address.
              replace(/filter%5B([^=]+)%5D/g, 'filter.$1').
              replace(/filter\.([^=]+)%5D%5B([^=]+)=/g, 'filter.$1.$2=');
        else
          TableAdmin.user_hash = false;
        TableAdmin.last_hash = document.location.hash;

        // Fire update event
        $('.table-body').trigger('update-table');

        if (typeof callback !== 'undefined') callback(response);
        TableAdmin.tableView(false);
        TableAdmin.form_last_state = null;
        TableAdmin.id = null;
        Admin.pollNotices();
        Admin.ready();
      }
    );
    Admin.busy();
  },

  /**
   * Updates table using current hash part of URL as parameters.
   *
   * @param callback Optional callback function ran on successful update.
   */
  updateTableFromHash: function (callback)
  {
    var hash = document.location.hash;
    if (hash.charAt(0) == '#') hash = hash.substr(1);

    // Un-pretty-ize filters
    hash = hash.replace(/filter\.([^=]+)\.([^=]+)=/g, 'filter[$1][$2]=').
                replace(/filter\.([^=]+)=/g, 'filter[$1]=');

    hash = Admin.parseQueryString(hash);
    if (typeof hash['id'] !== 'undefined')
      TableAdmin.edit(hash['id'], callback);
    else
      TableAdmin.updateTable(hash, callback);
  },

  /**
   * Executes a per-object action.
   *
   * @param action
   * @param id
   */
  action: function (action, id)
  {
    Admin.busy();
    $.post(ajaxBaseURL + 'action', { 'action': action, 'id': id },
      function(response) {
        // Check for errors
        if (response.error)
        {
          Admin.ready();
          Admin.modal(_t('Admin.ERROR'), response.error, { 'ok': null });
          return;
        }
        if (response.reload)
          TableAdmin.updateTable();
        else
          Admin.ready();
      }
    );
  },

  /**
   * Executes a mass action.
   *
   * @param action
   */
  massAction: function (action)
  {
    var ids = [], $checkboxes = $('.mass-selector:checked');
    $checkboxes.each(function () { ids.push($(this).attr('data-id')) });
    Admin.busy();
    $.post(ajaxBaseURL + 'massAction', { 'action': action, 'ids': ids },
      function(response) {
        // Check for errors
        if (response.error)
        {
          Admin.ready();
          Admin.modal(_t('Admin.ERROR'), response.error, { 'ok': null });
          return;
        }
        if (response.reload)
          TableAdmin.updateTable();
        else
        {
          TableAdmin.updateGlobalButtons(response.buttons);
          Admin.ready();
        }
      }
    );
  },

  /**
   * Executes a global action.
   *
   * @param action
   */
  tableAction: function (action)
  {
    Admin.busy();
    $.post(ajaxBaseURL + 'tableAction', { action: action },
      function(response) {
        // Check for errors
        if (response.error)
        {
          Admin.ready();
          Admin.modal(_t('Admin.ERROR'), response.error, { 'ok': null });
          return;
        }
        if (response.reload)
          TableAdmin.updateTable();
        else
          Admin.ready();
      }
    );
  },

  /**
   * Resets mass selection checkboxes and disables mass actions.
   */
  resetMassActions: function ()
  {
    $('button.mass-action').attr('disabled', 'disabled');
    $('input.mass-selector').removeAttr('checked');
  },

  /**
   * Updates mass actions state.
   */
  updateMassActions: function ()
  {
    // This is supposed to happen in background, so we don't use busy()/ready()
    var ids = [], $checkboxes = $('.mass-selector:checked');
    $checkboxes.each(function () { ids.push($(this).attr('data-id')) });
    if (!ids.length)
    {
      TableAdmin.resetMassActions();
      return;
    }

    $.post(ajaxBaseURL + 'updateMassActions', { ids: ids },
      function(response) {
        TableAdmin.updateGlobalButtons(response);
        TableAdmin.mass_actions_update_pending = false;
      }
    );  // this should be a GET, but the array could get quite large
  },

  /**
   * Sets up mass selection.  Useful when rows are dynamically added.
   */
  setupMassSelect: function ()
  {
    $('.mass-selector').unbind('click').click(TableAdmin.handleMassSelect);
  },

  /**
   * Handles clicks on mass selection checkboxes.
   *
   * @param e
   */
  handleMassSelect: function (e)
  {
    e.stopPropagation();

    // Highlight row
    var $target = $(this);
    if ($target.is(':checked'))
      $target.parents('tr').addClass('selected');
    else
      $target.parents('tr').removeClass('selected');

    // Disable mass actions when no checkboxes selected
    var $massSelectors = $('.mass-selector:checked');
    if (!($massSelectors.length))
    {
      TableAdmin.resetMassActions();
      return;
    }

    // Schedule a state update if it hasn't already been scheduled
    if (!TableAdmin.mass_actions_update_pending)
    {
      TableAdmin.mass_actions_update_pending = true;
      window.setTimeout(TableAdmin.updateMassActions, 500);
    }
  },

  /**
   * Changes sorting order.
   *
   * @param column
   */
  sortBy: function (column)
  {
    var $currentColumnLink = $('#active-sort-column');
    if ($currentColumnLink)
    {
      var currentColumn = $currentColumnLink.parent().attr('data-column');
      var $currentDirSpan = $('span', $currentColumnLink);
    }
    else  // Just in case
    {
      $currentColumnLink = $('<a/>');
      var currentColumn = '';
      var $currentDirSpan = $('<span/>');
    }

    // If clicked on column we're already sorting by, toggle directions
    if (currentColumn == column)
    {
      var dir;
      if ($currentDirSpan.attr('id') == 'active-sort-dir-asc')
        dir = 'desc';
      else
        dir = 'asc';

      TableAdmin.updateTable({
        'page': 1,
        'sort.column': column,
        'sort.dir': dir
      }, function () {
        $currentDirSpan.attr('id', 'active-sort-dir-' + dir);
        $currentDirSpan.html(dir == 'asc' ? '&darr;' : '&uarr;');
      });
    }
    else  // Otherwise switch to new column with ascending direction
    {
      TableAdmin.updateTable({
        'page': 1,
        'sort.column': column,
        'sort.dir': 'asc'
      }, function () {
        $currentColumnLink.attr('id', '');
        $currentDirSpan.attr('id', '');
        $currentDirSpan.html('');

        var $newColumnLink = $('th[data-column=' + column + '] a');
        var $newDirSpan = $('span', $newColumnLink);
        $newColumnLink.attr('id', 'active-sort-column');
        $newDirSpan.attr('id', 'active-sort-dir-asc');
        $newDirSpan.html('&darr;');
      });
    }
  },

  /**
   * Changes displayed page.  Trivial.
   *
   * @param page
   */
  loadPage: function(page) {
    TableAdmin.updateTable({
      'page': page
    })
  },

  /**
   * Changes number of displayed records.  Trivial.
   *
   * @param per_page
   */
  setPerPage: function(per_page) {
    TableAdmin.updateTable({
      'page': 1,
      'per_page': per_page
    });
  },

  /**
   * Applies filter fields to table.
   */
  applyFilters: function () {
    TableAdmin.updateTable('page=1&' + $('#thead-form').serialize());
  },

  /**
   * Resets all filters on table.  Trivial.
   */
  resetFilters: function () {
    TableAdmin.updateTable({
      'page': 1,
      'filter_reset': 1
    })
  },

  /**
   * Enters table view.
   */
  tableView: function (update) {
    if (update || typeof update === 'undefined')
    {
      if (!TableAdmin.confirmUnsavedChanges()) return;
      TableAdmin.updateTable();
    }
    else
    {
      $('.form-view').hide();
      $('.table-view').show();
      TableAdmin.fitTableSizes();
      // XXX Without this, thead table width might not be set correctly at times.
      // No idea why.  This seems to help.
      window.setTimeout(function () {
        TableAdmin.fitTableSizes();
      }, 150);
    }
  },

  /**
   * Enters form view.
   */
  formView: function () {
    $('.table-view').hide();
    $('.form-view').show();
  },

  /**
   * Asks user for a confirmation if there have been some unsaved changes.
   */
  confirmUnsavedChanges: function () {
    $('.receive-update').trigger('form-update');

    if (TableAdmin.form_last_state &&
        TableAdmin.form_last_state != $('#form-container').serialize())
    {
      var result = confirm(_t('Admin.UNSAVED_CHANGES'));

      // Restore hash as it might have been changed by hand
      if (!result)
      {
        document.location.hash = TableAdmin.last_hash;
      }

      return result;
    }
    return true;
  },

  onFormLoad: function (response, callback) {
    // Check for errors
    if (response.error || !response.form)
    {
      Admin.ready();
      Admin.modal(_t('Admin.ERROR'), response.error ? response.error : _t('Admin.INTERNAL_ERROR'), { 'ok': null });
      if ($('.table-view').is(':visible')) TableAdmin.tableView(true);
      return;
    }

    // Unload previous form
    $('.receive-unload').trigger('form-unload');

    // Update form
    // Do not use $('#form-container').html() here as that strips <script> tags, which we process manually.
    document.getElementById('form-container').innerHTML = response.form;
    $('#form-links-container').html(response.links);
    $('#form-links-container a').click(function (e) {
      if ($(this).hasClass('disabled')) return false;
    });

    // Switch to form view
    TableAdmin.formView();

    // Load JS validators
    // TODO

    // Eval form scripts manually
    $('#form-container script').each(function() { eval($(this).text()); });

    // Fire event for custom initialization
    $('#form-container').trigger('form-load');

    // Store form state, if the form is in valid state
    if (response.valid)
      TableAdmin.form_last_state = $('#form-container').serialize();

    // Update URL hash part
    document.location.hash = 'id=' + response.id;
    TableAdmin.last_hash = document.location.hash;
    TableAdmin.id = response.id;
    TableAdmin.user_hash = false;

    // Scroll to first error, if any
    var error = $('.error');
    if (error.length)
      $(document).scrollTop($(error[0]).offset().top - 10);

    Admin.pollNotices();

    Admin.ready();

    if (typeof callback !== 'undefined') callback(response);
  },

  /**
   * Loads creation/edition form.
   *
   * @param id
   * @param callback
   */
  edit: function (id, callback) {
    if (!TableAdmin.confirmUnsavedChanges()) return;

    $.get(ajaxBaseURL + 'form', { id: id },
      function(response) { TableAdmin.onFormLoad(response, callback); }
    );
    Admin.busy();
  },

  /**
   * Saves form.
   *
   * @param id
   * @param callback
   */
  save: function(id, callback) {
    $('.receive-update').trigger('form-update');

    $.post(ajaxBaseURL + 'form?id=' + id, $('#form-container').serialize(),
      function(response) { TableAdmin.onFormLoad(response, callback); }
    );
    Admin.busy();
  },

  preview: function () {
    $('.receive-update').trigger('form-update');

    $.post(ajaxBaseURL + 'validate', $('#form-container').serialize(),
      function(response) {
        // Check for errors
        if (response.error)
        {
          Admin.ready();
          Admin.modal(_t('Admin.ERROR'), response.error, { 'ok': null });
          return;
        }

        if (response.valid)
        {
          var $form = $('#form-container');
          $form.attr('method', 'POST');
          $form.attr('action', ajaxBaseURL + 'preview');
          $form.attr('target', '_blank');
          $form.submit();
          $form.removeAttr('method');
          $form.removeAttr('action');
          $form.removeAttr('target');
          Admin.ready();
        }
        else
        {
          TableAdmin.save(TableAdmin.id);
          alert(_t('Admin.INVALID_PREVIEW'));
        }
      }
    );
    Admin.busy();
  },

  /**
   * Saves form and creates new object immediately.
   *
   * @param id
   */
  saveAndCreate: function (id) {
    TableAdmin.save(id, function(response) {
      if (response.valid) TableAdmin.edit(0);
    });
  }
};

$(document).ready(TableAdmin.init);