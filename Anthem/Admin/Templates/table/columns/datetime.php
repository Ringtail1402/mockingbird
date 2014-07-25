<?php
/**
 * Date/time column value template.
 *
 * @var string $value
 */
?>
<?php if ($url): ?>
  <a class="value" <?php echo $url ?>>
<?php endif; ?>
<?php $value = strtotime($value); if ($value) echo strftime('%x %R', $value) ?>
<?php if ($url): ?>
  </a>
<?php endif; ?>