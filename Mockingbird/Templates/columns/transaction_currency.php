<?php
/**
 * Currency column value template, Transaction-specific
 *
 * @var string $value
 * @var \Mockingbird\Model\Transaction $object
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>
<div style="text-align: right;">
<?php if ($value == '0.00') $value = $object->getVirtualColumn('TotalAmount') ?>
<span class="label label-<?php echo $value >= 0 ? 'success' : 'important' ?>" <?php if (!$object->getAmount()) echo 'style="font-size: 100% !important;"' ?>>
  <?php echo $value >= 0 ? '+' : '&ndash;' ?><?php echo $m->c(abs($value), $object->getCurrency()) ?>
</span>
</div>
