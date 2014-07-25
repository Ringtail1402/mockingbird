/**
 * Javascript for PropelSubformsInput input.
 */
var PropelSubformsInput = {
  templates: {},

  init: function () {
    PropelSubformsInput.templates = {};

    // Look through PropelSubformInputs
    $('.subform-container').each(function () {
      // Find the last, "template" form, store it as raw HTML and delete
      var $container = $(this);
      var $template = $('.subform[data-row=__NEW]', $container);
      PropelSubformsInput.templates[$container.attr('id')] = $template.prop('outerHTML');
      $template.remove();
    });

    // Handle adding new subforms
    $('.subform-add-button').click(function () {
      var $button = $(this);
      var container = $button.attr('data-target-id');
      var form = PropelSubformsInput.templates[container];
      var newid = 'new' + Math.round(Math.random() * 10000);
      form = form.replace(/__NEW/g, newid);
      $('#' + container).append($(form));
      $('.subform[data-row=' + newid + '] script').each(function() { eval($(this).text()); });
      $('.subform[data-row=' + newid + '] .subform-delete-button').click(PropelSubformsInput.deleteButtonHandler);
      $('.subform-empty[data-target-id=' + container + ']').hide();
      $('#' + container).trigger('subform-add');
      return false;
    });

    // Handle deleting new subforms
    $('.subform-delete-button').each(function () { $(this).click(PropelSubformsInput.deleteButtonHandler); });
  },

  deleteButtonHandler: function () {
    var $subform = $('#' + $(this).attr('data-target-id'));
    var container = $subform.attr('data-target-id');
    $subform.remove();
    if (!$('#' + container + ' .subform').length) $('.subform-empty[data-target-id=' + container + ']').show();
    $('#' + container).trigger('subform-delete');
    return false;
  }
};

$(document).ready(PropelSubformsInput.init);

