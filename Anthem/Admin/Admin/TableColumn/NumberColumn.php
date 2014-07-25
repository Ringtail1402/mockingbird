<?php

namespace Anthem\Admin\Admin\TableColumn;

use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * A simple number column.
 */
class NumberColumn extends StringColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'number';
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
      'value'   => 'Anthem/Admin:table/columns/number.php',
      'filter'  => 'Anthem/Admin:table/columns/number_filter.php',
      'filter.inline' => 'Anthem/Admin:table/columns/number_filter_inline.php',
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
    $column = $this->getPropelColumnName($query);
    if (isset($filter['from']) && is_numeric($filter['from']) &&
        isset($filter['to']) && is_numeric($filter['to']))
    {
      $filter['from'] = trim($filter['from']);
      $filter['to'] = trim($filter['to']);
      $c       = $query->getNewCriterion($column, $filter['from'], \Criteria::GREATER_EQUAL);
      $c->addAnd($query->getNewCriterion($column, $filter['to'], \Criteria::LESS_EQUAL));
      $query->add($c);
    }
    elseif (isset($filter['from']) && is_numeric($filter['from']))
    {
      $filter['from'] = trim($filter['from']);
      $query->add($column, $filter['from'], \Criteria::GREATER_EQUAL);
    }
    elseif (isset($filter['to']) && is_numeric($filter['to']))
    {
      $filter['to'] = trim($filter['to']);
      $query->add($column, $filter['to'], \Criteria::LESS_EQUAL);
    }
    return $query;
  }
}