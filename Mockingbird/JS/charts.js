/**
 * Mockingbord charts Javascript.
 */
var Charts = {
  last_hash: null,
  chart_data: null,
  chart_options: null,
  resize_pending: false,

  /**
   * Page initilization.
   */
  init: function ()
  {
    Admin.busy();

    Charts.updateFromHash(true);
    // Set up hash part of URL change handler
    window.setInterval(function () {
      if (document.location.hash != Charts.last_hash)
      {
        Charts.updateFromHash();
      }
    }, 250);

    // Reload chart on form update
    $('#charts-form select').change(function () {
      Charts.updateForm();
      Charts.setHash();
      Charts.loadChart();
    });

    // Redisplay chart on window resize
    $(window).resize(function () {
      if (Charts.resize_pending) return;
      Charts.resize_pending = true;
      setTimeout(function () {
        Charts.resize_pending = false;
        Charts.displayChart();
      }, 500)
    });
  },

  /**
   * Updates everything from hash.
   */
  updateFromHash: function (noload)
  {
    var hash = document.location.hash;
    if (hash.charAt(0) == '#') hash = hash.substr(1);
    hash = Admin.parseQueryString(hash);
    var fields = ['type', 'for', 'by', 'in', 'year', 'month'];
    for (var i in fields)
    {
      var field = fields[i];
      if (hash[field])
        $('#chart-' + field + ' option[value=' + hash[field] + ']').attr('selected', 'selected');
    }

    Charts.updateForm();
    Charts.setHash();
    if (!noload) Charts.loadChart();
  },

  /**
   * Sets hash from current parameters.
   */
  setHash: function ()
  {
    var hash = $('#charts-form').serialize();
    document.location.hash = hash;
    $('#print-link').attr('href', '?print=1#' + hash);
    Charts.last_hash = document.location.hash;
  },

  /**
   * Updates form state.
   */
  updateForm: function ()
  {
    var for_val = $('#chart-for').val();
    if (for_val == 'balance')
    {
      if ($('#chart-by [value=category]').is(':selected'))
        $('#chart-by [value=account]').attr('selected', 'selected');
      $('#chart-by [value=category]').removeAttr('selected').attr('disabled', 'disabled');
    }
    else
      $('#chart-by [value=category]').removeAttr('disabled');

    var in_val = $('#chart-in').val();
    if (in_val == 'all' || in_val == 'year')
      $('#chart-month').attr('disabled', 'disabled').hide();
    else
      $('#chart-month').removeAttr('disabled').show();
    if (in_val == 'all')
      $('#chart-year').attr('disabled', 'disabled').hide();
    else
      $('#chart-year').removeAttr('disabled').show();
  },

  /**
   * Loads and displays a chart.
   */
  loadChart: function () {
    $.get(baseURL + '/ajax/chart_data', $('#charts-form').serialize(), function (data) {
      var colors = data.colors;
      var title = data.title;
      delete data.colors;
      delete data.title;
      Charts.chart_data = new google.visualization.DataTable(data);
      Charts.chart_options = { colors: colors, title: title };
      Admin.ready();
      Charts.displayChart();
    });
  },

  /**
   * Displays/redisplays a chart.
   */
  displayChart: function () {
    if (!Charts.chart_data) return;

    if ($('body').hasClass('print'))
    {
      $('body').css('width', 1000);
      Charts.chart_options.width = $('#charts-container').width();
      Charts.chart_options.height = 500;
    }
    else
    {
      Charts.chart_options.width = $('#charts-container').width();
      Charts.chart_options.height = $(window).height() - $('#charts-container').offset().top;
    }
    switch ($('#chart-type').val())
    {
      case 'pie':
        if (Charts.chart_data.getNumberOfRows() < 1)
        {
          $('#charts-container').html(_t('CHART_NO_DATA'));
          return;
        }
        var chart = new google.visualization.PieChart(document.getElementById('charts-container'));
        Charts.chart_options.pieResidueSliceLabel = _t('CHART_OTHER');
        Charts.chart_options.sliceVisibilityThreshold = 1.5/360;
        Charts.chart_options.chartArea = { width: '90%', height: '90%' };
        break;

      case 'time':
        if (Charts.chart_data.getNumberOfColumns() < 2)
        {
          $('#charts-container').html(_t('CHART_NO_DATA'));
          return;
        }
        var chart = new google.visualization.SteppedAreaChart(document.getElementById('charts-container'));
        Charts.chart_options.isStacked = true;
        Charts.chart_options.areaOpacity = 0.6;
        Charts.chart_options.connectSteps = false;
        Charts.chart_options.hAxis = { slantedText: false };
        Charts.chart_options.chartArea = { width: '70%', height: '80%' };
        break;
    }
    chart.draw(Charts.chart_data, Charts.chart_options);
  }
};

$(document).ready(Charts.init);
