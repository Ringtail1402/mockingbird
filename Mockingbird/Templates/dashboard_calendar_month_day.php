<?php
/**
 * A single cell in a calendar table.
 *
 * @var integer $year
 * @var integer $month
 * @var integer $day
 * @var float $income
 * @var float $spending
 * @var \Mockingbird\View\MockingbirdHelpers $m
 * @var \Core\View\ViewHelpers $view
 * @var \Core\View\LinkHelpers $link
 */
?>

<a class="dashboard-link" onclick="Dashboard.setDay(<?php echo $year ?>, <?php echo $month ?>, <?php echo $day ?>);">
  <?php echo $day ?>
</a>

<?php $view->squash() ?>
<div style="text-align: right; font-size: 15px;">
  <?php $url = $link->url('transactions') .
                    '#filter.created_at.from=' . date('Y-m-d', mktime(0, 0, 0, $month, $day, $year)) .
                    '&filter.created_at.to=' . date('Y-m-d', mktime(0, 0, 0, $month, $day, $year)) ?>
  <?php if (!$income && !$spending) echo '<br><br>' ?>
  <?php if ($income xor $spending) echo '<br>' ?>
  <?php if ($income): ?><?php echo $m->cc($income, null, $url) ?><?php endif; ?><?php if ($income && $spending) echo '<br>' ?>
  <?php if ($spending): ?><?php echo $m->cc(-$spending, null, $url) ?><?php endif; ?>
</div>
<?php $view->endSquash() ?>