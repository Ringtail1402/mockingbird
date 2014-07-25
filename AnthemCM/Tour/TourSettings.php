<?php

namespace AnthemCM\Tour;

use Silex\Application;
use Anthem\Settings\SettingsInterface;
use AnthemCM\Tour\Input\TourResetInput;

/**
 * Tour module settings.
 */
class TourSettings implements SettingsInterface
{
  /**
   * Returns Tour module settings.
   *
   * @return array
   */
  public function getSettings(Application $app)
  {
    return array(
      'tour.state' => array(
        'title'   => 'Tour.TOUR_RESET',
        'global'  => false,
        'default' => array(),
        'input'   => function () use ($app) { return new TourResetInput($app); },
      )
    );
  }
}
