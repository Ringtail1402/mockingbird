var IEWarning = {
  show: function () {
    if (document.cookie.indexOf('iewarning_dismissed=1') != -1) return;
    $.get(baseURL + '/_iewarning', {}, function (response) {
      $('#disable-layer').show();
      $('body').append($(response));
      var date = new Date();
      date.setFullYear(date.getFullYear() + 1);
      document.cookie = 'iewarning_dismissed=1; expires=' + date.toUTCString() + '; path=/';
      $('#iemodal .close, #iemodal .btn').click(function () {
        $('#iemodal').removeClass('in');
        $('#disable-layer').hide();
      });
    });
  }
};

IEWarning.show();