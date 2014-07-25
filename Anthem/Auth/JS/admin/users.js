/**
 * Users admin page javascript.
 */
var Users = {
  init: function () {
    // Handle table update
    $('.table-body').on('update-table', function () {
      // Set up autocomplete for filters
      $('#filters [data-id=email]').typeahead({
        source: Users.searchEmail
      });
      $('#filters [data-id=groups]').typeahead({
        source: Users.searchGroups
      });
    });

    // Handle form update
    $('#form-container').on('form-load', function () {
      $('#is_superuser').change(Users.updateForm);
      Users.updateForm();
    });
  },

  updateForm: function () {
    // Do not show policy input for superusers, as superuser flag overrides any policies
    if ($('#is_superuser').is(':checked'))
      $('.row-policies').hide();
    else
      $('.row-policies').show();
  },

  /**
   * Autocomplete function for email filter.  Searches via ajax call.
   *
   * @param query Search string.
   * @param process Callback.
   */
  searchEmail: function(query, process)
  {
    $.get(ajaxBaseURL + 'searchEmail', { q: query }, function(response) {
      process(response);
    });
  },

  /**
   * Autocomplete function for group filter.  Searches via ajax call.
   *
   * @param query Search string.
   * @param process Callback.
   */
  searchGroups: function(query, process)
  {
    $.get(ajaxBaseURL + 'searchGroups', { q: query }, function(response) {
      process(response);
    });
  },

  lockUser: function(id) {
    Admin.busy();
    $.get(ajaxBaseURL + 'lockDialog', { id: id }, function (response) {
      Admin.ready();
      Admin.modal(_t('Auth.LOCK_DIALOG'), response, {
        'ok': function () {
          Admin.busy();
          $.post(ajaxBaseURL + 'lockDialog', $('#modal-body').serialize(), function (response) {
            TableAdmin.updateTable();
          });
        },
        'cancel': null
      });
    })
  },

  unlockUser: function(id) {
    $.post(ajaxBaseURL + 'unlockUser', { id: id }, function (response) {
      TableAdmin.updateTable();
    });
  }
};

$(document).ready(Users.init);
