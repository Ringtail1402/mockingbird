/**
 * Mockingbird category admin page javascript.
 */
var TransactionTags = {
  init: function () {
    // Handle table update
    $('.table-body').on('update-table', function () {
      // Set up autocomplete for filters
      $('#filters [data-id=title]').typeahead({
        source: TransactionTags.searchTitle
      });
    });

    // Handle form update
    $('#form-container').on('form-load', function () {
    });
  },

  /**
   * Autocomplete function for tag filter.  Searches via ajax call.
   *
   * @param query Search string.
   * @param process Callback.
   */
  searchTitle: function(query, process)
  {
    $.get(ajaxBaseURL + 'searchTitle', { q: query }, function(response) {
      process(response);
    });
  }
};

$(document).ready(TransactionTags.init);
