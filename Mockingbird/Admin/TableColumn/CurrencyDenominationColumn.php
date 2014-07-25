<?php

namespace Mockingbird\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * A Currency column -- not for showing amount of currency, but for showing currency itself.
 */
class CurrencyDenominationColumn extends StringColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'currency';
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
      'value'   => 'Mockingbird:columns/currency_denomination.php',
      'filter'  => 'Mockingbird:columns/currency_denomination_filter.php',
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
      $query->useCurrencyQuery()->orderByIsPrimary(\Criteria::ASC)->orderByTitle(\Criteria::ASC)->endUse();
    else
      $query->useCurrencyQuery()->orderByIsPrimary(\Criteria::DESC)->orderByTitle(\Criteria::DESC)->endUse();
    return $query;
  }
}