<?php

namespace Anthem\Forms\Input;

use Anthem\Forms\Input\BaseInput;

/**
 * <textarea> input, like string but, well, textarea.
 */
class TextareaInput extends BaseInput
{
  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:textarea.php';
  }
}