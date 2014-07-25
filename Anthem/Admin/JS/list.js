/**
 * JS for ListAdmin.  Changes TableAdmin operation a bit.
 */
var ListAdmin = {
  init: function () {

  },

  /**
   * Changes sorting order.
   *
   * @param column
   */
  sortBy: function (column)
  {
    var currentColumn = $('#sort-column').attr('data-column');
    var currentDir = $('#sort-asc').is(':visible') ? 'asc' : 'desc';

    // If clicked on column we're already sorting by, toggle directions
    if (currentColumn == column)
    {
      var dir;
      if (currentDir == 'asc')
        dir = 'desc';
      else
        dir = 'asc';

      TableAdmin.updateTable({
        'page': 1,
        'sort.column': column,
        'sort.dir': dir
      }, function () {
        $('#sort-' + currentDir).hide();
        $('#sort-' + dir).show();
      });
    }
    else  // Otherwise switch to new column with ascending direction
    {
      TableAdmin.updateTable({
        'page': 1,
        'sort.column': column,
        'sort.dir': 'asc'
      }, function () {
        $('#sort-column').attr('data-column', column).text($('.sort-link[data-column=' + column + ']').text());
        $('#sort-asc').show();
        $('#sort-desc').hide();
      });
    }
  }
};

$(document).ready(ListAdmin.init);

// Override TableAdmin functions

TableAdmin.fitTableSizes = function () {};

TableAdmin.toggleFilters = function () {
  if (!$('#filters-container').is(':visible'))
  {
    $('.global-actions [data-id=search]').addClass('active');
    $('#filters-container').show();
  }
  else
  {
    $('.global-actions [data-id=search]').removeClass('active');
    $('#filters-container').hide();
    TableAdmin.resetFilters();
  }
};
