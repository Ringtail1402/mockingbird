<?php

namespace Mockingbird;

use Silex\Application;
use Anthem\Settings\SettingsInterface;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\CheckboxInput;
use Anthem\Forms\Input\SelectInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\IntegerValidator;
use Anthem\Forms\Validator\NumberValidator;
use Anthem\Forms\Validator\PrimaryKeyValidator;
use Mockingbird\Input\CurrencyInput;

/**
 * Mockingbird module settings.
 */
class MockingbirdSettings implements SettingsInterface
{
  /**
   * Returns Mockingbird module settings.
   *
   * @return array
   */
  public function getSettings(Application $app)
  {
    return array(
      'mockingbird' => array(
        'title' => 'Mockingbird',

        'page_contents' => array(
          'mockingbird.default_currency' => array(
            // Per-user default currency
            'title'     => 'DEFAULT_CURRENCY',
            'help'      => 'DEFAULT_CURRENCY_HELP',
            'default'   => null,
            'input'     => function () use ($app) {
              $currencies = array();
              $primary = $app['mockingbird.model.currency']->getPrimaryCurrency();
              $primary_fmt = str_replace('#', '%.2f', $primary->getFormat());
              foreach ($app['mockingbird.model.currency']->getAll() as $currency)
              {
                $currencies[$currency->getId()] = $currency->getTitle();
                if (!$currency->getIsPrimary())
                {
                  $currencies[$currency->getId()] .= sprintf(' (%s = %s)', str_replace('#', '1', $currency->getFormat()),
                    sprintf($primary_fmt, $currency->getRateToPrimary()));
                }
              }

              return new SelectInput($app, array(
                'values' => $currencies,
                'validator' => array(
                  new RequiredValidator(),
                  new PrimaryKeyValidator(array('model' => 'Mockingbird\\Model\\Currency')),
                )
              ));
            }
          ),

          // Max number of days within which the transaction can be editable
          'mockingbird.max_transaction_editable_age' => array(
            'title'   => 'MAX_TRANSACTION_EDITABLE_AGE',
            'help'    => 'MAX_TRANSACTION_EDITABLE_AGE_HELP',
            'default' => 3,
            'input'   => function () use ($app) {
              return new StringInput($app, array(
                'class' => 'input-mini',
                'validator' => array(
                  new RequiredValidator(),
                  new IntegerValidator(array('min' => 1)),
                ),
              ));
            }
          ),
          // Limit for avg. spending/day calculation
          'mockingbird.day_average_limit' => array(
            'title'   => 'DAY_AVERAGE_LIMIT',
            'help'    => 'DAY_AVERAGE_LIMIT_HELP',
            'default' => 1000,
            'input'   => function () use ($app) {
              return new CurrencyInput($app, array(
                'currency'  => $app['mockingbird.model.currency']->getDefaultCurrency(),
                'absolute'  => true,
                'validator' => array(
                  new NumberValidator(array('min' => 0)),
                ),
              ));
            }
          ),
          // Automatic currency rate update
          'mockingbird.auto_update_rates' => array(
            'title'   => 'AUTO_UPDATE_RATES',
            'help'    => 'AUTO_UPDATE_RATES_HELP',
            'default' => true,
            'global'  => true,
            'input'   => function () use ($app) {
              return new CheckboxInput($app);
            }
          ),
        ),
      ),
    );
  }
}
