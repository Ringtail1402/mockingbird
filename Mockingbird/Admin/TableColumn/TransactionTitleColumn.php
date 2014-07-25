<?php

namespace Mockingbird\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * Transaction title.  Shows parent transaction, if any, or suggests expanding child transactions, if any.
 */
class TransactionTitleColumn extends StringColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'transaction-title';
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
      'value'   => 'Mockingbird:columns/transaction_title.php',
      'filter'  => 'Mockingbird:columns/transaction_title_filter.php',
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
    $filter = trim($filter);
    $column = $this->getPropelColumnName($query);
    $c      = $query->getNewCriterion($column, '%' . $filter . '%', Criteria::LIKE);
    $c->addOr($query->getNewCriterion('pt.Title', '%' . $filter . '%', Criteria::LIKE));
    $query->add($c);
    return $query;
  }
}