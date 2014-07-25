<?php
/**
 * Mockingbird dashboard entire calendar template.
 *
 * @var array $years
 * @var array $totals
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>

<div class="dashboard-calendar-header">
    <h3><?php echo _t('ALL_TIME') ?></h3>
    <br>
    <i class="icon-chevron-down" id="down-to-year"></i>
    <br>
    <a id="today"><?php echo _t('THIS_YEAR') ?></a>
</div>

<table class="table table-bordered dashboard-calendar all">
  <?php foreach ($years as $year => $data): ?>
    <tr>
      <td<?php if (!empty($data['class'])) echo ' class="' . $data['class'] . '"' ?>>
        <?php if (!empty($data['content'])) echo $data['content'] ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<div class="dashboard-calendar-footer">
  <?php echo _t('DASHBOARD_TOTAL_INCOME') ?>
  <?php echo $m->cc($totals['income']) ?>,
  <?php echo _t('DASHBOARD_TOTAL_SPENDING') ?>
  <?php echo $m->cc($totals['spending']) ?>
    <br>
  <?php echo _t('DASHBOARD_TOTAL_AVG_YEAR') ?>
  <?php echo $m->cc($totals['avg']) ?>
</div>