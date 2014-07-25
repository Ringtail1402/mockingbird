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
<?php if ($use_mass_select): ?>
  <th></th>
<?php endif; ?>

<?php // Filter columns ?>
<?php foreach ($column_options as $name => $params): ?>
  <?php if (isset($params['filter']) && $params['filter']): ?>
    <th><?php echo $columns[$name]->renderFilter(isset($filter_data[$name]) ? $filter_data[$name] : null) ?></th>
  <?php else: ?>
    <th></th>
  <?php endif; ?>
<?php endforeach; ?>

<?php // Filter buttons ?>
<th>
  <button id="filters-apply" class="btn btn-small" onclick="TableAdmin.applyFilters(); return false;">
    <i class="icon-search"></i> <?php echo _t('Admin.SEARCH') ?>
  </button>
  <button id="filters-reset" class="btn btn-small" onclick="TableAdmin.resetFilters(); return false;">
    <i class="icon-refresh"></i> <?php echo _t('Admin.RESET') ?>
  </button>
</th>
