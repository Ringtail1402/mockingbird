<?php
/**
 * <select> input template.
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
<select
  id="<?php echo $id ?>"
  name="<?php echo $name ?>"
  class="<?php echo $class ?><?php if (!$valid) echo ' error' ?>"
  <?php echo $js_validation_options ?>
  <?php if (isset($options['readonly']) && $options['readonly']): ?>
    readonly disabled
  <?php endif; ?>
>
  <?php if (!empty($options['add_empty'])): ?>
    <option value=""><?php if (is_string($options['add_empty'])) echo htmlspecialchars($option['add_empty']) ?></option>
  <?php endif; ?>
  <?php foreach ($options['values'] as $_value => $text): ?>
    <?php if (is_array($text)): ?>
      <optgroup label="<?php echo htmlspecialchars($_value) ?>">
        <?php foreach ($text as $_value => $text): ?>
          <option <?php if (!empty($options['option_attrs'][$_value])) echo $options['option_attrs'][$_value] ?>
                  value="<?php echo htmlspecialchars($_value) ?>"<?php if ($_value == $value) echo ' selected' ?>>
            <?php echo htmlspecialchars($text) ?>
          </option>
        <?php endforeach; ?>
      </optgroup>
    <?php else: ?>
      <option <?php if (!empty($options['option_attrs'][$_value])) echo $options['option_attrs'][$_value] ?>
              value="<?php echo htmlspecialchars($_value) ?>"<?php if ($_value == $value) echo ' selected' ?>>
        <?php echo htmlspecialchars($text) ?>
      </option>
    <?php endif; ?>
  <?php endforeach; ?>
</select>
<?php $view->endSquash() ?>