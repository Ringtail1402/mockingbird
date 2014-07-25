<?php
/**
 * "Frame" template for ListAdminPage, print version.
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
<link type="text/css" rel="stylesheet" href="<?php echo $asset->css('Anthem/Admin:table.css') ?>">
<link type="text/css" rel="stylesheet" href="<?php echo $asset->css('Anthem/Admin:list.css') ?>">
<?php foreach ($admin_page->getExtraStylesheets() as $css): ?>
<link type="text/css" rel="stylesheet" href="<?php echo $asset->css($css) ?>">
<?php endforeach; ?>

<script type="text/javascript" src="<?php echo $asset->js('Anthem/Admin:table.js') ?>"></script>
<script type="text/javascript" src="<?php echo $asset->js('Anthem/Admin:list.js') ?>"></script>
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

  <?php // List content placeholder ?>
  <div class="table-body" id="table-container">
      <h4><?php echo _t('Admin.TABLE_LOADING') ?></h4>
  </div>
</div>
