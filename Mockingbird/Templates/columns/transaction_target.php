<?php
/**
 * Target column value template, Transaction-specific
 *
 * @var \Mockingbird\Model\Transaction $object
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>
<?php if ($object->getTargetAccountId()): ?>
  <span style="color: <?php echo $object->getVirtualColumn('taColor') ?>">
    <?php echo htmlspecialchars($object->getVirtualColumn('taTitle')) ?>
  </span>
<?php elseif ($object->getVirtualColumn('cpTitle')): ?>
  <?php echo htmlspecialchars($object->getVirtualColumn('cpTitle')) ?>
<?php else: ?>
  ??
<?php endif; ?>
