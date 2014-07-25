<?php
/**
 * Subforms input template.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $js_validation_options
 * @var string $value
 * @var array $options
 * @var boolean $valid
 * @var \Core\View\ViewHelpers $view
 */
?>
<div id="<?php echo $id ?>_empty" class="alert subform-empty" data-target-id="<?php echo $id ?>"<?php if (count($options['fields']) > 1) echo ' style="display: none;"' ?>>
  <?php echo empty($options['empty_message']) ? _t('Forms.NO_SUBFORMS') : $options['empty_message'] ?>
</div>
<div id="<?php echo $id ?>" class="accordion subform-container">
  <?php foreach ($options['fields'] as $_name => $subform): ?>
    <div class="accordion-group subform" id="<?php echo $id ?>_row<?php echo $_name ?>" data-row="<?php echo $_name ?>" data-target-id="<?php echo $id ?>">
      <div class="accordion-heading">
        <?php if (empty($options['readonly'])): ?>
          <button class="btn btn-danger pull-right subform-delete-button" data-target-id="<?php echo $id ?>_row<?php echo $_name ?>"
                  style="margin-right: 3px; margin-top: 3px;"><i class="icon-remove icon-white"></i> <?php echo _t('Forms.DELETE') ?></button>
        <?php endif; ?>
        <a class="accordion-toggle" data-toggle="collapse" data-target="#subform-collapse-<?php echo $_name ?>" style="cursor: pointer;">
          <?php $label = $subform->getOption('label') ?>
          <?php echo is_callable($label) ? $label() : $label ?>
        </a>
      </div>
      <div class="accordion-body collapse <?php if ($_name == '__NEW' || count($subform->getVisibleErrors())) echo 'in' ?>" id="subform-collapse-<?php echo $_name ?>">
        <div class="accordion-inner">
          <?php echo $subform->render() ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php if (empty($options['readonly'])): ?>
  <button class="btn subform-add-button" data-target-id="<?php echo $id ?>"><i class="icon-plus"></i> <?php echo _t('Forms.ADD') ?></button>
<?php endif; ?>