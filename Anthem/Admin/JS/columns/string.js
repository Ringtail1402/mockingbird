$(document).ready(function () {
  var saveInput = function($input) {
    $.post(ajaxBaseURL + 'column/update',
      {
        id: $input.attr('data-id'),
        field: $input.attr('data-field'),
        value: $input.val()
      },
      function () {
        $input.hide();
        $('a, .value', $input.getParent()).show();
        $('.value', $input.getParent()).text($input.val());
      }
    );
  };

  // React to in-place input events
  $('.table-body').on('update-table', function () {
    $('.editable_string').keyup(function (e) {
      var $this = $(this);
      // Validate after each keypress
      TableAdmin.edit_validation.validate();

      // Submit on Enter if validation was successful
      if (e.code == 13 && !$this.hasClass('validation-failed'))
      {
        saveInput($this);
      }

      // Cancel edit on Esc
      if (e.code == 27)
      {
        $this.hide();
        $('a, .value', $this.getParent()).show();
        $this.val($('.value', $this.getParent()).text());
      }
    });

    // Submit on lost focus
    $('.editable_string').blur(function (e) {
      saveInput($this);
    });

    // Pop up input on edit button click
    $('.editable_string_edit').click(function (e) {
      var $target = $(e.target);
      if (!$target.is('a')) $target = $target.getParent();
      setTimeout(function () {
        $('a, .value', $target.getParent().getParent()).hide();
        $('.editable-string', $target.getParent().getParent()).show();
      }, 400);
      return false;
    });

    // Clear field on clear button click
    $('.editable_string_clear').click(function (e) {
      var $target = (e.target);
      if (!$target.is('a')) $target = $target.getParent();
      var $input = $('.editable-string', $target.getParent().getParent());
      $.post(ajaxBaseURL + 'column/update',
        {
          id: $input.attr('data-id'),
          field: $input.attr('data-field'),
          value: ''
        },
        function () {
          $('.value', $input.getParent()).text('');
        }
      );
    });
  });
});