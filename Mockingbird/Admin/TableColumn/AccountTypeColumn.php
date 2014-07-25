<?php

namespace Mockingbird\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * An account type column, Account-specific.
 */
class AccountTypeColumn extends StringColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'account-type';
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
      'value'   => 'Mockingbird:columns/account_type.php',
      'filter'  => 'Mockingbird:columns/account_type_filter.php',
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
    switch ($filter)
    {
      case 'normal':
        $query->filterByIsdebt(false);
        break;

      case 'debit':
        $query->filterByIsdebt(true);
        $query->filterByIscredit(false);
        break;

      case 'credit':
        $query->filterByIsdebt(true);
        $query->filterByIscredit(true);
        break;
    }
    return $query;
  }

  /**
   * Adds sorting criteria for this field to the query.
   *
   * @param  \ModelCriteria $query
   * @param  string         $dir
   * @return \ModelCriteria
   */
  public function addSortCriteria($query, $dir)
  {
    $query->orderByIsdebt($dir);
    $query->orderByIscredit($dir);
    return $query;
  }
}