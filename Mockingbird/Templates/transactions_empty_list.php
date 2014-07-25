<?php if ($filtered): ?>
  <h3><?php echo $message ?></h3>
<?php else: ?>
  <h3><?php echo _t('TRANSACTION_TABLE_EMPTY1') ?></h3>
  <h4><?php echo _t('TRANSACTION_TABLE_EMPTY2') ?></h4>
<?php endif; ?>