<?php

namespace Anthem\Forms\Validator;

use Anthem\Forms\Validator\BaseValidator;

/**
 * A validator that verifies that value matches (or doesn't match) a specific regular expression.
 *
 * Options:
 * - regexp (string, required): Regexp to match against.
 * - reverse (boolean): If true, checks that the value doesn't match the regexp.
 * - message (string): Custom message.
 */
class RegexpValidator extends BaseValidator
{
  /**
   * Performs validation.
   *
   * @param  mixed $value
   * @return boolean|string
   */
  public function validate($value)
  {
    // Empty value is considered valid, use RequiredValidator to catch empty values
    if (!$value) return true;

    $result = preg_match($this->options['regexp'], $value);
    if (!empty($this->options['reverse'])) $result = !$result;
    if (!$result && isset($this->options['message']))
      return $this->options['message'];
    else
      return $result;
  }
}