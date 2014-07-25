<?php
/**
 * Filters row (<tr id="filters">...</tr>).  The entire row is optional.
 *
 * @var boolean $use_mass_select
 * @var array $column_options
 * @var \Anthem\Admin\Admin\TableColumn\BaseColumn[] $columns
 * @var array $filter_data
 */
?>
<?php // Filter columns ?>
<dl class="dl-horizontal">
  <?php foreach ($column_options as $name => $params): ?>
    <?php if (isset($params['filter']) && $params['filter']): ?>
      <dt><?php echo isset($params) ? $params['title'] : $name ?>:</dt>
      <dd class="list-filter form-inline"><?php echo $columns[$name]->renderFilter(isset($filter_data[$name]) ? $filter_data[$name] : null, 'inline') ?></dd>
    <?php endif; ?>
<?php endforeach; ?>
</dl>

<?php // Filter buttons ?>
<div>
  <button id="filters-apply" class="btn btn-small" onclick="TableAdmin.applyFilters(); return false;">
      <i class="icon-search"></i> <?php echo _t('Admin.SEARCH') ?>
  </button>
  <button id="filters-reset" class="btn btn-small" onclick="TableAdmin.resetFilters(); return false;">
      <i class="icon-refresh"></i> <?php echo _t('Admin.RESET') ?>
  </button>
</div>
