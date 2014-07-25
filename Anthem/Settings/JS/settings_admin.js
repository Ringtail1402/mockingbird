/**
 * Settings UI JS.
 */
var Settings = {
  last_hash: null,
  page: null,
  form_last_state: null,

  /**
   * Page initilization.
   */
  init: function ()
  {
    // Initial state
    Settings.page = $('.nav-tabs li.active').attr('data-page');
    Settings.form_last_state = $('#form-container').serialize();

    Settings.updateFromHash();
    // Set up hash part of URL change handler
    setInterval(function () {
      if (document.location.hash != Settings.last_hash)
      {
        Settings.updateFromHash();
      }
    }, 250);

    // Exiting page
    $(window).on('beforeunload', function (e) {
      if (Settings.form_last_state != $('#form-container').serialize())
      {
        e.returnValue = _t('Admin.UNSAVED_CHANGES');
        e.stopPropagation();
        return e.returnValue;
      }
    });
  },

  /**
   * Updates everything from hash.
   */
  updateFromHash: function (noload)
  {
    var hash = document.location.hash, page;
    if (hash.charAt(0) == '#') hash = hash.substr(1);
    hash = Admin.parseQueryString(hash);
    if (hash.page && $('.nav-tabs li[data-page=' + hash.page + ']'))
      page = hash.page;
    else
      page = '';
    Settings.setHash();
    Settings.load(document.location.pathname.indexOf('/global') == document.location.pathname.length - '/global'.length, page);
  },

  /**
   * Sets hash from current parameters.
   */
  setHash: function ()
  {
    var hash = '', page = $('.nav-tabs li.active').attr('data-page');
    if (page)
      hash = 'page=' + page;
    document.location.hash = hash;
    Settings.last_hash = document.location.hash;
  },


  /**
   * Asks user for a confirmation if there have been some unsaved changes.
   */
  confirmUnsavedChanges: function () {
    if (Settings.form_last_state != $('#form-container').serialize())
    {
      var result = confirm(_t('Admin.UNSAVED_CHANGES'));

      // Restore hash as it might have been changed by hand
      if (!result)
        document.location.hash = Settings.last_hash;

      return result;
    }
    return true;
  },

  onFormLoad: function (response) {
    // Unload previous form
    $('.receive-unload').trigger('form-unload');

    // Update form
    // Do not use $('#form-container').html() here as that strips <script> tags, which we process manually.
    document.getElementById('form-container').innerHTML = response;

    // Eval form scripts manually
    $('#form-container script').each(function() { eval($(this).text()); });

    // Fire event for custom initialization
    $('#form-container').trigger('form-load').show();

    // Store form state, if the form is in valid state
    Settings.page = $('.nav-tabs li.active').attr('data-page');
    if ($('#is-valid').val())
      Settings.form_last_state = $('#form-container').serialize();

    // Scroll to first error, if any
    var error = $('.error');
    if (error.length)
      $(document).scrollTop($(error[0]).offset().top - 10);

    Settings.setHash();
    Admin.pollNotices();
    Admin.ready();
  },

  /**
   * Loads settings page
   */
  load: function (global, page) {
    if (Settings.page == page)
    {
      $('#form-container').show();
      return;
    }
    if (!Settings.confirmUnsavedChanges()) return;

    Admin.busy();
    $.get(baseURL + '/settings' + (global ? '/global' : ''), { page: page }, Settings.onFormLoad);
  },

  /**
   * Saves current settings page
   */
  save: function (global) {
    Admin.busy();
    $.post(baseURL + '/settings' + (global ? '/global' : '') +
           '?page=' + Settings.page, $('#form-container').serialize(), Settings.onFormLoad);
  }
};

$(document).ready(Settings.init);