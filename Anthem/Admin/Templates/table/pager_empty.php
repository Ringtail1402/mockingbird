<?php
/**
 * Pager block template used in case no records are available for display.
 *
 * @var integer $total_records
 * @var integer $total_filtered_records
 * @var string $extra_content
 */
?>

<div class="pagination">
  <ul>
    <?php // Stats ?>
    <li><span>
      <?php if ($total_records == $total_filtered_records): ?>
        <?php echo _t('Admin.PAGER_EMPTY') ?>
      <?php else: ?>
        <?php echo _t('Admin.PAGER_FILTERED_EMPTY', $total_records) ?>
      <?php endif; ?>
    </span></li>

    <?php if ($extra_content): ?>
      <li><?php echo $extra_content ?></li>
    <?php endif; ?>
  </ul>
</div>
