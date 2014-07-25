<?php

namespace Mockingbird\Input;

use Anthem\Forms\Input\SelectInput;
use Silex\Application;
use Mockingbird\Model\Currency;

/**
 * An input field for currency selection.
 */
class CurrencyDenominationInput extends SelectInput
{
  /**
   * The constructor.
   *
   * @param Application $app
   * @param array       $options
   */
  public function __construct(Application $app, array $options = array())
  {
    $options['values'] = array();
    $options['option_attrs'] = array();
    $currencies = $app['mockingbird.model.currency']->getAll();
    /** @var Currency[] $currencies */
    foreach ($currencies as $currency)
    {
      $options['values'][$currency->getId()] = $currency->getTitle();
      $format = explode('#', $currency->getFormat());
      $options['option_attrs'][$currency->getId()] =
          'data-currency="' . htmlspecialchars($currency->getTitle()) . '" ' .
          'data-rate="' . $currency->getRateToPrimary() . '" ' .
          'data-format-pre="' . htmlspecialchars($format[0]) . '" ' .
          'data-format-post="' . htmlspecialchars($format[1]) . '"';
    }

    parent::__construct($app, $options);
  }
}