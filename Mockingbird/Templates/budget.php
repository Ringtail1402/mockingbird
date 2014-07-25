<?php
/**
 * Mockingbird budget template.
 *
 * @var integer $year
 * @var integer $month
 * @var \Mockingbird\Model\Budget|null $budget
 * @var array $incomes
 * @var array $expenses
 * @var boolean $editable
 * @var \Core\View\ViewHelpers $view
 */
?>

<?php if ($budget): ?>
  <h2><?php echo _t('BUDGET_INCOMES') ?></h2>
  <?php echo $view->sub('Mockingbird:budget_table.php', array('budget' => $budget, 'data' => $incomes, 'income' => true)) ?>
  <h2><?php echo _t('BUDGET_EXPENSES') ?></h2>
  <?php echo $view->sub('Mockingbird:budget_table.php', array('budget' => $budget, 'data' => $expenses, 'income' => false)) ?>
<?php else: ?>
  <h4><span>
    <?php echo _t('BUDGET_NOT_DEFINED') ?>
    <?php if ($editable): ?>
      <?php echo _t('BUDGET_CAN_CREATE') ?>
    <?php endif; ?>
  </span></h4>
<?php endif; ?>
