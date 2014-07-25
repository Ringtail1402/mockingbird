<?php

namespace Mockingbird\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * A target column, Transaction-specific.
 */
class TransactionTargetColumn extends StringColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'transaction-target';
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
      'value'   => 'Mockingbird:columns/transaction_target.php',
      'filter'  => 'Mockingbird:columns/transaction_target_filter.php',
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
    if (!empty($filter['target_account']))
      $query->filterByTargetAccountId($filter['target_account']);
    if (!empty($filter['counter_party']))
      $query->add('cp.TITLE', '%' . $filter['counter_party'] . '%', \Criteria::LIKE);
    return $query;
  }

  public function addSortCriteria($query, $dir)
  {
    if (strtolower($dir) == 'asc')
      $query->addAscendingOrderByColumn('COALESCE(ta.Title, cp.Title)');
    else
      $query->addDescendingOrderByColumn('COALESCE(ta.Title, cp.Title)');
    return $query;
  }
}