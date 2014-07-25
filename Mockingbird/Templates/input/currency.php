<?php
/**
 * Currency input template.
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
<?php $format = explode('#', $options['currency']->getFormat()) ?>
<div id="<?php echo $id ?>_container" class="currency-container <?php if ($format[0]) echo 'input-prepend '; if ($format[1]) echo 'input-append' ?>">
  <span class="add-on pre" id="<?php echo $id ?>_pre"<?php if (!$format[0]) echo ' style="display: none;"' ?>>
    <?php echo htmlspecialchars($format[0]) ?>
  </span>
  <input
    type="text"
    id="<?php echo $id ?>"
    name="<?php echo $name ?>"
    class="<?php echo $class ?><?php if (!$valid) echo ' error' ?>"
    <?php echo $js_validation_options ?>
    value="<?php echo htmlspecialchars($value) ?>"
    <?php if (isset($options['readonly']) && $options['readonly']): ?>
      readonly
    <?php endif; ?>
    data-currency="<?php echo htmlspecialchars($options['currency']->getTitle()) ?>"
    data-rate="<?php echo $options['currency']->getRateToPrimary() ?>"
    autocomplete="off"
  >
  <span class="add-on post" id="<?php echo $id ?>_post"<?php if (!$format[1]) echo ' style="display: none;"' ?>>
    <?php echo htmlspecialchars($format[1]) ?>
  </span>
</div>
<?php $view->endSquash() ?>