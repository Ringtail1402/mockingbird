<?php

namespace Anthem\Forms\Validator;

use Anthem\Forms\Validator\BaseValidator;

/**
 * A validator that verifies that value is equal or not equal to some other value.
 *
 * Options:
 * - value (mixed, required) Value for comparison.  Can be a function.
 * - not_equal (boolean) Checks non-equality, default is false.
 * - message (string) Custom message.
 */
class EqualityValidator extends BaseValidator
{
  /**
   * Performs validation.
   *
   * @param  mixed $value
   * @return boolean|string
   * @throws \LogicException
   */
  public function validate($value)
  {
    if (empty($this->options['value']))
      throw new \LogicException('value option is required for EqualityValidator.');
    $_value = $this->options['value'];
    if (is_callable($_value)) $_value = $_value();

    if (empty($this->options['not_equal']))
      $valid = $value == $_value;
    else
      $valid = $value != $_value;

    if (!$valid)
      return (isset($this->options['message']) ? $this->options['message'] : _t('Forms.GENERIC_VALIDATOR_MESSAGE'));
    return true;
  }
}