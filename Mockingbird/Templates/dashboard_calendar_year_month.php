<?php
/**
 * A single cell in a calendar table.
 *
 * @var integer $year
 * @var integer $month
 * @var float $income
 * @var float $spending
 * @var \Mockingbird\View\MockingbirdHelpers $m
 * @var \Core\View\ViewHelpers $view
 * @var \Core\View\LinkHelpers $link
 */
?>
<?php $date = new \DateTime("{$year}-{$month}-01 00:00:00") ?>
<?php $date->add(new \DateInterval('P1M'))->sub(new \DateInterval('PT1S')) ?>
<?php $day = $date->format('d') ?>

<a class="dashboard-link" onclick="Dashboard.downToMonth(<?php echo $month ?>);">
  <?php echo strftime('%B', $date->format('U')) ?>
</a>

<?php $view->squash() ?>
<div style="text-align: right; font-size: 20px;">
  <?php $url = $link->url('transactions') .
    '#filter.created_at.from=' . date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)) .
    '&filter.created_at.to=' . date('Y-m-d', mktime(0, 0, 0, $month, $day, $year)) ?>
  <?php if (!$income && !$spending) echo '<br><br>' ?>
  <?php if ($income xor $spending) echo '<br>' ?>
  <?php if ($income): ?><?php echo $m->cc($income, null, $url) ?><?php endif; ?><?php if ($income && $spending) echo '<br>' ?>
  <?php if ($spending): ?><?php echo $m->cc(-$spending, null, $url) ?><?php endif; ?>
</div>
<?php $view->endSquash() ?>