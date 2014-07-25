<?php
/**
 * Boolean column value template.
 *
 * @var boolean $value
 */
?>
<?php if (!empty($options['invert_value'])) $value = !$value ?>
<?php if (empty($options['false_empty']) || $value): ?>
  <i class="icon-<?php echo $value ? 'ok' : 'remove' ?>"></i>
<?php endif; ?>