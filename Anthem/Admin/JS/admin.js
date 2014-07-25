/**
 * Generic admin JS.
 */

var Admin = {
  init: function () {
    // Set up error handling
    $(document).ajaxError(function (e, xhr, settings, exception) { if (xhr.statusCode) Admin.error(exception || xhr.responseText) });

    // Mark submenu with an active item as active.  Ideally should do this is template.
    $('.nav .dropdown-menu .active').each(function () { $(this).parents('.dropdown').addClass('active'); });

    // Keep-Alive every three minutes
    setInterval(function () {
      $.get(baseURL + '/ping');
    }, 180000);
  },

  pollNotices: function () {
    var $existing_alerts = $('.main > .alert');
    var existing_ids = [];
    $existing_alerts.each(function () { if ($(this).attr('data-uniqid')) existing_ids.push($(this).attr('data-uniqid')); });
    $.get(baseURL + '/_notify_update', { existing_ids: existing_ids }, function (response) {
      for (var uniqid in response.deleted)
        $('.alert[data-uniqid=' + uniqid.replace(/\./g, '\\.') + ']').removeClass('in').on($.support.transition.end, function () { $(this).remove(); });

      for (var uniqid in response.replace)
        $('.alert[data-uniqid=' + uniqid.replace(/\./g, '\\.') + ']').replaceWith($(response.replace[uniquid]));

      var $prepend_point = $('.main > *:first-child');
      for (var uniqid in response.new)
      {
        $prepend_point.before($(response.new[uniqid]));
        var $new_alert = $('.main > .alert:first-child');
        setTimeout(function () { $new_alert.addClass('in'); }, 200);
      }
      if (response.new.length) $(document).scrollTop(0);
    });
  },

  /**
   * Enters "busy" state (no input possible).
   */
  busy: function ()
  {
    $('#spinner').show();
    $('#disable-layer').css('height', $(document).height()).show();
  },

  /**
   * Leaves "busy" state.
   */
  ready: function ()
  {
    $('#disable-layer').hide();
    $('#spinner').hide();
  },

  /**
   * Display an error message to the user.
   *
   * @param message Optional, default message if shown if not set.
   */
  error: function(message)
  {
    if (typeof message === 'object') message = message.message;
    if (typeof message === 'undefined' || message.trim() == '')
      return;  // message = _t('Admin.DEFAULT_AJAX_ERROR');
    else
      message = _t('Admin.AJAX_ERROR') + message;
    Admin.modal(_t('Admin.ERROR'), message, { ok: true });
    Admin.ready();
  },

  /**
   * Displays a modal dialog.
   *
   * @param header
   * @param text
   * @param buttons  An array of buttons.  ok, yes and no are acceptable keys.
   *   Value can be true, or a callback executed on button click.
   * @param nodismiss  An array of buttons which will not close modal on click.
   *   By default click on any button closes the modal.
   */
  modal: function(header, text, buttons, nodismiss)
  {
    $('#modal-header').html(header);
    $('#modal-body').html(text);
    $('#modal .modal-footer button').hide();
    for (var id in buttons)
    {
      $('#modal-button-' + id).show();
      if (typeof buttons[id] == 'function')
        $('#modal-button-' + id).one('click', buttons[id]);
    }
    $('.modal-footer button').attr('data-dismiss', 'modal');
    for (var id in nodismiss)
      if (nodismiss[id])
        $('#modal-button-' + id).removeAttr('data-dismiss');

    $('#modal').modal();
  },

  /**
   * Helper function, taken from Mootools-More.
   *
   * @param string
   * @param decodeKeys
   * @param decodeValues
   * @return {Object}
   */
  parseQueryString: function(string, decodeKeys, decodeValues){
    if (decodeKeys == null) decodeKeys = true;
    if (decodeValues == null) decodeValues = true;

    var vars = string.split(/[&;]/),
        object = {};
    if (!vars.length) return object;

    for (var i in vars) {
      var val = vars[i];
      var index = val.indexOf('=') + 1,
          value = index ? val.substr(index) : '',
          keys = index ? val.substr(0, index - 1).match(/([^\]\[]+|(\B)(?=\]))/g) : [val],
          obj = object;
      if (!keys) return object;
      if (decodeValues) value = decodeURIComponent(value);
      for (var j in keys)
      {
        var key = keys[j];
        if (decodeKeys) key = decodeURIComponent(key);
        var current = obj[key];

        if (j < keys.length - 1) obj = obj[key] = current || {};
        else if (typeof(current) == 'array') current.push(value);
        else obj[key] = current != null ? [current, value] : value;
      }
    }

    return object;
  }
};

$(document).ready(Admin.init);