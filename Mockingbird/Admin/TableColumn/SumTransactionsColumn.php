<?php

namespace Mockingbird\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\DateTimeColumn;
use Mockingbird\Admin\TableColumn\CurrencyColumn;

/**
 * A column which shows a sum of transaction and allows filtering by them.
 * This is basically a CurrencyColumn but with DateTimeColumn's filters.
 */
class SumTransactionsColumn extends CurrencyColumn
{
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
      'filter'  => 'Anthem/Admin:table/columns/datetime_filter.php',
    );
  }

  /**
   * Adds filtering criteria for this field to the query.
   *
   * @param  \ModelCriteria $query
   * @param  mixed          $filter
   * @return \ModelCriteria
   * @throws \LogicException
   */
  public function addFilter($query, &$filter)
  {
    if (empty($this->options['transaction_alias']))
      throw new \LogicException('transaction_alias option required for filtering by SumTransactionsColumn.');

    $date_column = new DateTimeColumn($this->app, $this->options['transaction_alias'] . '.CREATED_AT', array('is_virtual' => true));
    $date_column->addFilter($query, $filter);

    return $query;
  }

  /**
   * Renders the value.  Handles conversion from primary to default currency.
   *
   * @param mixed  $value
   * @param object $object
   * @param string $link
   * @return string
   */
  public function renderField($value, $object, $link = null)
  {
    $value /= $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary();
    return parent::renderField($value, $object, $link);
  }


  public function getJS()
  {
    return 'Anthem/Admin:columns/datetime.js';
  }
}