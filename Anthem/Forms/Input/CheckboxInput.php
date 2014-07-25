<?php

namespace Anthem\Forms\Input;

use Silex\Application;
use Anthem\Forms\Input\BaseInput;

/**
 * <input type="checkbox"> input.
 */
class CheckboxInput extends BaseInput
{
  /**
   * The constructor.
   *
   * @param \Silex\Application $app
   * @param array              $options
   */
  public function __construct(Application $app, array $options = array())
  {
    $options['show_own_help'] = true;
    parent::__construct($app, $options);
  }

  /**
   * Returns value, normalized to true or false.
   *
   * @return boolean
   */
  public function getValue()
  {
    return parent::getValue() ? true : false;
  }

  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:checkbox.php';
  }
}
