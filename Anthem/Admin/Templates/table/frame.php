<?php
/**
 * "Frame" template for TableAdminPage (includes stuff that doesn't get dynamically reloaded).
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
  <?php foreach ($admin_page->getExtraStylesheets() as $css): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $asset->css($css) ?>">
  <?php endforeach; ?>

  <script type="text/javascript" src="<?php echo $asset->js('Anthem/Admin:table.js') ?>"></script>
  <?php foreach ($admin_page->getExtraScripts() as $script): ?>
    <script type="text/javascript" src="<?php echo $asset->js($script) ?>"></script>
  <?php endforeach; ?>
<?php $view->endSlotAppend() ?>

<?php // Table view ?>
<div class="table-view">
  <?php // Mass/global links/actions ?>
  <?php $view->beginSlot('global-actions') ?>
    <div class="global-actions table-view">
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

  <?php // Actual table.  Headings/filters actually go into separate table, to make the main one scrollable ?>
  <div class="table-body">
    <form id="thead-form" style="margin: 0;">
    <table id="thead" class="table table-bordered table-condensed">
      <?php // Heading ?>
      <thead>
        <tr id="headers">
          <?php if ($admin_page->useMassSelect()): ?>
            <th class="select"></th>
          <?php endif; ?>

          <?php // List columns ?>
          <?php foreach ($admin_page->getColumnOptions() as $field => $column): ?>
            <?php $style = '' ?>
            <?php if (isset($column['width'])) $style .= 'width: ' . $column['width'] . ';'; ?>
            <?php if (isset($column['min-width'])) $style .= 'min-width: ' . $column['min-width'] . ';'; ?>
            <th data-column="<?php echo $field ?>"<?php if ($style) echo ' style="' . $style . '"' ?>>
              <?php if (isset($column['sort']) && $column['sort']): ?>

                <?php // Sort links ?>
                <?php if ($admin_page->getSortColumn() == $field): ?>
                  <a id="active-sort-column" href="#sort.column=<?php echo $field ?>" onclick="TableAdmin.sortBy('<?php echo $field ?>'); return false;">
                    <?php echo isset($column['title']) ? $column['title'] : $field ?>
                    <?php if ($admin_page->getSortDir() == 'asc'): ?>
                      <span id="active-sort-dir-asc">&darr;</span>
                    <?php else: ?>
                      <span id="active-sort-dir-deasc">&uarr;</span>
                    <?php endif; ?>
                  </a>
                <?php else: ?>
                  <a href="#sort.column=<?php echo $field ?>" onclick="TableAdmin.sortBy('<?php echo $field ?>'); return false;">
                    <?php echo isset($column['title']) ? $column['title'] : $field ?>
                    <span></span>
                  </a>
                <?php endif; ?>

              <?php // No sort links ?>
              <?php else: ?>
                <?php echo isset($column['title']) ? $column['title'] : $field ?>
              <?php endif; ?>
            </th>
          <?php endforeach; ?>
          <th<?php if ($admin_page->getActionColumnWidth()) echo ' style="width: ' .$admin_page->getActionColumnWidth() . ';"' ?>>
          </th>
        </tr>

        <?php // Filters placeholder ?>
        <?php if ($admin_page->hasFilters()): ?>
          <tr id="filters" style="display: none;">
          </tr>
        <?php endif; ?>
      </thead>
    </table>
    </form>

    <?php // Table content placeholder ?>
    <div class="table-inner">
      <table id="table" class="table table-bordered table-condensed table-striped">
        <tbody id="table-container">
          <tr><td colspan="<?php echo ($admin_page->useMassSelect() ? 1 : 0) + count($admin_page->getColumnOptions()) + 1 ?>" class="dummy">
            <div class="alert-message block-message warning">
              <?php echo _t('Admin.TABLE_LOADING') ?>
            </div>
          </td></tr>
        </tbody>
      </table>
    </div>
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
