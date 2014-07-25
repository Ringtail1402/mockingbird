$(document).ready(function () {
  // Initialize datepicker
  $('.table-body').on('update-table', function () {
    var updatePlaceholders = function () {
      var $filter = $(this), $filter2;
      if ($filter.attr('data-subid') == 'from')
        $filter2 = $('.datefilter[data-id=' + $filter.attr('data-id') + '][data-subid=to]');
      else
      {
        $filter2 = $filter;
        $filter = $('.datefilter[data-id=' + $filter.attr('data-id') + '][data-subid=from]');
      }
      if (!$filter.val().length && !$filter2.val().length)
      {
        $filter.attr('placeholder', _t('Admin.FROM_TO_EMPTY'));
        $filter2.attr('placeholder', _t('Admin.FROM_TO_EMPTY'));
      }
      else if (!$filter.val())
      {
        $filter.attr('placeholder', _t('Admin.FROM_EMPTY'));
        $filter2.removeAttr('placeholder');
      }
      else if (!$filter2.val())
      {
        $filter.removeAttr('placeholder');
        $filter2.attr('placeholder', _t('Admin.TO_EMPTY'));
      }
      else
      {
        $filter.removeAttr('placeholder');
        $filter2.removeAttr('placeholder');
      }
    };
    var onChangeDate = function () {
      updatePlaceholders.apply(this);
      TableAdmin.applyFilters();
    }
    $('.datefilter').datepicker().each(updatePlaceholders).change(onChangeDate).blur(onChangeDate).
       on('changeDate', onChangeDate);
  });
});
