/**
 * Mockingbord dashboard Javascript.
 */
var Dashboard = {
  last_hash: null,
  datetime: 'today',
  mode: 'month',
  year: new Date().getFullYear(),
  month: new Date().getMonth() + 1,

  /**
   * Page initilization.
   */
  init: function ()
  {
    Dashboard.updateFromHash();
    // Set up hash part of URL change handler
    window.setInterval(function () {
      if (document.location.hash != Dashboard.last_hash)
      {
        Dashboard.updateFromHash();
      }
    }, 250);
  },

  /**
   * Updates everything from hash.
   */
  updateFromHash: function ()
  {
    var hash = document.location.hash;
    if (hash.charAt(0) == '#') hash = hash.substr(1);
    hash = Admin.parseQueryString(hash);
    if (hash.mode && (hash.mode == 'all' || hash.mode == 'year' || hash.mode == 'month'))
      Dashboard.mode = hash.mode;
    if (hash.balance) Dashboard.datetime = hash.balance;
    if (hash.year) Dashboard.year = hash.year;
    if (hash.month) Dashboard.month = hash.month;

    Dashboard.setHash();
    Dashboard.loadAccounts();
    Dashboard.loadCalendar();
  },

  /**
   * Sets hash from current parameters.
   */
  setHash: function ()
  {
    Dashboard.last_hash = 'mode=' + Dashboard.mode;
    if (Dashboard.mode == 'month' || Dashboard.mode == 'year')
      Dashboard.last_hash += '&year=' + Dashboard.year;
    if (Dashboard.mode == 'month')
      Dashboard.last_hash += '&month=' + Dashboard.month;
    Dashboard.last_hash += '&balance=' + Dashboard.datetime;
    document.location.hash = Dashboard.last_hash;
    Dashboard.last_hash = document.location.hash;
  },

  /**
   * Loads/reloads accounts into view.
   */
  loadAccounts: function ()
  {
    $('#spinner').show();
    $.get(baseURL + '/ajax/dashboard_accounts/' + Dashboard.datetime, {},
      function (response) {
        $('#accounts-container').html(response);
        $('#spinner').hide();
      }
    );
  },

  /**
   * Loads/reloads calendar into view.
   */
  loadCalendar: function ()
  {
    $('#spinner').show();
    var url = Dashboard.mode == 'month' ?
                (baseURL + '/ajax/dashboard_calendar/month/' + Dashboard.year + '/' + Dashboard.month) :
              Dashboard.mode == 'year' ?
                (baseURL + '/ajax/dashboard_calendar/year/' + Dashboard.year) :
              baseURL + '/ajax/dashboard_calendar/all';
    $.get(url, {},
      function (response) {
        $('#calendar-container').html(response);
        $('#spinner').hide();

        $('#up-to-all').click(function () {
          Dashboard.mode = 'all';
          Dashboard.setHash();
          Dashboard.loadCalendar();
        })

        $('#up-to-year, #down-to-year').click(function () {
          Dashboard.mode = 'year';
          Dashboard.setHash();
          Dashboard.loadCalendar();
        })

        $('#down-to-month').click(function () {
          Dashboard.mode = 'month';
          Dashboard.setHash();
          Dashboard.loadCalendar();
        })

        $('#back-month').click(function () {
          Dashboard.month--;
          if (Dashboard.month == 0)
          {
            Dashboard.month = 12;
            Dashboard.year--;
          }
          Dashboard.setHash();
          Dashboard.loadCalendar();
        });

        $('#forward-month').click(function () {
          Dashboard.month++;
          if (Dashboard.month == 13)
          {
            Dashboard.month = 1;
            Dashboard.year++;
          }
          Dashboard.setHash();
          Dashboard.loadCalendar();
        });

        $('#back-year').click(function () {
          Dashboard.year--;
          Dashboard.setHash();
          Dashboard.loadCalendar();
        });

        $('#forward-year').click(function () {
          Dashboard.year++;
          Dashboard.setHash();
          Dashboard.loadCalendar();
        });

        $('#today').click(function () {
          Dashboard.month = new Date().getMonth() + 1;
          Dashboard.year = new Date().getFullYear();
          Dashboard.datetime = 'today';
          Dashboard.setHash();
          Dashboard.loadCalendar();
          Dashboard.loadAccounts();
        });
      }
    );
  },

  downToYear: function(year)
  {
    Dashboard.mode = 'year';
    Dashboard.year = year;
    Dashboard.setHash();
    Dashboard.loadCalendar();
  },

  downToMonth: function(month)
  {
    Dashboard.mode = 'month';
    Dashboard.month = month;
    Dashboard.setHash();
    Dashboard.loadCalendar();
  },

  setDay: function(year, month, day)
  {
    Dashboard.datetime = year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    Dashboard.setHash();
    Dashboard.loadAccounts();
  }
};

$(document).ready(Dashboard.init);
