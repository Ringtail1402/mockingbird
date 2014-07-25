<?php

namespace Anthem\Forms\Input;

use Anthem\Forms\Input\BaseInput;

/**
 * Simple <input type="text">, about the simplest input possible.
 */
class StringInput extends BaseInput
{
  /**
   * Returns value, trimming it by default.
   *
   * @return string
   */
  public function getValue()
  {
    if (!empty($this->options['no_trim']))
      return $this->value;
    return trim($this->value);
  }

  /**
   * Sets value (duh).
   *
   * @param mixed $value
   */
  public function setValue($value)
  {
    if (!empty($this->options['format']))
      $value = sprintf($this->options['format'], $value);
    parent::setValue($value);
  }


  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:string.php';
  }
}