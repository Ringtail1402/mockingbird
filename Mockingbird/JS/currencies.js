/**
 * Mockingbird currency admin page javascript.
 */
var Currencies = {
  init: function () {
    // Handle table update
    $('.table-body').on('update-table', function () {
    });

    // Handle form update
    $('#form-container').on('form-load', function () {
      Currencies.updateForm();
      $('#is_primary').click(Currencies.updateForm);
    });
  },

  updateForm: function () {
    if ($('#is_primary').is(':checked'))
      $('.row-rate_to_primary').hide();
    else
      $('.row-rate_to_primary').show();
  }
};

$(document).ready(Currencies.init);
