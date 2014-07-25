<?php
/**
 * Group input template.  This is used for forms as well as for form tabs etc.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $js_validation_options
 * @var string $value
 * @var string[] $errors
 * @var array $options
 * @var boolean $valid
 * @var \Anthem\Core\View\ViewHelpers $view
 */
?>
<div class="form-horizontal">
<fieldset>
  <?php // Group label ?>
  <?php if (isset($options['label']) && empty($options['no_top_label'])): ?>
    <div class="control-group">
      <label class="control-label">&nbsp;</label>
      <div class="controls">
        <legend class="form-title">
          <?php echo is_callable($options['label']) ? $options['label']() : $options['label'] ?>
        </legend>
      </div>
    </div>
  <?php endif; ?>

  <?php // Iterate through fields ?>
  <?php foreach ($options['fields'] as $name => $field): ?>
    <?php if ($field->isInvisible()): ?>
      <?php echo $field->render() ?>
      <?php continue ?>
    <?php endif; ?>

    <div class="control-group<?php if (count($field->getVisibleErrors())) echo ' error' ?> row-<?php echo $name ?>">

      <?php // Label ?>
      <label class="control-label" for="<?php echo $field->getFullId() ?>">
        <?php if ($field->getOption('label')): ?>
          <?php echo $field->getOption('label') ?>
        <?php else: ?>
          <?php echo $field->getOption('name') ?>
        <?php endif; ?>
      </label>

      <?php // Actual input ?>
      <div class="controls">
        <?php echo $field->render() ?>

        <?php // Single error ?>
        <?php if (count($field->getVisibleErrors()) == 1): ?>
          <span class="help-inline"><?php $errors = $field->getVisibleErrors(); echo array_pop($errors) ?></span>
        <?php endif; ?>

        <?php // Help text.  An input may choose to manage its help by itself. ?>
        <?php if ($field->getOption('help') && !$field->getOption('show_own_help')): ?>
          <span class="help-inline"><?php echo $field->getOption('help') ?></span>
        <?php endif; ?>

        <?php // Multiple errors ?>
        <?php if (count($field->getVisibleErrors()) > 1): ?>
          <ul>
            <?php foreach ($field->getVisibleErrors() as $error): ?>
              <li class="help-inline"><?php echo $error ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <?php // Group help ?>
  <?php if (isset($options['help'])): ?>
    <div class="control-group">
      <label class="control-label">&nbsp;</label>
      <div class="controls">
        <span class="help-inline">
          <?php echo is_callable($options['help']) ? $options['help']() : $options['help'] ?>
        </span>
      </div>
    </div>
  <?php endif; ?>

  <?php // Group error ?>
  <?php if (count($errors)): ?>
    <div class="control-group error">
      <div class="controls">
        <?php if (count($errors) == 1): ?>
          <?php foreach ($errors as $error): ?>
            <li class="help-inline"><?php echo $error ?></li>
          <?php endforeach; ?>
        <?php else: ?>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li class="help-inline"><?php echo $error ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</fieldset>
</div>