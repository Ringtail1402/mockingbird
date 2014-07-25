$(document).ready(function () {
  $('.table-body').on('update-table', function () {
    // Initialize boolean filter
    $('.boolean-filter').each(function() {
      var value = $('input[type=hidden][data-id=' + $(this).attr('data-id') + ']').val();
      if (value == 'on')
        $(this).attr('checked', 'checked').prop('indeterminate', false);
      else if (value == 'off')
        $(this).removeAttr('checked').prop('indeterminate', false);
      else
        $(this).removeAttr('checked').prop('indeterminate', true);
    }).click(function () {
      var $value = $('input[type=hidden][data-id=' + $(this).attr('data-id') + ']'), value = $value.val();
      if (value == 'on')
      {
        $value.val('off');
        $(this).removeAttr('checked').prop('indeterminate', false);
      }
      else if (value == 'off')
      {
        $value.val('');
        $(this).removeAttr('checked').prop('indeterminate', true);
      }
      else
      {
        $value.val('on');
        $(this).attr('checked', 'checked').prop('indeterminate', false);
      }
      TableAdmin.applyFilters();
    });
  });
});
