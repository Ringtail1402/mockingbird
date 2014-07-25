<?php

namespace Anthem\Admin\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * A datetime column.
 */
class DateTimeColumn extends StringColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'datetime';
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
      'value'   => 'Anthem/Admin:table/columns/datetime.php',
      'filter'  => 'Anthem/Admin:table/columns/datetime_filter.php',
      'filter.inline' => 'Anthem/Admin:table/columns/datetime_filter_inline.php',
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
    $from = isset($filter['from']) ? date('Y-m-d', strtotime($filter['from'])) . ' 00:00:00' : null;
    $to   = isset($filter['to'])   ? date('Y-m-d', strtotime($filter['to']))   . ' 23:59:59' : null;
    $column = $this->getPropelColumnName($query);
    if ($from && $to)
    {
      $c =       $query->getNewCriterion($column, $from, Criteria::GREATER_EQUAL);
      $c->addAnd($query->getNewCriterion($column, $to, Criteria::LESS_EQUAL));
      $query->add($c);
    }
    elseif ($from)
      $query->add($column, $from, Criteria::GREATER_EQUAL);
    elseif ($to)
      $query->add($column, $to, Criteria::LESS_EQUAL);
    return $query;
  }

  public function getJS()
  {
    return 'Anthem/Admin:columns/datetime.js';
  }
}