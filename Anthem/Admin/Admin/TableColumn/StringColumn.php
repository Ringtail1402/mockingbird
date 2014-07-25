<?php

namespace Anthem\Admin\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\PropelColumn;

/**
 * A simple string column.
 */
class StringColumn extends PropelColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'string';
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
      'value'   => 'Anthem/Admin:table/columns/string.php',
      'filter'  => 'Anthem/Admin:table/columns/string_filter.php',
      'filter.inline' => 'Anthem/Admin:table/columns/string_filter_inline.php',
    );
  }

 /**
  * Adds filtering criteria for this field to the query.
  *
  * @param  mixed $query
  * @param  mixed $filter
  * @return void
  */
  public function addFilter($query, &$filter)
  {
    $filter = trim($filter);
    $column = $this->getPropelColumnName($query);
    $query->add($column, '%' . $filter . '%', Criteria::LIKE);
    return $query;
  }

  public function getJS()
  {
    return null;
    // TODO: untested at the moment.
    //return 'admin/columns/string.js';
  }
}