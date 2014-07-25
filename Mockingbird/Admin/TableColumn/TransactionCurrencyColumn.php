<?php

namespace Mockingbird\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Mockingbird\Admin\TableColumn\CurrencyColumn;

/**
 * A currency column, Transaction-specific.
 */
class TransactionCurrencyColumn extends CurrencyColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'transaction-currency';
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
      'value'   => 'Mockingbird:columns/transaction_currency.php',
      'filter'  => 'Mockingbird:columns/currency_filter.php',
      'filter.inline' => 'Mockingbird:columns/currency_filter_inline.php',
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
    $column = $this->getPropelColumnName($query) . ' * c.RATE_TO_PRIMARY';
    $from = isset($filter['from']) ? sprintf('%F', $filter['from']) : null;
    $to = isset($filter['to']) ? sprintf('%F', $filter['to']) : null;
    if (($from || $from === '0') && ($to || $to === '0'))
    {
      $from = sprintf('%F', $from * $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary());
      $to = sprintf('%F', $to * $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary());
      if ($from > $to)
      {
        $tmp = $from;
        $from = $to;
        $to = $tmp;
      }
      if (isset($filter['sign']) && $filter['sign'] == '-')
      {
        $tmp = sprintf('%F', -$from);
        $from = sprintf('%F', -$to);
        $to = $tmp;
      }
      if ($to === '0')
        $c =       $query->getNewCriterion(null, $column . ' > ' . $from, Criteria::CUSTOM);
      else
        $c =       $query->getNewCriterion(null, $column . ' >= ' . $from, Criteria::CUSTOM);
      if ($from === '0')
        $c->addAnd($query->getNewCriterion(null, $column . ' < ' . $to, Criteria::CUSTOM));
      else
        $c->addAnd($query->getNewCriterion(null, $column . ' <= ' . $to, Criteria::CUSTOM));
      $query->add($c);
    }
    elseif ($from === '0')
    {
      if (isset($filter['sign']) && $filter['sign'] == '-')
        $query->add(null, $column . ' < ' . $from, Criteria::CUSTOM);
      else
        $query->add(null, $column . ' > ' . $from, Criteria::CUSTOM);
    }
    elseif ($to === '0')
    {
      if (isset($filter['sign']) && $filter['sign'] == '-')
        $query->add(null, $column . ' > ' . $to, Criteria::CUSTOM);
      else
        $query->add(null, $column . ' < ' . $to, Criteria::CUSTOM);
    }
    elseif ($from)
    {
      $from = sprintf('%F', $from * $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary());
      if (isset($filter['sign']) && $filter['sign'] == '-')
      {
        $c =       $query->getNewCriterion(null, $column . ' <= -' . $from, Criteria::CUSTOM);
        $c->addAnd($query->getNewCriterion(null, $column . ' > 0', Criteria::CUSTOM));
        $query->add($c);
      }
      else
      {
        $c =       $query->getNewCriterion(null, $column . ' >= ' . $from, Criteria::CUSTOM);
        $c->addAnd($query->getNewCriterion(null, $column . ' < 0', Criteria::CUSTOM));
        $query->add($c);
      }
    }
    elseif ($to)
    {
      $to = sprintf('%F', $to * $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary());
      if (isset($filter['sign']) && $filter['sign'] == '-')
      {
        $c =       $query->getNewCriterion(null, $column . ' >= -' . $to, Criteria::CUSTOM);
        $c->addAnd($query->getNewCriterion(null, $column . ' < 0', Criteria::CUSTOM));
        $query->add($c);
      }
      else
      {
        $c =       $query->getNewCriterion(null, $column . ' < ' . $to, Criteria::CUSTOM);
        $c->addAnd($query->getNewCriterion(null, $column . ' > 0', Criteria::CUSTOM));
        $query->add($c);
      }
    }

    return $query;
  }

  public function addSortCriteria($query, $dir)
  {
    $column = $this->getPropelColumnName($query);
    if (strtolower($dir) == 'asc')
      $query->addAscendingOrderByColumn($column . ' + IFNULL(COALESCE(SUM(st.AMOUNT), 0), 0)');
    else
      $query->addDescendingOrderByColumn($column . ' + IFNULL(COALESCE(SUM(st.AMOUNT), 0), 0)');
    return $query;
  }
}