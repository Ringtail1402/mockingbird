/**
 * Feedback JS module.
 */
var Feedback = {
  submitting: false,
  submitted: false,

  showForm: function () {
    Admin.busy();
    Feedback.submitted = false;
    $.get(baseURL + '/feedback', {}, function (data) {
      Admin.ready();
      Admin.modal(_t('Feedback.FEEDBACK'), data.form, { ok: Feedback.submitForm, cancel: null }, { ok: true });
    });
  },

  submitForm: function () {
    if (Feedback.submitting) return;
    if (Feedback.submitted)
    {
      $('#modal').modal('hide');
      return;
    }

    Feedback.submitting = true;
    Admin.busy();
    $.post(baseURL + '/feedback', $('#modal-body').serialize(), function (data) {
      Feedback.submitting = false;
      Admin.ready();
      if (data.message)
      {
        Feedback.submitted = true;
        $('#modal-body').html('<p>' + data.message + '</p>');
        $('#modal-button-cancel').hide();
      }
      else
      {
        $('#modal-body').html(data.form);
      }
      $('#modal-button-ok').one('click', Feedback.submitForm);
    });
  }
};
