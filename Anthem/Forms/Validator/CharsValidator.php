<?php

namespace Anthem\Forms\Validator;

use Anthem\Forms\Validator\BaseValidator;

/**
 * A validator that verifies that value either consists of specified characters, or never includes them.
 *
 * Options:
 * - characters (string, required): Characters permitted/not permitted.
 * - reverse (boolean): If true, checks that characters don't appear in value.
 * - message (string): Custom message.
 */
class CharsValidator extends BaseValidator
{
 /**
  * Performs validation.
  *
  * @param  mixed $value
  * @return boolean|string
  */
  public function validate($value)
  {
    // Cut all characters
    $value_cut = $value;
    for ($i = 0; $i < strlen($this->options['characters']); $i++)
      $value_cut = str_replace($this->options['characters'][$i], '', $value_cut);

    if (isset($this->options['reverse']) && $this->options['reverse'])
      $result = $value_cut == $value;
    else
      $result = $value_cut == '';

    if (!$result && isset($this->options['message']))
      return $this->options['message'];
    else
      return $result;
  }
}