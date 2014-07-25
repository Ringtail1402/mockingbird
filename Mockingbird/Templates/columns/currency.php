<?php
/**
 * Currency column value template.
 *
 * @var string $value
 * @var \Mockingbird\Model\Transaction $object
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>
<?php if ($value): ?>
<div style="text-align: right;">
  <?php echo $m->cc($value, method_exists($object, 'getCurrency') ? $object->getCurrency() : $app['mockingbird.model.currency']->getDefaultCurrency()) ?>
</div>
<?php endif; ?>