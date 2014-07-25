<?php

namespace Mockingbird\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * A currency column.
 */
class CurrencyColumn extends StringColumn
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
      'value'   => 'Mockingbird:columns/currency.php',
      'filter'  => 'Mockingbird:columns/currency_filter.php',
      'filter.inline' => 'Mockingbird:columns/currency_filter_inline.php',
    );
  }

  /**
   * Adds sorting criteria for this field to the query.
   *
   * @param \ModelCriteria $query
   * @param string         $dir
   * @return \ModelCriteria
   */
  public function addSortCriteria($query, $dir)
  {
    $column = $this->getPropelColumnName($query);
    if (!empty($this->options['currency_alias']))
      $column = '(' . $column . ' * ' . $this->options['currency_alias'] . '.RATE_TO_PRIMARY)';

    if (strtolower($dir) == 'asc')
      $query->addAscendingOrderByColumn($column);
    else
      $query->addDescendingOrderByColumn($column);

    return $query;
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
    if (!empty($this->options['currency_alias']))
      $column .= ' * ' . $this->options['currency_alias'] . '.RATE_TO_PRIMARY';
    $from = isset($filter['from']) ? $filter['from'] : null;
    $to =   isset($filter['to']) ? $filter['to'] : null;
    if (($from || $from === '0') && ($to || $to === '0'))
    {
      if ($from > $to)
      {
        $tmp = $from;
        $from = $to;
        $to = $tmp;
      }
      if (isset($filter['sign']) && $filter['sign'] == '-')
      {
        $tmp = -$from;
        $from = -$to;
        $to = $tmp;
      }
      $c =       $query->getNewCriterion(null, $column . ' >= ' . (float)$from, Criteria::CUSTOM);
      $c->addAnd($query->getNewCriterion(null, $column . ' <= ' . (float)$to, Criteria::CUSTOM));
      $query->add($c);
    }
    elseif ($from || $from === '0')
    {
      if (isset($filter['sign']) && $filter['sign'] == '-')
        $query->add(null, $column . ' <= ' . -(float)$from, Criteria::CUSTOM);
      else
        $query->add(null, $column . ' >= ' . (float)$from, Criteria::CUSTOM);
    }
    elseif ($to || $to === '0')
    {
      if (isset($filter['sign']) && $filter['sign'] == '-')
        $query->add(null, $column . ' >= ' . -(float)$to, Criteria::CUSTOM);
      else
        $query->add(null, $column . ' <= ' . (float)$to, Criteria::CUSTOM);
    }
    return $query;
  }
}