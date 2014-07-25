<?php
/**
 * Actual table grid template.
 *
 * @var object[] $records
 * @var \Anthem\Admin\Admin\TableColumn\BaseColumn[] $columns
 * @var array $column_options
 * @var boolean $use_mass_select
 * @var callable $test_mass_selector
 * @var array[string] $links
 * @var array[string] $external_links
 * @var array[string] $actions
 * @var string $alt_model Alternate model class.  This is not used by TableAdminPage itself but used e.g. by
 *             MediaLibraryAdmin.  Records of this class will have their ids for mass actions set to negative.
 * @var string $row_class Optional row class.
 * @var boolean $print Print mode (disables checkbox and action columns)
 * @var \Anthem\Core\View\ViewHelpers $view
 */
?>

<?php foreach ($records as $i => $record): ?>
  <tr data-id="<?php echo $record->getPrimaryKey() ?>"<?php if (!empty($row_class)) echo ' class="' . $row_class . '"' ?>>

    <?php // Checkbox column ?>
    <?php if ($use_mass_select && !$print): ?>
      <td class="select">
        <?php if (!$test_mass_selector || $test_mass_selector($record)): ?>
          <?php $view->squash() ?>
          <input type="checkbox"
                 class="mass-selector"
                 data-id="<?php if (isset($alt_model) && get_class($record) == $alt_model) echo '-' ?><?php echo $record->getPrimaryKey() ?>">
          <?php $view->endSquash() ?>
        <?php endif; ?>
      </td>
    <?php endif; ?>

    <?php // Data columns ?>
    <?php foreach ($columns as $name => $column): ?>
      <?php $view->squash() ?>
      <td class="type-<?php echo $column->getTypeName() ?> column-<?php echo $name ?>"
        <?php if (!$i): ?>
          <?php $style = '' ?>
          <?php if (isset($column_options[$name]['width'])) $style .= 'width: ' . $column_options[$name]['width'] . ';'; ?>
          <?php if (isset($column_options[$name]['min-width'])) $style .= 'min-width: ' . $column_options[$name]['min-width'] . ';'; ?>
          <?php if ($style) echo 'style="' . $style . '"' ?>
        <?php endif; ?>
      >
      <?php $view->endSquash() ?>
        <?php echo $column->renderField(empty($column_options[$name]['is_virtual']) ?
                                          call_user_func(array($record, 'get' . str_replace('_', '', $name))) :
                                          ($record->hasVirtualColumn($name) ?
                                            $record->getVirtualColumn($name) : null),
                                        $record,
                                        (isset($column_options[$name]['link_form']) && $column_options[$name]['link_form']) ?
                                          'class="edit_link" ' . (isset($column_options[$name]['link_attrs']) ?
                                                                  $column_options[$name]['link_attrs']($record) :
                                                                  'href="#id=' . $record->getPrimaryKey() .
                                                                  '" onclick="TableAdmin.edit(' . $record->getPrimaryKey() . '); return false;"')
                                          : null) ?>
      </td>
    <?php endforeach; ?>

    <?php // Action columns ?>
    <?php if (!$print): ?>
      <td class="record-actions"<?php if (!$i && $action_column_width) echo ' style="width: ' . $action_column_width . ';"' ?>>
        <?php foreach($links as $name => $link): ?>
          <?php echo $view->sub($admin->getTemplate('link'), array('name' => $name, 'link' => $link, 'object' => $record)) ?>
        <?php endforeach; ?>
        <?php foreach($actions as $name => $action): ?>
          <?php echo $view->sub($admin->getTemplate('action'), array('name' => $name, 'action' => $action, 'object' => $record)) ?>
        <?php endforeach; ?>
      </td>
    <?php endif; ?>
  </tr>
<?php endforeach; ?>
