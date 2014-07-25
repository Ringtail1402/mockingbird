/**
 * Mockingbord budget Javascript.
 */
var Budget = {
  last_hash: null,
  view_mode: 'table',
  edit_mode: false,
  form_last_state: null,
  chart_loaded: false,
  charts_data: {},
  charts_options: {},
  resize_pending: false,

  /**
   * Page initilization.
   */
  init: function ()
  {
    Budget.updateFromHash();
    // Set up hash part of URL change handler
    window.setInterval(function () {
      if (document.location.hash != Budget.last_hash)
      {
        Budget.updateFromHash();
      }
    }, 250);

    // Reload budget on form update
    $('#budget-form select').change(function () {
      Budget.loadBudget();
    });

    // Exiting page
    $(window).on('beforeunload', function (e) {
      if (Budget.edit_mode &&
          Budget.form_last_state != $('#form-container').serialize())
      {
        e.returnValue = _t('Admin.UNSAVED_CHANGES');
        e.stopPropagation();
        return e.returnValue;
      }
    });

    // Redisplay chart on window resize
    $(window).resize(function () {
      if (Budget.view_mode != 'chart' || Budget.edit_mode || !Budget.charts_data.incomes) return;
      if (Budget.resize_pending) return;
      Budget.resize_pending = true;
      setTimeout(function () {
        Budget.resize_pending = false;
        Budget.displayCharts();
      }, 500)
    });
  },

  /**
   * Updates everything from hash.
   */
  updateFromHash: function ()
  {
    var hash = document.location.hash;
    if (hash.charAt(0) == '#') hash = hash.substr(1);
    hash = Admin.parseQueryString(hash);
    if (hash['year'])
      $('#budget-year option[value=' + hash['year'] + ']').attr('selected', 'selected');
    if (hash['month'])
      $('#budget-month option[value=' + hash['month'] + ']').attr('selected', 'selected');
    if (hash['as'])
    {
      $('#budget-as option[value=' + hash['as'] + ']').attr('selected', 'selected');
      Budget.view_mode = hash['as'];
    }
    if (hash['edit'])
      Budget.edit_mode = true;
    else
      Budget.edit_mode = false;

    Budget.loadBudget();
  },

  /**
   * Sets hash from current parameters.
   */
  setHash: function ()
  {
    var hash = $('#budget-form').serialize();
    if (Budget.edit_mode) hash += '&edit=1';
    document.location.hash = hash;
    $('#print-link').attr('href', '?print=1#' + hash);
    $('#edit-link').attr('href', '#' + hash + '&edit=1');
    Budget.last_hash = document.location.hash;
  },

  /**
   * Asks user for a confirmation if there have been some unsaved changes.
   */
  confirmUnsavedChanges: function () {
    if (Budget.edit_mode && Budget.form_last_state != null &&
        Budget.form_last_state != $('#form-container').serialize())
    {
      var result = confirm(_t('Admin.UNSAVED_CHANGES'));

      // Restore hash as it might have been changed by hand
      if (!result)
        document.location.hash = Budget.last_hash;

      return result;
    }
    return true;
  },

  /**
   * Saves budget.
   */
  save: function () {
    Budget.setHash();
    Admin.busy();
    $.post(baseURL + '/ajax/budget_edit', $('#form-container').serialize(), Budget.onFormLoad);
  },

  /**
   * Cancels edit and switches to main view.
   */
  cancelEdit: function () {
    if (!Budget.confirmUnsavedChanges()) return;
    Budget.edit_mode = false;
    Budget.loadBudget();
  },

  updateForm: function () {
    if ($('#copy option').length < 2) $('.row-copy').hide();

    if (!$('#entries .subform').length)
      $('.row-copy').show();
    else
      $('.row-copy').hide();
    if ($('#copy option:selected').val())
      $('.row-entries').hide();
    else
      $('.row-entries').show();
  },

  /**
   * Handles form loading.
   *
   * @param data
   */
  onFormLoad: function (data) {
    $('#budget-container').hide();
    $('.chart-container').hide();
    $('.footer').show();
    document.getElementById('form-container').innerHTML = data.form;
    $('#form-container').show();
    PropelSubformsInput.init();
    $('#budget-form, .global-actions').hide();
    $('#form-links-container').show();
    if (data.valid)
      Budget.form_last_state = $('#form-container').serialize();
    Budget.updateForm();
    $('#entries').on('subform-add', Budget.updateForm);
    $('#entries').on('subform-delete', Budget.updateForm);
    $('#copy').change(Budget.updateForm);
    Admin.ready();
  },

  /**
   * Loads and displays a chart.
   */
  loadBudget: function () {
    Budget.view_mode = $('#budget-as option:selected').val();
    if (!Budget.confirmUnsavedChanges()) return;
    if (Budget.view_mode == 'chart' && !Budget.chart_loaded) return;
    Budget.setHash();
    Admin.busy();
    if (Budget.edit_mode)
      $.get(baseURL + '/ajax/budget_edit', $('#budget-form').serialize(), Budget.onFormLoad);
    else
    {
      if (Budget.view_mode == 'chart')
      {
        $.get(baseURL + '/ajax/budget_chart_data', $('#budget-form').serialize(), function (data) {
          $('#form-container').hide();

          if (data.message)
          {
            $('.chart-container').hide();
            $('#budget-container').html(data.message).show();
          }
          else
          {
            $('.chart-container').show();
            $('#budget-container').hide();

            var title = data.incomes.title;
            delete data.incomes.title;
            Budget.charts_data.incomes = new google.visualization.DataTable(data.incomes);
            Budget.charts_options.incomes = { colors: [ '#080', '#0a0', '#0c0' ], title: title,
                          hAxis: { slantedText: false, maxTextLines: 2, showTextEvery: 1 },
                          chartArea : { width: '70%', height: '80%' } };

            title = data.expenses.title;
            delete data.expenses.title;
            Budget.charts_data.expenses = new google.visualization.DataTable(data.expenses);
            Budget.charts_options.expenses = { colors: [ '#800', '#a00', '#c00' ], title: title,
              hAxis: { slantedText: false, maxTextLines: 2, showTextEvery: 1 },
              chartArea : { width: '70%', height: '80%' } };

            Budget.displayCharts();
          }

          $('#budget-form, .global-actions').show();
          $('#form-links-container').hide();
          $('.footer').hide();

          if (data.editable)
            $('#edit-link').show();
          else
            $('#edit-link').hide();
          if (data.printable)
            $('#print-link').show();
          else
            $('#print-link').hide();
          Budget.form_last_state = null;
          Admin.ready();
        });
      }
      else // 'table'
      {
        $.get(baseURL + '/ajax/budget_data', $('#budget-form').serialize(), function (data) {
          $('#form-container').hide();
          $('.chart-container').hide();
          $('#budget-container').html(data.budget).show();
          $('#budget-form, .global-actions').show();
          $('#form-links-container').hide();
          $('.footer').hide();
          if (data.editable)
            $('#edit-link').show();
          else
            $('#edit-link').hide();
          if (data.printable)
            $('#print-link').show();
          else
            $('#print-link').hide();
          Budget.form_last_state = null;
          Admin.ready();
        });
      }
    }
  },

  onChartsLoaded: function () {
    Budget.chart_loaded = true;
    if (Budget.view_mode == 'chart') Budget.loadBudget();
  },

  displayCharts: function () {
    var width, height;
    if ($('body').hasClass('print'))
    {
      $('body').css('width', 1000);
      width = $('#chart-income-container').width();
      height = 250;
    }
    else
    {
      width = $('#chart-income-container').width();
      height = ($(window).height() - $('#chart-income-container').offset().top) / 2.2;
    }
    Budget.charts_options.incomes.width = width;
    Budget.charts_options.incomes.height = height;
    Budget.charts_options.expenses.width = width;
    Budget.charts_options.expenses.height = height;

    var chart1 = new google.visualization.ColumnChart(document.getElementById('chart-income-container'));
    var chart2 = new google.visualization.ColumnChart(document.getElementById('chart-expense-container'));
    chart1.draw(Budget.charts_data.incomes, Budget.charts_options.incomes);
    chart2.draw(Budget.charts_data.expenses, Budget.charts_options.expenses);
  }
};

$(document).ready(Budget.init);
