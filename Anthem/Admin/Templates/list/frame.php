<?php
/**
 * "Frame" template for ListAdminPage (includes stuff that doesn't get dynamically reloaded).
 *
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\AssetHelpers $asset
 * @var \Anthem\Admin\Admin\TableAdminPage $admin_page
 */
?>

<?php $view->extend('Anthem/Admin:layout.php') ?>
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

<?php // Table view ?>
<div class="table-view">
  <?php // Mass/global links/actions ?>
  <?php $view->beginSlot('global-actions') ?>
    <div class="global-actions table-view">
      <?php // Sorting ?>
      <div class="btn-group" style="display: inline-block; vertical-align: bottom;">
        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
          <?php $columns = $admin_page->getColumnOptions() ?>
          <i class="icon-resize-vertical"></i>
          <span id="sort-column" data-column="<?php echo $admin_page->getSortColumn() ?>"><?php echo $columns[$admin_page->getSortColumn()]['title'] ?></span>
          <span id="sort-asc"<?php if ($admin_page->getSortDir() != 'asc') echo ' style="display: none;"' ?>><?php echo _t('Admin.ASC') ?></span>
          <span id="sort-desc"<?php if ($admin_page->getSortDir() != 'desc') echo ' style="display: none;"' ?>><?php echo _t('Admin.DESC') ?></span>
          <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <?php foreach ($columns as $field => $column): ?>
            <li>
              <a class="sort-link" data-column="<?php echo $field ?>" href="#sort.column=<?php echo $field ?>" onclick="ListAdmin.sortBy('<?php echo $field ?>'); return false;">
                <?php echo isset($column['title']) ? $column['title'] : $field ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <?php foreach($admin_page->getTableLinks() as $name => $table_link): ?>
        <?php echo $view->sub($admin->getTemplate('table_link'), array('name' => $name, 'table_link' => $table_link)) ?>
      <?php endforeach; ?>
      <?php foreach($admin_page->getMassActions() as $name => $mass_action): ?>
        <?php echo $view->sub($admin->getTemplate('mass_action'), array('name' => $name, 'mass_action' => $mass_action)) ?>
      <?php endforeach; ?>
      <?php foreach($admin_page->getTableActions() as $name => $table_action): ?>
        <?php echo $view->sub($admin->getTemplate('table_action'), array('name' => $name, 'table_action' => $table_action)) ?>
      <?php endforeach; ?>
    </div>
  <?php $view->endSlot() ?>

  <?php // Possible extra HTML ?>
  <div class="table-header">
    <?php echo $admin_page->getExtraHtml() ?>
  </div>

  <?php // Filters ?>
  <?php if ($admin_page->hasFilters()): ?>
    <div class="alert alert-info" id="filters-container" style="display: none;">
      <h4><?php echo _t('Admin.SEARCH') ?>:</a></h4>
      <form id="thead-form"><div id="filters"></div></form>
    </div>
  <?php endif; ?>

  <?php // List content placeholder ?>
  <div class="table-body" id="table-container">
    <h4><?php echo _t('Admin.TABLE_LOADING') ?></h4>
  </div>

  <?php $view->beginSlot('footer') ?>
    <div class="table-footer table-view">
      <?php // Pager placeholder ?>
      <div id="pager-container"></div>
    </div>
  <?php $view->endSlotAppend() ?>
</div>

<?php // Form view ?>
<div class="form-view" style="display: none;">
  <form id="form-container"></form>

  <?php // Form links ?>
  <?php $view->beginSlot('footer') ?>
    <div class="form-horizontal">
      <div id="form-links-container" class="form-actions form-view" style="margin-top: -15px; display: none;"></div>
    </div>
  <?php $view->endSlotAppend() ?>
</div>
