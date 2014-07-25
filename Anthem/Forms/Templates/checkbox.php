<?php
/**
 * Checkbox input template.  Trivial.
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
<?php $view->squash() ?>
<label class="checkbox inline">
  <input
    type="checkbox"
    id="<?php echo $id ?>"
    name="<?php echo $name ?>"
    class="<?php echo $class ?><?php if (!$valid) echo ' error' ?>"
    <?php echo $js_validation_options ?>
    <?php if ($value) echo 'checked' ?>
    <?php if (isset($options['readonly']) && $options['readonly']): ?>
      readonly disabled
    <?php endif; ?>
  >
  <?php if (isset($options['help'])): ?>
    <span class="help-inline"><?php echo $options['help'] ?></span>
  <?php endif; ?>
</label>
<?php $view->endSquash() ?>
