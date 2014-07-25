<?php

namespace Anthem\Forms\Validator;

use Anthem\Forms\Validator\BaseValidator;

/**
 * A validator that verifies that value is a number (or rather, a string with a number).
 *
 * Options:
 * - message (string) Custom message.
 */
class NumberValidator extends BaseValidator
{
 /**
  * Performs validation.
  *
  * @param  mixed $value
  * @return boolean|string
  */
  public function validate($value)
  {
    if (!empty($value) && !is_numeric($value))
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