/**
 * Mockingbird account admin page javascript.
 */
var Accounts = {
  init: function () {
    // Handle table update
    $('.table-body').on('update-table', function () {
    });

    // Handle form update
    $('#form-container').on('form-load', function () {
      // Show either balance or initial amount field
      if ($('#initial_amount').is('[readonly]'))
        $('.row-initial_amount').hide();
      else
        $('.row-balance').hide()

      // React to currency fields changes by updating and/or converting
      // initial amount field to selected currency
      $('[name=currency_id]').change(function () {
        Util.updateAmountFromAccountCurrency('currency_id', 'initial_amount');
      });
    });
  }
};

$(document).ready(Accounts.init);
