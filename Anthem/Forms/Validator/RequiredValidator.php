<?php

namespace Anthem\Forms\Validator;

use Anthem\Forms\Validator\BaseValidator;

/**
 * A trivial validator that verifies that value is non-empty.
 *
 * Options:
 * - message (string) Custom message.
 */
class RequiredValidator extends BaseValidator
{
 /**
  * Performs validation.
  *
  * @param  mixed $value
  * @return boolean|string
  */
  public function validate($value)
  {
    if (empty($value) || (is_numeric($value) && $value == 0))
      return (isset($this->options['message']) ? $this->options['message'] : _t('Forms.REQUIRED_VALIDATOR_MESSAGE'));
    return true;
  }
}