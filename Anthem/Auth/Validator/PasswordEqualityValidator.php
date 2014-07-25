<?php

namespace Anthem\Auth\Validator;

use Silex\Application;
use Anthem\Forms\Validator\BaseValidator;

/**
 * A validator that checks that two passwords match.
 *
 * Options:
 * - message (string) Custom message.
 * - password1_field (string) First password field, defaults to "password".
 * - password2_field (string) Second password field, defaults to "password2".
 */
class PasswordEqualityValidator extends BaseValidator
{
  /**
   * Performs validation.
   *
   * @param  mixed $value
   * @return boolean|string
   */
  public function validate($value)
  {
    $password1_field = isset($this->options['password1_field']) ? $this->options['password1_field'] : 'password';
    $password2_field = isset($this->options['password2_field']) ? $this->options['password2_field'] : 'password2';
    if ($value[$password1_field] != $value[$password2_field])
      return (isset($this->options['message']) ? $this->options['message'] : _t('Auth.PASSWORD_EQUALITY_VALIDATOR_MESSAGE'));
    return true;
  }
}