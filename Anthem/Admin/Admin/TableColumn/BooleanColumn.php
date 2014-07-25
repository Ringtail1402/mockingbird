<?php

namespace Anthem\Admin\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\PropelColumn;

/**
 * A boolean value column.
 */
class BooleanColumn extends PropelColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'boolean';
  }

  /**
   * Returns default templates to use.
   *
   * @param  none
   * @return array
   */
  protected function getDefaultTemplates()
  {
    return array(
      'value'   => 'Anthem/Admin:table/columns/boolean.php',
      'filter'  => 'Anthem/Admin:table/columns/boolean_filter.php',
      'filter.inline' => 'Anthem/Admin:table/columns/boolean_filter_inline.php',
    );
  }

  /**
   * Adds filtering criteria for this field to the query.
   *
   * @param  mixed $query
   * @param  mixed $filter
   * @return mixed
   */
  public function addFilter($query, &$filter)
  {
    $column = $this->getPropelColumnName($query);
    if (($filter == 'on' && empty($this->options['invert_value'])) ||
        ($filter == 'off' && !empty($this->options['invert_value'])))
      $query->add($column, true);
    if (($filter == 'off' && empty($this->options['invert_value'])) ||
        ($filter == 'on' && !empty($this->options['invert_value'])))
      $query->add($column, false);
    return $query;
  }

  public function getJS()
  {
    return 'Anthem/Admin:columns/boolean.js';
  }
}
