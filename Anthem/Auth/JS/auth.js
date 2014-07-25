/**
 * Auth javascript.  This relies on login-link exiting, login-form container existing and hidden, login-spinner
 * optionally in login form, and login-button in login form.
 */
var Auth = {
  init: function () {
    var $login_link = $('#login-link'), $login_form = $('#login-form');
    if ($login_link.length)
    {
      $login_link.click(function () {
        if ($login_form.hasClass('in'))
        {
          $login_form.removeClass('in');
          $.support.transition ?
              $login_form.one($.support.transition.end, function () { $login_form.hide();  }) :
              $login_form.hide();
        }
        else
        {
          $login_form.show();
          $('body')[0].offsetWidth;
          $login_form.addClass('in');
        }
      });
      $.get($('#ajax-login-form-url').val(), {}, function (data) {
        $login_form.html(data.form);
        if (data.already_logged_on)
        {
          if (data.https_login)
            window.location.href = "https:" + window.location.href.substring(window.location.protocol.length);
          else
            window.location.reload();
        }
      }, 'jsonp');

      $(document).on('keyup', '#login-form input', function(e) {
        if (e.keyCode == 13) $('#login-button-click');
      });

      $(document).on('click', '#login-button', function () {
        $('#login-spinner').show();
        $.get($('#ajax-login-action').val(), $login_form.serialize(), function (data) {
          $('#login-spinner').hide();
          if (data.valid || data.already_logged_on)
          {
            if (data.https_login)
              window.location.href = "https:" + window.location.href.substring(window.location.protocol.length);
            else
              window.location.reload();
          }
          else
          {
            $login_form.html(data.form);
            var $email    = $('.row-email input',    $login_form);
            var $password = $('.row-password input', $login_form);
            if ($email.val())
              $password.focus();
            else
              $email.focus();
          }
          return false;
        }, 'jsonp')
      });
    }
  }
};

$(document).ready(Auth.init);
