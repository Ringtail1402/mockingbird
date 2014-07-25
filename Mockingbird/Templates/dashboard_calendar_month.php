<?php
/**
 * Mockingbird dashboard month calendar template.
 *
 * @var integer $year
 * @var integer $month
 * @var array $days
 * @var array $totals
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>

<div class="dashboard-calendar-header">
  <i class="icon-chevron-up" id="up-to-year"></i>
  <br>
  <i class="icon-backward" id="back-month"></i>
  <i class="icon-forward" id="forward-month"></i>
  <h3><?php echo strftime('%B %Y', mktime(0, 0, 0, $month, 1, $year)) ?></h3>
  <br>
  <a id="today"><?php echo _t('TODAY') ?></a>
</div>

<table class="table table-bordered dashboard-calendar month">
  <?php echo $m->calendar($year, $month, $days) ?>
</table>

<div class="dashboard-calendar-footer">
  <?php echo _t('DASHBOARD_TOTAL_INCOME') ?>
  <?php echo $m->cc($totals['income']) ?>,
  <?php echo _t('DASHBOARD_TOTAL_SPENDING') ?>
  <?php echo $m->cc($totals['spending']) ?>
  <br>
  <?php echo _t('DASHBOARD_TOTAL_AVG_DAY', $m->c($app['settings']->get('mockingbird.day_average_limit'))) ?>
  <?php echo $m->cc($totals['avg']) ?>
</div>