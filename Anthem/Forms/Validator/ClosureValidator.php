<?php

namespace Anthem\Forms\Validator;

use Anthem\Forms\Validator\BaseValidator;

/**
 * A validator that verifies that value using arbitrary function.
 *
 * Options:
 * - closure (function($value), required) Function which actually validates the value.
 * - message (string) Custom message.
 */
class ClosureValidator extends BaseValidator
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
    if (empty($this->options['closure']))
      throw new \LogicException('value option is required for EqualityValidator.');
    $valid = $this->options['closure']($value);

    if (!$valid)
      return (isset($this->options['message']) ? $this->options['message'] : _t('Forms.GENERIC_VALIDATOR_MESSAGE'));
    return true;
  }
}