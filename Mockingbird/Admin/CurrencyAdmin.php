<?php

namespace Mockingbird\Admin;

use Anthem\Admin\Admin\TableAdminPage;
use Mockingbird\Model\Currency;
use Mockingbird\Model\CurrencyQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Currency object admin page.
 */
class CurrencyAdmin extends TableAdminPage
{
  public function getTitle()
  {
    return _t('MENU_CURRENCIES');
  }

  public function getSubtitle()
  {
    return _t('CURRENCY_SUBTITLE');
  }

  protected function getOptions()
  {
    $app = $this->app;
    $self = $this;

    return array(
      'form'          => 'Mockingbird\\Form\\CurrencyForm',
      'table_columns' => array(
        'title'      => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\StringColumn',
                              'title' => _t('CURRENCY_TITLE'), 'width' => '15%', 'sort' => true, 'filter' => true, 'link_form' => true),
        'is_primary' => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\BooleanColumn',
                              'title' => _t('ISPRIMARY'), 'width' => '15%', 'sort' => true, 'filter' => true,
                              'options' => array('false_empty' => true)),
        'rate_to_primary' => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\NumberColumn',
                              'title' => _t('RATE_TO_PRIMARY'), 'width' => '15%', 'sort' => true, 'filter' => false,
                              'options' => array('format' => '%.4f')),
        'format'     => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\StringColumn',
                              'title' => _t('FORMAT'), 'width' => '15%', 'sort' => true, 'filter' => true),
      ),
      'default_sort'  => array('title', 'asc'),
      'can_edit'      => false,
      'can_purge'     => false,
      'extra_table_actions' => $this->ro ? array() : array('update_rates' => array(
        'title'   => '<i class="icon-refresh"></i> ' . _t('CURRENCY_UPDATE_RATES'),
        'action'  => function() use($self) { return $self->updateRates(); },
        'reload'  => true,
      )),
      'extra_js'      => array(
        'Mockingbird:currencies.js',
        'Mockingbird:util.js',
      )
    );
  }

  /**
   * Returns a new query object.
   *
   * @param  none
   * @return \ModelCriteria
   */
  protected function getQuery()
  {
    return CurrencyQuery::create('c')
                        ->leftJoinAccounts('a')
                        ->withColumn('SUM(a.Id)', 'num_accounts')
                        ->groupBy('c.Id');
  }

  /**
   * Checks if an object can be deleted.
   *
   * @param  Currency $object
   * @return bool
   */
  public function testDelete($object)
  {
    // Do not allow if any accounts exist, or if is default currency.
    if ($object->hasVirtualColumn('num_accounts'))
      $naccounts = $object->getVirtualColumn('num_accounts');
    else
      $naccounts = $object->countAccountss();
    return !$object->isNew() && !$object->getIsPrimary() && !$naccounts;
  }

  /**
   * Checks if several objects can be deleted.
   *
   * @param  array $ids
   * @return boolean
   */
  public function testDeleteMass(array $ids)
  {
    // Do not allow if any accounts exist, or if is default currency.
    $ncurrencies = CurrencyQuery::create('c')
                                ->filterByPrimaryKeys($ids)
                                ->leftJoinAccounts('a')
                                ->withColumn('SUM(a.Id)', 'num_accounts')
                                ->filterByIsPrimary(false)
                                ->having('num_accounts IS NULL')
                                ->groupBy('c.Id')
                                ->count();
    return ($ncurrencies > 0);
  }

  /**
   * Deletes several objects.
   *
   * @param  array $ids
   * @return boolean
   */
  public function deleteMass(array $ids)
  {
    // Do not allow if any accounts exist, or if is default currency.
    $currencies = CurrencyQuery::create('c')
                               ->filterByPrimaryKeys($ids)
                               ->leftJoinAccounts('a')
                               ->withColumn('SUM(a.Id)', 'num_accounts')
                               ->filterByIsPrimary(false)
                               ->having('num_accounts IS NULL')
                               ->groupBy('c.Id')
                               ->find();
    $ids = array();
    foreach ($currencies as $currency) $ids[] = $currency->getId();
    CurrencyQuery::create('c')
                 ->filterByPrimaryKeys($ids)
                 ->delete();

    return true;
  }

  /**
   * Loads rates of all currencies from openexchangerates.org.
   */
  public function updateRates()
  {
    try
    {
      $this->app['mockingbird.model.currency']->loadRate();
    }
    catch (\Exception $e)
    {
      $this->app['notify']->addTransient($e->getMessage(), 'error');
    }
    return true;
  }
}
