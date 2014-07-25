<?php

namespace Anthem\Forms\Input;

use Anthem\Forms\Input\BaseInput;
use Silex\Application;

/**
 * <select> input.  Options:
 * - values (array, required) Select options.  May be arranged in two level (via <optgroup>).
 * - add_empty (boolean|string) Add a first empty option.
 * - option_attrs (array) Extra attributes for <option> tags ($value => $attr_string).
 *   If this is a string, provides text for empty option.
 */
class SelectInput extends BaseInput
{
  /**
   * The constructor.  Checks that values option is set.
   *
   * @param Application $app
   * @param array       $options
   * @throws \LogicException
   */
  public function __construct(Application $app, array $options = array())
  {
    if (!isset($options['values']) || !is_array($options['values']))
      throw new \LogicException('values option must be set and must be an array.');
    parent::__construct($app, $options);
  }

  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:select.php';
  }
}