<?php
/**
 * Many-to-many input template.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $js_validation_options
 * @var string $value
 * @var array $options
 * @var boolean $valid
 * @var \Anthem\Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<?php foreach ($options['target_objects'] as $i => $target): ?>
  <label class="checkbox inline">
    <input type="checkbox"
           id="<?php echo $id ?>_<?php echo $target->getPrimaryKey() ?>"
           name="<?php echo $name ?>[<?php echo $target->getPrimaryKey() ?>]"
           class="<?php echo $class ?><?php if (!$valid) echo ' error' ?>"
           <?php echo $js_validation_options ?>
           <?php if (!empty($value[$target->getPrimaryKey()])) echo 'checked' ?>
           <?php if (isset($options['readonly']) && $options['readonly']): ?>
             readonly disabled
           <?php endif; ?>
           >
    <?php echo htmlspecialchars($target) ?>
  </label>
<?php endforeach; ?>
<?php if (!count($options['target_objects'])): ?>
  <?php echo !empty($options['empty_message']) ? $options['empty_message'] : _t('Forms.NO_MANY_TO_MANY') ?>
<?php endif; ?>
<?php $view->endSquash() ?>
<?php if (isset($options['help'])): ?>
  <label class="inline"><span class="help-inline"><?php echo $options['help'] ?></span></label>
<?php endif; ?>
