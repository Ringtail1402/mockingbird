<?php

namespace Anthem\Forms\Validator;

use Anthem\Forms\Validator\BaseValidator;

/**
 * A validator that verifies that value is a primary key of a specified model.
 *
 * Options:
 * - model (string, required) Model class.
 * - message (string) Custom message.
 */
class PrimaryKeyValidator extends BaseValidator
{
 /**
  * Performs validation.
  *
  * @param  mixed $value
  * @return boolean|string
  */
  public function validate($value)
  {
    if (!$value) return true;
    $object = call_user_func(array($this->options['model'] . 'Query', 'create'))->findPk($value);
    if (!$object)
      return (isset($this->options['message']) ? $this->options['message'] : _t('Forms.PRIMARY_KEY_VALIDATOR_MESSAGE'));
    return true;
  }
}