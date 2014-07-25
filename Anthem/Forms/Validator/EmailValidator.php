<?php

namespace Anthem\Forms\Validator;

use Anthem\Forms\Validator\RegexpValidator;

/**
 * A validator that verifies that value is a valid e-mail address.
 *
 * Options:
 * - message (string): Custom message.
 */
class EmailValidator extends RegexpValidator
{
  /**
   * Performs validation.
   *
   * @param  mixed $value
   * @return boolean|string
   */
  public function validate($value)
  {
    $this->options['regexp'] = '/^[^@\s]+@[^\s.]+\.[^\s]+$/';
    if (empty($this->options['message']))
      $this->options['message'] = _t('Forms.EMAIL_VALIDATOR_MESSAGE');

    return parent::validate($value);
  }
}