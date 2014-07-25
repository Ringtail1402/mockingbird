<?php
/**
 * Datepicker input template.  Trivial.
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
<span class="datetime-field">
  <input
    type="text"
    id="<?php echo $id ?>_date"
    name="<?php echo $name ?>[date]"
    class="<?php echo $class ?><?php if (!$valid) echo ' error' ?>"
    <?php echo $js_validation_options ?>
    value="<?php $view->endSquash() ?><?php echo htmlspecialchars($value['date']) ?><?php $view->squash() ?>"
    autocomplete="off"
    <?php if (!empty($options['readonly'])): ?>
      readonly disabled
    <?php endif; ?>
  >
  <?php if (empty($options['readonly'])): ?>
    <script type="text/javascript">
      $('#<?php echo $id ?>_date').datepicker();
    </script>
  <?php endif; ?>
  <?php if (!isset($options['with_time']) || $options['with_time']): ?>
    <select
      id="<?php echo $id ?>_hours"
      name="<?php echo $name ?>[hours]"
      class="<?php if (!$valid) echo 'error' ?>"
      <?php if (!empty($options['readonly'])): ?>
        readonly disabled
      <?php endif; ?>
    >
      <option value="0"></option>
      <?php for ($i = 0; $i < 24; $i++): ?>
        <option value="<?php echo $i ?>"<?php if ($value['hours'] == $i) echo ' selected' ?>>
          <?php echo sprintf('%02d', $i) ?>
        </option>
      <?php endfor; ?>
    </select>
    :
    <select
      id="<?php echo $id ?>_minutes"
      name="<?php echo $name ?>[minutes]"
      class="<?php if (!$valid) echo 'error' ?>"
      <?php if (!empty($options['readonly'])): ?>
        readonly disabled
      <?php endif; ?>
     >
      <option value="0"></option>
      <?php for ($i = 0; $i < 60; $i++): ?>
        <option value="<?php echo $i ?>"<?php if ($value['minutes'] == $i) echo ' selected' ?>>
          <?php echo sprintf('%02d', $i) ?>
        </option>
      <?php endfor; ?>
    </select>
  <?php endif; ?>
</span>
<?php $view->endSquash() ?>