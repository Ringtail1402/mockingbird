<?php
/**
 * Account type column value template.
 *
 * @var \Mockingbird\Model\Account $object
 */
?>
<?php if ($object->getIsdebt()): ?>
  <?php if ($object->getIscredit()): ?>
    <span style="color: #f00;"><?php echo _t('CREDIT_ACCOUNT') ?></span>
  <?php else: ?>
    <span style="color: #080;"><?php echo _t('DEBIT_ACCOUNT') ?></span>
  <?php endif; ?>
<?php endif; ?>