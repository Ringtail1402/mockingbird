/**
 * Misc. JS code
 */
var Util = {
  init: function () {
    $('.table-body').on('update-table', Util.updateCounts);
    $('#form-container').on('form-load', Util.updateCounts);
  },

  /**
   * Updates various stats.
   */
  updateCounts: function () {
    $.get(baseURL + '/admin/mockingbird.transactions.admin/ajax/menuUpdate', {
      url: document.location.pathname.replace(baseURL, ''),
      print: $('body').hasClass('print') ? 1 : 0
    }, function (response) {
      $('.navbar').replaceWith($(response));
      $('.nav .dropdown-menu .active').each(function () { $(this).parents('.dropdown').addClass('active'); });
    });
  },

  /**
   * Updates amount field to the currency of selected account/currency field.
   *
   * @param account Name attribute for account/currency <select>.
   * @param amount Name attribute for amount <input>.
   * @param initial Do not actually update rate, only currency
   */
  updateAmountFromAccountCurrency: function(account, amount, initial)
  {
    var $option = $('[name=' + account + '] option:selected');
    var new_rate = parseFloat($option.attr('data-rate'));
    var $rows = $('.row-' + amount);
    $rows.each(function () {
      var $row = $(this);
      var old_amount = parseFloat($('input', $row).val());
      var old_rate = parseFloat($('input', $row).attr('data-rate'));

      if (new_rate)  // ignore if empty option selected or empty amount
      {
        $('input', $row).attr('data-currency', $option.attr('data-сurrency'));
        if (new_rate != old_rate)
        {
          // Convert amount to new currency
          $('input', $row).attr('data-rate', new_rate);
          if (old_amount)
          {
            var new_amount = old_amount * old_rate / new_rate;
            if (!initial) $('input', $row).val($.format.number(new_amount, '0.00'));
          }

          // Update currency prefix
          if ($option.attr('data-format-pre').length)
          {
            $('.currency-container', $row).addClass('input-prepend');
            $('.pre', $row).text($option.attr('data-format-pre'));
            $('.pre', $row).css('display', 'inline-block');
          }
          else
          {
            $('.currency-container', $row).removeClass('input-prepend');
            $('.pre', $row).hide();
          }

          // Update currency postfix
          if ($option.attr('data-format-post').length)
          {
            $('.currency-container', $row).addClass('input-append');
            $('.post', $row).text($option.attr('data-format-post'));
            $('.post', $row).css('display', 'inline-block');
          }
          else
          {
            $('.currency-container', $row).removeClass('input-append');
            $('.post', $row).hide();
          }
        }
      }
    });
  }
};

$(document).ready(Util.init);
