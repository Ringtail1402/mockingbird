<?php
/**
 * Mockingbird dashboard year calendar template.
 *
 * @var integer $year
 * @var array $months
 * @var array $totals
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>

<div class="dashboard-calendar-header">
    <i class="icon-chevron-up" id="up-to-all"></i>
    <br>
    <i class="icon-backward" id="back-year"></i>
    <i class="icon-forward" id="forward-year"></i>
    <h3><?php echo $year ?></h3>
    <br>
    <i class="icon-chevron-down" id="down-to-month"></i>
    <br>
    <a id="today"><?php echo _t('THIS_MONTH') ?></a>
</div>

<table class="table table-bordered dashboard-calendar year">
  <tr>
  <?php for ($month = 1; $month <= 12; $month++): ?>
    <td<?php if (!empty($months[$month]['class'])) echo ' class="' . $months[$month]['class'] . '"' ?>>
      <?php if (!empty($months[$month]['content'])) echo $months[$month]['content'] ?>
    </td>
    <?php if ($month == 4 || $month == 8) echo '</tr><tr>' ?>
  <?php endfor; ?>
  </tr>
</table>

<div class="dashboard-calendar-footer">
  <?php echo _t('DASHBOARD_TOTAL_INCOME') ?>
  <?php echo $m->cc($totals['income']) ?>,
  <?php echo _t('DASHBOARD_TOTAL_SPENDING') ?>
  <?php echo $m->cc($totals['spending']) ?>
    <br>
  <?php echo _t('DASHBOARD_TOTAL_AVG_MONTH') ?>
  <?php echo $m->cc($totals['avg']) ?>
</div>