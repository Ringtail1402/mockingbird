<div style="position: relative; text-align: right;">
<?php
/**
 * Integer column value template.
 *
 * @var string $value
 * @var string $url
 */
?>
<?php if ($url): ?>
  <a class="value" <?php echo $url ?>>
<?php else: ?>
  <span class="value">
<?php endif; ?>
  <?php echo sprintf(empty($options['format']) ? '%d' : $options['format'], $value) ?>
<?php if ($url): ?>
  </a>
<?php else: ?>
  </span>
<?php endif; ?>

<?php if (isset($options['edit']) && $options['edit']): ?>
  <input style="width: 80%; display: none; margin: 0;" class="editable_string" data-field="<?php echo $options['field'] ?>" data-id="<?php echo $object->getPrimaryKey() ?>" value="<?php echo htmlspecialchars($value) ?>"
    data-validators="validate-integer
    <?php if (!isset($options['allow_empty']) || !$options['allow_empty']): ?>
      required
    <?php endif; ?>
    "
  >
  <span class="onhover">
    <a href="#edit" class="editable_string_edit"><i class="icon-pencil" title="<?php echo _t('Admin.EDIT') ?>"></i></a>
    <?php if (isset($options['allow_empty']) && $options['allow_empty']): ?>
      <a href="#clear" class="editable_string_clear"><i class="icon-remove" title="<?php echo _t('Admin.CLEAR') ?>"></i></a>
    <?php endif; ?>
  </span>
<?php endif; ?>
</div>