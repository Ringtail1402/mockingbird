<?php
/**
 * Pager block template.
 *
 * @var integer $page
 * @var integer $first_record
 * @var integer $last_record
 * @var integer $total_records
 * @var integer $total_filtered_records
 * @var integer $total_pages
 * @var integer $per_page
 * @var integer[] $per_page_options
 * @var string $extra_content
 * @var \Anthem\Admin\View\AdminHelpers $admin
 */
?>

<div class="pagination">
  <ul>
    <li><a href="#top" onclick="if ($('.table-inner').length) $('.table-inner')[0].scrollTop = 0; else window.scrollTo(0, 0); return false;"><?php echo _t('Admin.TOP') ?></a></li>

    <?php // Stats ?>
    <li><span>
      <?php if ($total_records == $total_filtered_records): ?>
        <?php echo _t('Admin.PAGER', $page, $total_pages, $first_record, $last_record, $total_records) ?>
      <?php else: ?>
        <?php echo _t('Admin.PAGER_FILTERED', $page, $total_pages,
                      $first_record, $last_record, $total_filtered_records, $total_records) ?>
      <?php endif; ?>
    </span></li>

    <?php // Per page records number selector ?>
    <li class="per-page"><span>
      <select id="per-page" onchange="TableAdmin.setPerPage(this.value);">
        <?php foreach ($per_page_options as $_per_page): ?>
          <option value="<?php echo $_per_page ?>"<?php if ($_per_page == $per_page) echo ' selected' ?>><?php echo $_per_page ?>&nbsp;</option>
        <?php endforeach ?>
      </select>
      <?php echo _t('Admin.PER_PAGE') ?>
    </span></li>

    <?php if ($total_pages > 1): ?>
      <?php // Previous page ?>
      <?php if ($page > 1): ?>
        <li class="prev">
          <a href="#page=<?php echo $page - 1 ?>" onclick="TableAdmin.loadPage(<?php echo $page - 1 ?>); return false;">
            <?php echo _t('Admin.PREV_PAGE') ?>
          </a>
        </li>
      <?php else: ?>
        <li class="prev disabled">
          <a href="javascript:void(0)">
            <?php echo _t('Admin.PREV_PAGE') ?>
          </a>
        </li>
      <?php endif; ?>

      <?php // Some numbered pages ?>
      <?php foreach ($admin->getPagerLinks($page, $total_pages) as $_page): ?>
        <?php if ($_page && $_page == $page): ?>
          <li class="active">
            <a href="javascript:void(0)"><?php echo $_page ?></a>
          </li>
        <?php elseif ($_page): ?>
          <li>
            <a href="#page=<?php echo $_page ?>" onclick="TableAdmin.loadPage(<?php echo $_page ?>); return false;"><?php echo $_page ?></a>
          </li>
        <?php else: ?>
          <li class="disabled">
            <a href="javascript:void(0)">…</a>
          </li>
        <?php endif; ?>
      <?php endforeach; ?>

      <?php // Next page ?>
      <?php if ($page < $total_pages): ?>
        <li class="next">
          <a href="#page=<?php echo $page + 1 ?>" onclick="TableAdmin.loadPage(<?php echo $page + 1 ?>); return false;">
            <?php echo _t('Admin.NEXT_PAGE') ?>
          </a>
        </li>
      <?php else: ?>
        <li class="next disabled">
          <a href="javascript:void(0)">
            <?php echo _t('Admin.NEXT_PAGE') ?>
          </a>
        </li>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($extra_content): ?>
      <li><?php echo $extra_content ?></li>
    <?php endif; ?>
  </ul>
</div>
