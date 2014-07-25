/**
 * Mockingbird transaction admin page javascript.
 */
var Transactions = {
  init: function () {
    // Handle table update
    $('.table-body').on('update-table', function () {
      // Remove any stale tooltips etc.
      $('.tooltip, .typeahead').remove();

      // Set up autocomplete for filters
      $('.title-typeahead').typeahead({
        source: Transactions.searchTitle
      });
      $('.counterparty-typeahead').typeahead({
        source: Transactions.searchCounterparties
      });
      $('.tag-typeahead').typeahead({
        source: Transactions.searchTags
      });

      // Set up tooltips
      Transactions.setupTagTooltips();

      // Set up mass selected
      $('.mass-selector').click(function () {
        var $transaction = $(this).parents('.transaction');
        if ($(this).is(':checked'))
        {
          $transaction.addClass('selected');
          $('.transaction[data-parent-id=' + $transaction.attr('data-id') +']').addClass('selected')
        }
        else
        {
          $transaction.removeClass('selected');
          $('.transaction[data-parent-id=' + $transaction.attr('data-id') +']').removeClass('selected')
        }
        if (!TableAdmin.mass_actions_update_pending)
        {
          TableAdmin.mass_actions_update_pending = true;
          window.setTimeout(TableAdmin.updateMassActions, 500);
        }
      });
      $('.transaction').click(Transactions.onRowClick);

      // Set up subtransactions expansion
      $('.transactions-toggle').click(function (e) {
        Transactions.toggleSubtransactions($(this).attr('data-id'));
        e.stopPropagation();
      });
    });

    // Handle form update
    $('#form-container').on('form-load', function () {
      // Remove any stale tooltips etc.
      $('.tooltip, .typeahead').remove();

      // Set up autocomplete for fields
      if (!$('#counter_party').attr('disabled'))
        $('#counter_party').attr('autocomplete', 'off').typeahead({
          source: Transactions.searchCounterparties
        });
      $('.row-title input').attr('autocomplete', 'off').typeahead({
        source: Transactions.searchTitle
      });
      $('.row-tags input[type=text]').typeahead({
        source: Transactions.searchTags
      });

      // Initialize subforms
      PropelSubformsInput.init();
      $('#subtransactions').on('subform-add', function () {
        Util.updateAmountFromAccountCurrency('account_id', 'amount');
        $('.row-title:last input').attr('autocomplete', 'off').typeahead({
          source: Transactions.searchTitle
        });
        $('.row-tags:last input[type=text]').typeahead({
          source: Transactions.searchTags
        });
      });

      // React to type field changes by showing/hiding parts of the form
      $('[name=type]').change(Transactions.updateType);
      Transactions.updateType();

      // React to accounts fields changes by updating and/or converting
      // amount field to account currency
      Util.updateAmountFromAccountCurrency('account_id', 'amount', true);
      Util.updateAmountFromAccountCurrency('target_account_id', 'target_amount', true);
      $('[name=account_id]').change(function () {
        Util.updateAmountFromAccountCurrency('account_id', 'amount');
      });
      $('[name=target_account_id]').change(function () {
        Util.updateAmountFromAccountCurrency('target_account_id', 'target_amount');
      });

      // React to amount field change by updating target amount field
      $('[name=amount]').keyup(Transactions.updateTargetAmountFromAmount);
    });

    setInterval(function () {
      Transactions.updateTotalAmount();
    }, 600);
  },

  onRowClick: function (e) {
    if ($(e.target).is('a, button')) return;
    var $transaction = ($(this).hasClass('sub') && !$(this).hasClass('exclude-parent')) ? $('.transaction[data-id=' + $(this).attr('data-parent-id') + ']') : $(this);
    var $selector = $('.mass-selector', $transaction);
    if ($selector.is(':checked'))
    {
      $transaction.removeClass('selected');
      $('.transaction[data-parent-id=' + $transaction.attr('data-id') +']').removeClass('selected')
      $selector.removeAttr('checked');
    }
    else
    {
      $transaction.addClass('selected');
      $('.transaction[data-parent-id=' + $transaction.attr('data-id') +']').addClass('selected')
      $selector.attr('checked', 'checked');
    }
    if (!TableAdmin.mass_actions_update_pending)
    {
      TableAdmin.mass_actions_update_pending = true;
      window.setTimeout(TableAdmin.updateMassActions, 500);
    }
  },

  /**
   * Sets up tooltips for transaction tags in table view.
   * Tags are loaded on the fly via ajax.
   * TODO: tooltips are somewhat glitchy at the moment.
   */
  setupTagTooltips: function ()
  {
    $('.transaction-tags').each(function () {
      var $this = $(this);
      if ($this.data('tooltip')) return;
      var id = $this.attr('data-id');
      $this.tooltip({
        delay: 100,
        title: function () {
          $.get(ajaxBaseURL + 'getTags', { id: id },
            function(response) {
              $this.data('tooltip').options.title = response;
              if ($this.data('tooltip').hoverState == 'in')
                $this.tooltip('hide').tooltip('show');
            }
          );
          return '';
        }
      });
    });
  },

  /**
   * Expands/collapses subtransactions of the specified master transaction
   * in table view.  Loads them as needed.
   *
   * @param id
   */
  toggleSubtransactions: function (id) {
    var $chevron = $('.transactions-toggle[data-id=' + id + '] .chevron');
    var $subtransactions = $('.transaction[data-parent-id=' + id + ']');
    if ($chevron.hasClass('icon-chevron-up'))
    {
      // Collapse
      $chevron.removeClass('icon-chevron-up').addClass('icon-chevron-down');
      $subtransactions.hide();
    }
    else
    {
      $chevron.removeClass('icon-chevron-down').addClass('icon-chevron-up');
      if ($subtransactions.length)
      {
        // Expand, already loaded
        $subtransactions.show();
      }
      else
      {
        // Expand, will need to load part of table
        Admin.busy();
        $.get(ajaxBaseURL + 'table',
          {
            page: 1,
            per_page: 0,
            filter_reset: 1,
            filter: { parent: id },
            'sort.column': 0,
            no_save: 1,
            print: $('body').hasClass('print') ? 1 : 0
          },
          function(response) {
            // Append subtransactions
            $('.transaction[data-id=' + id + ']').after(response.table);
            $('.transaction[data-parent-id=' + id + ']').click(Transactions.onRowClick);
            if ($('.transaction[data-id=' + id + ']').hasClass('selected'))
              $('.transaction[data-parent-id=' + id + ']').addClass('selected');
            Admin.ready();
          }, 'json'
        );
      }
    }
  },

  /**
   * Autocomplete function for title filter.  Searches via ajax call.
   *
   * @param query Search string.
   * @param process Callback.
   */
  searchTitle: function(query, process)
  {
    $.get(ajaxBaseURL + 'searchTitle', { q: query }, function(response) {
        process(response);
    });
  },

  /**
   * Autocomplete function for counter party filter.  Searches via ajax call.
   *
   * @param query Search string.
   * @param process Callback.
   */
  searchCounterparties: function(query, process)
  {
    $.get(ajaxBaseURL + 'searchCounterparties', { q: query }, function(response) {
      process(response);
    });
  },

  /**
   * Autocomplete function for tag filter.  Searches via ajax call.
   *
   * @param query Search string.
   * @param process Callback.
   */
  searchTags: function(query, process)
  {
    $.get(ajaxBaseURL + 'searchTags', { q: query }, function(response) {
      process(response);
    });
  },

  /**
   * Updates form view after type radio button change.
   * Shows/hides parts of the form as needed.
   */
  updateType: function ()
  {
    var type = $('[name=type]:checked').val();
    if (type == 'transfer')
    {
      $('.row-target_account_id').show();
      $('.row-target_amount').show();
      $('.row-counter_party').hide();
    }
    else
    {
      $('.row-target_account_id').hide();
      $('.row-target_amount').hide();
      $('.row-counter_party').show();
    }

    if (type == 'master')
    {
      $('#amount').attr('readonly', 'readonly').attr('disabled', 'disabled');
      $('.row-subtransactions').show();
      Transactions.updateTotalAmount();
    }
    else
    {
      if (!$('#account_id').attr('readonly'))
        $('#amount').removeAttr('readonly').removeAttr('disabled');
      $('.row-subtransactions').hide();
      // TODO: should not delete everything at once
      $('.subform').remove();
      $('.subform-empty').show();
    }
  },

  /**
   * Updates target amount from changed amount field, converting currencies if necessary.
   * When amount field changes, target amount field is always synchronized, but it
   * can be then updated manually by user.
   */
  updateTargetAmountFromAmount: function ()
  {
    var amount = parseFloat($('[name=amount]').val());
    var rate = parseFloat($('[name=amount]').attr('data-rate'));
    var target_rate = parseFloat($('[name=target_amount]').attr('data-rate'));
    if (amount)
    {
      var target_amount = amount;
      if (rate != target_rate)
        target_amount = amount * rate / target_rate;
      $('[name=target_amount]').val($.format.number(target_amount, '0.00'));
    }
  },

  /**
   * Updates total amount for master transaction from all subtransactions, if applicable.
   */
  updateTotalAmount: function () {
    var typecontrol = $('[name=type]:checked');
    if (typecontrol.length && typecontrol.val() == 'master')
    {
      var sum = 0;
      $('#subtransactions .row-amount input').each(function () {
        var amount = parseFloat($(this).val());
        if (!isNaN(amount)) sum += amount;
      });
      $('[name=amount]').val($.format.number(sum, '0.00'));
    }
  }
};

$(document).ready(Transactions.init);
