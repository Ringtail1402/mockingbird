<?php

namespace AnthemCM\Tour\Input;

use Anthem\Forms\Input\BaseInput;

/**
 * A button which resets tour state.
 */
class TourResetInput extends BaseInput
{
  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'AnthemCM/Tour:input/tour_reset.php';
  }

  /**
   * Returns value.  The value is always current tour state setting.
   * The input changes it via AJAX call rather than via form post.
   *
   * @return string
   */
  public function getValue()
  {
    return $this->app['settings']->get('tour.state');
  }
}