<?php

namespace Mockingbird\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * An Account column.
 */
class AccountColumn extends StringColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'account';
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
      'value'   => 'Mockingbird:columns/account.php',
      'filter'  => 'Mockingbird:columns/account_filter.php',
    );
  }

 /**
  * Adds filtering criteria for this field to the query.
  *
  * @param  \ModelCriteria $query
  * @param  mixed          $filter
  * @return \ModelCriteria
  */
  public function addFilter($query, &$filter)
  {
    $column = $this->getPropelColumnName($query);
    $query->add($column, $filter);
    return $query;
  }

  public function addSortCriteria($query, $dir)
  {
    if (strtolower($dir) == 'asc')
      $query->useAccountQuery()->orderByTitle(\Criteria::ASC)->endUse();
    else
      $query->useAccountQuery()->orderByTitle(\Criteria::DESC)->endUse();
    return $query;
  }
}