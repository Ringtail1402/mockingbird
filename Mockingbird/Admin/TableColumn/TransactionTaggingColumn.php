<?php

namespace Mockingbird\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * A category/tag column, Transaction-specific.
 */
class TransactionTaggingColumn extends StringColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'transaction-tagging';
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
      'value'   => 'Mockingbird:columns/transaction_tagging.php',
      'filter'  => 'Mockingbird:columns/transaction_tagging_filter.php',
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
    if (!empty($filter['category']))
      $query->filterByCategoryId($filter['category']);
    if (!empty($filter['tag']))
    {
      $query->useRefTransactionTagQuery('rtt', \Criteria::LEFT_JOIN)
              ->useTransactionTagQuery('tt')
                ->filterByTitle($filter['tag'])
              ->endUse()
            ->endUse();
    }
    return $query;
  }

  public function addSortCriteria($query, $dir)
  {
    if (strtolower($dir) == 'asc')
      $query->addAscendingOrderByColumn('tc.Title');
    else
      $query->addDescendingOrderByColumn('tc.Title');
    return $query;
  }
}