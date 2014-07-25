<?php
/**
 * "Frame" template for TableAdminPage, print version.
 *
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\AssetHelpers $asset
 * @var \Anthem\Admin\Admin\TableAdminPage $admin_page
 */
?>

<?php $view->extend('Anthem/Admin:layout_print.php') ?>
<?php $view->setSlot('title', $admin_page->getTitle()) ?>
<?php $view->setSlot('subtitle', $admin_page->getSubtitle()) ?>

<?php // CSS and Javascript includes ?>
<?php $view->beginSlot('head') ?>
<link type="text/css" rel="stylesheet" href="<?php echo $asset->css('Anthem/Admin:table.css') ?>"
      xmlns="http://www.w3.org/1999/html">
<?php foreach ($admin_page->getExtraStylesheets() as $css): ?>
<link type="text/css" rel="stylesheet" href="<?php echo $asset->css($css) ?>">
<?php endforeach; ?>

<script type="text/javascript" src="<?php echo $asset->js('Anthem/Admin:table.js') ?>"></script>
<?php foreach ($admin_page->getExtraScripts() as $script): ?>
<script type="text/javascript" src="<?php echo $asset->js($script) ?>"></script>
<?php endforeach; ?>
<?php $view->endSlotAppend() ?>

<div class="container-fluid" id="limit-results" data-limit="<?php echo $admin_page->getMaxPrintEntries() ?>" style="display: none;">
  <p><?php echo sprintf(_t('Admin.TOO_MANY_ENTRIES', $admin_page->getMaxPrintEntries())) ?></p>
</div>

<?php // Table view ?>
<div class="table-view">
  <?php // Possible extra HTML ?>
    <div class="table-header">
      <?php echo $admin_page->getExtraHtml() ?>
    </div>

  <?php // Actual table.  Headings/filters actually go into separate table, to make the main one scrollable ?>
  <div class="table-inner" style="display: none;"></div>
  <div class="table-body">
    <table id="table" class="table table-bordered table-condensed table-striped">
      <?php // Heading ?>
      <thead>
        <tr id="headers">
          <?php // List columns ?>
          <?php foreach ($admin_page->getColumnOptions() as $field => $column): ?>
            <th data-column="<?php echo $field ?>"<?php if (isset($column['width'])) echo ' style="width: ' . $column['width'] . ';"' ?>>
              <?php echo isset($column['title']) ? $column['title'] : $field ?>
              <?php if (isset($column['sort']) && $column['sort']): ?>
                <?php // Sort links ?>
                <?php if ($admin_page->getSortColumn() == $field): ?>
                  <?php if ($admin_page->getSortDir() == 'asc'): ?>
                    <span id="active-sort-dir-asc">&darr;</span>
                  <?php else: ?>
                    <span id="active-sort-dir-deasc">&uarr;</span>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>
            </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody id="table-container">
      <tr><td colspan="<?php echo count($admin_page->getColumnOptions()) ?>" class="dummy">
        <div class="alert-message block-message warning">
          <?php echo _t('Admin.TABLE_LOADING') ?>
        </div>
      </td></tr>
      </tbody>
    </table>
  </div>
</div>
