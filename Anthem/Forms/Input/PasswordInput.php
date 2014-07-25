<?php

namespace Anthem\Forms\Input;

use Anthem\Forms\Input\StringInput;

/**
 * Password input.
 */
class PasswordInput extends StringInput
{
  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:password.php';
  }
}