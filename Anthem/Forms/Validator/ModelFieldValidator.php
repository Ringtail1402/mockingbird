<?php

namespace Anthem\Forms\Validator;

use Anthem\Forms\Validator\BaseValidator;

/**
 * A validator that verifies that a specified model with a specified value exists.
 *
 * Options:
 * - model (string, required) Model class.
 * - field (string, requried) Field.
 * - message (string) Custom message.
 */
class ModelFieldValidator extends BaseValidator
{
 /**
  * Performs validation.
  *
  * @param  mixed $value
  * @return boolean|string
  */
  public function validate($value)
  {
    $object = call_user_func(array($this->options['model'] . 'Query', 'create'))
              ->filterBy($this->options['field'], $value)
              ->findOne();
    if (!$object)
      return (isset($this->options['message']) ? $this->options['message'] : _t('Forms.MODEL_FIELD_VALIDATOR_MESSAGE'));
    return true;
  }
}