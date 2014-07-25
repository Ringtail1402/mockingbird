<?php

namespace Mockingbird\Input;

use Anthem\Forms\Input\StringInput;
use Silex\Application;
use Mockingbird\Model\Currency;

/**
 * An input field for currency values.
 */
class CurrencyInput extends StringInput
{
  /**
   * The constructor.  Checks that currency option is set.
   *
   * @param Application $app
   * @param array       $options
   * @throws \LogicException
   */
  public function __construct(Application $app, array $options = array())
  {
    if (!isset($options['currency']) || !$options['currency'] instanceof Currency)
      throw new \LogicException('currency option must be set and must be a valid Currency instance.');
    parent::__construct($app, $options);
  }

  /**
   * Sets input value.  Formats it appropriately.
   *
   * @param  float $value
   * @return void
   */
  public function setValue($value)
  {
    if (is_string($value))
      $value = str_replace(',', '.', $value);  // where does this come from?

    if ($value == 0 && empty($this->options['allow_zero']))
      $this->value = '';
    elseif (is_numeric($value) || $value == null)
    {
      if (!empty($this->options['absolute'])) $value = abs($value);
      $this->value = sprintf('%.2F', $value);
    }
    else
      $this->value = $value;
  }

  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Mockingbird:input/currency.php';
  }
}