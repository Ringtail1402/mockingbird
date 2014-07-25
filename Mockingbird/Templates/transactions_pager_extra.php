<?php
/**
 * Extra pager field for Transactions table.  Shows sum of all transactions.
 *
 * @var float $sum
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>

<span>
  <?php echo _t('TOTAL') ?>
  <big>&nbsp;<?php echo $m->cc($sum) ?></big>
</span>