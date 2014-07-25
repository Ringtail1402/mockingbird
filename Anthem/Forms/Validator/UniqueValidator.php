<?php

namespace Anthem\Forms\Validator;

use Anthem\Forms\Validator\BaseValidator;

/**
 * A validator that verifies that no same value already exists for this model.
 *
 * Options:
 * - query (function(value), required)  Function which return a ModelCriteria to search by value.
 * - message (string) Custom message.
 */
class UniqueValidator extends BaseValidator
{
 /**
  * Performs validation.
  *
  * @param  mixed $value
  * @return boolean|string
  */
  public function validate($value)
  {
    $count = $this->options['query']($value)->count();
    if ($count > 0)
      return (isset($this->options['message']) ? $this->options['message'] : _t('Forms.UNIQUE_VALIDATOR_MESSAGE'));
    return true;
  }
}