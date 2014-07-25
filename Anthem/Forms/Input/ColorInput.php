<?php

namespace Anthem\Forms\Input;

use Anthem\Forms\Input\StringInput;

/**
 * Colorpicker input, using a JS color picker.
 */
class ColorInput extends StringInput
{
  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:color.php';
  }
}