<?php
/**
 * Transaction title column value template.
 *
 * @var string $value
 * @var \Mockingbird\Model\Transaction $object
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>
<?php if ($object->getParentTransactionId()): ?>
  <a href="#id=<?php echo $object->getParentTransactionId() ?>" onclick="TableAdmin.edit(<?php echo $object->getParentTransactionId() ?>); return false;"><?php echo htmlspecialchars($object->getParentTransaction()->getTitle()) ?></a>
  <i class="icon-chevron-right"></i>
  <?php echo htmlspecialchars($value) ?>
<?php else: ?>
  <?php // Only one of a pair of transactions in a transfer should be directly edited ?>
  <?php $id = $object->getId(); if ($object->getCounterTransactionId() && $object->getAmount() > 0) $id = $object->getCounterTransactionId() ?>
  <a href="#id=<?php echo $id ?>" onclick="TableAdmin.edit(<?php echo $id ?>); return false;"><?php echo htmlspecialchars($value) ?></a>
  <?php if ($object->getAmount() == '0.00'): ?>
    <i class="icon-chevron-down transactions-toggle" data-id="<?php echo $object->getId() ?>"></i>
  <?php endif; ?>
<?php endif; ?>