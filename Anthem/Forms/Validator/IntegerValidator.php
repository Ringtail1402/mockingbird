<?php

namespace Anthem\Forms\Validator;

use Anthem\Forms\Validator\BaseValidator;

/**
 * A validator that verifies that value is an integer (or rather, a string with an integer).
 *
 * Options:
 * - message (string) Custom message.
 */
class IntegerValidator extends BaseValidator
{
 /**
  * Performs validation.
  *
  * @param  mixed $value
  * @return boolean|string
  */
  public function validate($value)
  {
    if ((!empty($value) && !is_numeric($value)) || !is_int($value + 0))
      return (isset($this->options['message']) ? $this->options['message'] : _t('Forms.NUMBER_VALIDATOR_MESSAGE'));
    if (is_numeric($value))
    {
      if (isset($this->options['min']) && $value < $this->options['min'])
        return _t('Forms.NUMBER_VALIDATOR_MIN_MESSAGE', $this->options['min']);
      if (isset($this->options['max']) && $value > $this->options['max'])
        return _t('Forms.NUMBER_VALIDATOR_MAX_MESSAGE', $this->options['max']);
    }
    return true;
  }
}