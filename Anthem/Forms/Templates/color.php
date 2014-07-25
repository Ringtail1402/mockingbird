<?php
/**
 * Colorpicker input template.
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
<input
  type="text"
  id="<?php echo $id ?>"
  name="<?php echo $name ?>"
  class="<?php echo $class ?><?php if (!$valid) echo ' error' ?> input-mini"
  <?php echo $js_validation_options ?>
  value="<?php $view->endSquash() ?><?php echo htmlspecialchars($value) ?><?php $view->squash() ?>"
  style="color: <?php echo htmlspecialchars($value) ?>; background-color: <?php echo htmlspecialchars($value) ?>; cursor: pointer;"
  readonly
>
<?php if (empty($options['readonly'])): ?>
  <script type="text/javascript">
    $('#<?php echo $id ?>').colorpicker().on('changeColor', function(e) {
      $('#<?php echo $id ?>').css('background-color', e.color.toHex()).css('color', e.color.toHex());
    });
  </script>
<?php endif; ?>
<?php $view->endSquash() ?>