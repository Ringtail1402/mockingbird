<?php

namespace Mockingbird\ModelService;

use Silex\Application;
use Anthem\Propel\ModelService\PropelModelService;
use Mockingbird\Model\Currency;
use Mockingbird\Model\CurrencyQuery;

/**
 * Model service for Currency model.
 */
class CurrencyService extends PropelModelService
{
  /**
   * @var Application
   */
  protected $app;

  /**
   * @var Currency
   */
  protected static $primary_currency = null, $default_currency = null;

  /**
   * @var array  Rates, as loaded from openexhangerates.org and converted to default currency.
   */
  protected static $rates = null;

  public function __construct(Application $app)
  {
    $this->app = $app;
  }

  /**
   * Returns default currency.
   * Default currency is a per-user setting.
   *
   * @return Currency
   */
  public function getDefaultCurrency()
  {
    if (self::$default_currency) return self::$default_currency;

    $currency_id = $this->app['settings']->get('mockingbird.default_currency');
    $currency = $this->createQuery()
                     ->findPk($currency_id);
    if (!$currency) return $this->getPrimaryCurrency();  // Default to primary

    self::$default_currency = $currency;
    return $currency;
  }

  /**
   * Returns primary currency.  This is cached.
   * Primary currency is a global setting.
   *
   * @throws \LogicException
   * @return Currency
   */
  public function getPrimaryCurrency()
  {
    if (self::$primary_currency) return self::$primary_currency;

    $currency = $this->createQuery()
                     ->findOneByIsPrimary(true);
    if (!$currency) throw new \LogicException('Primary currency not defined.');
    self::$primary_currency = $currency;
    return $currency;
  }

  /**
   * Changes primary currency.  This updates rates of all currencies.
   *
   * @param Currency $primary
   */
  public function setPrimaryCurrency(Currency $primary)
  {
    // Old default currency
    $old_primary = $this->getPrimaryCurrency();
    if ($old_primary->getId() == $primary->getId()) return;  // nothing to do

    // No longer default
    $old_primary->setIsPrimary(false);
    $old_primary->save();

    // Factor for rate recalculation
    if (!$primary->getRateToPrimary()) return;  // Shouldn't happen, but anyhow
    $factor = 1.0 / $primary->getRateToPrimary();

    // New default currency
    $primary->setRateToPrimary(1.0);
    $primary->setIsPrimary(true);
    $primary->save();

    // Update rates
    // Propel apparently cannot do a UPDATE SET RateToPrimary = RateToPrimary * {factor}, so do it one by one
    // This is not a common operation anyway
    $currencies = CurrencyQuery::create()
                               ->filterByIsPrimary(false)
                               ->find();
    foreach ($currencies as $currency)
    {
      $currency->setRateToPrimary($currency->getRateToPrimary() * $factor);
      $currency->save();
    }
    self::$primary_currency = $currency;
  }

  /**
   * Updates rates, either for a specific currency or for all currencies.
   * Uses openexchangerates.org API
   *
   * @param Currency $currency
   * @param boolean  $silent
   * @return boolean
   * @throws \Exception
   */
  public function loadRate($currency = null, $silent = false)
  {
    // Load rates from openexchangerates.org and convert them
    if (!self::$rates)
    {
      $rates = @file_get_contents('http://openexchangerates.org/api/latest.json?app_id=' . $this->app['Mockingbird']['openexchangerates.appid']);
      if (!$rates)
        throw new \Exception(_t('CURRENCY_OXR_LOAD_FAILED'));

      $rates = json_decode($rates, true);
      if (!empty($rates['error']))
        throw new \Exception(_t('CURRENCY_OXR_LOAD_ERROR', $rates['description']));

      $oxr_primary_currency = CurrencyQuery::create()
                                           ->filterByTitle($rates['base'])
                                           ->findOne();
      if (!$oxr_primary_currency)
        throw new \Exception(_t('CURRENCY_OXR_UNKNOWN_BASE', $rates['base']));
      $primary = $this->getDefaultCurrency();
      if (!isset($rates['rates'][$primary->getTitle()]))
        throw new \Exception(_t('CURRENCY_OXR_UNKNOWN_PRIMARY', $primary->getTitle()));

      if ($oxr_primary_currency->getIsPrimary())  // USD is primary, no conversion
        $factor = 1;
      else
        $factor = $rates['rates'][$primary->getTitle()];

      self::$rates = array();
      foreach ($rates['rates'] as $title => $rate)
        self::$rates[$title] = $factor / $rate;
    }

    // Update all currencies
    if (!$currency)
    {
      $currencies = CurrencyQuery::create()
                                 ->filterByIsPrimary(false)
                                 ->find();
      $failures = array();
      foreach ($currencies as $currency)
      {
        if (!$this->loadRate($currency, true))
          $failures[] = $currency->getTitle();
      }
      if (count($failures))
        throw new \Exception(_t('CURRENCY_OXR_UNKNOWN_CURRENCY', implode(', ', $failures)));

    }

    if (!isset(self::$rates[$currency->getTitle()]))
    {
      if ($silent) return false;
      throw new \Exception(_t('CURRENCY_OXR_UNKNOWN_CURRENCY', $currency->getTitle()));
    }
    $currency->setRateToPrimary(self::$rates[$currency->getTitle()]);
    $currency->save();
    return true;
  }

  /**
   * Returns all currencies.
   *
   * @return Currency[]
   */
  public function getAll()
  {
    return $this->createQuery()
                ->orderByIsPrimary(\Criteria::DESC)
                ->orderByTitle()
                ->find();
  }

  /**
   * Counts currencies.
   *
   * @return integer
   */
  public function countCurrencies()
  {
    return $this->createQuery()->count();
  }

  /**
   * Returns underlying model class.
   *
   * @return string
   */
  public function getModelClass()
  {
    return 'Mockingbird\\Model\\Currency';
  }
}
