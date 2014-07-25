<?php
/**
 * Category/tag column value template, Transaction-specific.
 *
 * @var \Mockingbird\Model\Transaction $object
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>
<?php if ($object->getCategoryId()): ?>
  <span style="color: <?php echo $object->getCategory()->getColor() ?>">
    <?php echo htmlspecialchars($object->getCategory()->getTitle()) ?>
  </span>
<?php endif; ?>
<i class="icon-tags transaction-tags" data-id="<?php echo $object->getId() ?>"></i>
