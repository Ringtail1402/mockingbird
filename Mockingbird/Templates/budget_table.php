<?php
/**
 * Mockingbird budget table template (incomes or expenses).
 *
 * @var \Mockingbird\Model\Budget $budget
 * @var array $data
 * @var boolean $income
 * @var \Core\View\ViewHelpers $view
 */
?>

<?php if (count($data)): ?>
  <table class="table table-bordered budget-table">
    <thead>
      <th><?php echo _t('BUDGET_CATEGORY') ?></th>
      <th><?php echo _t('BUDGET_DATE') ?></th>
      <th><?php echo _t('BUDGET_ESTIMATED_TOTAL') ?></th>
      <th><?php echo _t('BUDGET_ESTIMATED_CURRENT') ?></th>
      <th><?php echo _t('BUDGET_ACTUAL') ?></th>
      <th><?php echo _t('BUDGET_PERCENT') ?></th>
    </thead>
    <tbody>
      <?php foreach ($data as $title => $values): ?>
        <tr<?php if ($title == '*' || !$title) echo ' class="total"' ?>>
          <?php echo $view->sub('Mockingbird:budget_row.php', array('budget' => $budget, 'title' => $title, 'values' => $values, 'income' => $income)) ?>
        </tr>
        <?php foreach ($values['entries'] as $_values): ?>
          <tr class="subentry">
            <?php echo $view->sub('Mockingbird:budget_row.php', array('budget' => $budget, 'values' => $_values, 'income' => $income)) ?>
          </tr>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <h4><?php echo _t('BUDGET_NO_DATA') ?></h4>
<?php endif; ?>