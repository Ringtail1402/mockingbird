<?php

namespace Anthem\Forms\Input;

use Anthem\Forms\Input\BaseInput;

/**
 * Simple <input type="hidden">.
 */
class HiddenInput extends BaseInput
{
  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:hidden.php';
  }

  /**
   * Treat empty string as null.
   *
   * @return mixed|null
   */
  public function getValue()
  {
    if ($this->value == '') return null;
    return $this->value;
  }

  /**
   * This is an invisible input (well, duh).
   *
   * @return boolean
   */
  public function isInvisible()
  {
    return true;
  }
}