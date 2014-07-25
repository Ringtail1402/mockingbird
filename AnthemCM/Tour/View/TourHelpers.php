<?php

namespace AnthemCM\Tour\View;

use Silex\Application;

/**
 * Tour module helpers.
 */
class TourHelpers
{
  /**
   * @var \Silex\Application
   */
  protected $app;

  /**
   * The constructor.
   *
   * @param \Silex\Application $app
   */
  public function __construct(Application $app)
  {
    $this->app = $app;
  }

  /**
   * Inserts links to tour.js and tour_data.js as applicable.
   *
   * @param string $tour
   * @return string
   */
  public function init($tour)
  {
    $url = $this->app['request']->getPathInfo();

    $screens = $this->app['tour']->getApplicableScreens($tour, $url);
    if (count($screens))
    {
      $result  = '<script type="text/javascript" src="' . $this->app['url_generator']->generate('tour.data', array('tour' => $tour)) . '?url=' . urlencode($url) . '"></script>' . PHP_EOL;
      $result .= '<script type="text/javascript" src="' . $this->app['core.view.assets']->js('AnthemCM/Tour:tour.js') . '"></script>' . PHP_EOL;
      return $result;
    }
    return null;
  }
}