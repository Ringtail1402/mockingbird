<?php

namespace AnthemCM\Tour\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tour module AJAX controller.
 */
class TourController
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

    // Require authorization
    if (!empty($app['Auth']['enable'])) $app['auth']->checkAuthorization();
  }

  /**
   * Generates data JS for the specified tour, as applicable to current tour state and URL.
   *
   * @param Request $request
   * @param string  $tour
   * @return string
   */
  public function dataAction(Request $request, $tour)
  {
    $screens = $this->app['tour']->getApplicableScreens($tour, $request->get('url'));

    return new Response($this->app['core.view']->render('AnthemCM/Tour:tour_data.js.php', array(
        'tour'    => $tour,
        'screens' => $screens
      )),
      200, array('Content-Type' => 'text/javascript', 'Cache-Control' => 'no-cache'));
  }

  /**
   * Renders a specific screen for the specified tour.  Marks it as visited.
   *
   * @param Request $request
   * @param string  $tour
   * @param string  $screen
   * @return string
   */
  public function screenAction(Request $request, $tour, $screen)
  {
    $screen = $this->app['tour']->getTourScreenAndMarkVisited($tour, $screen);

    return $this->app['core.view']->render($screen['template']);
  }
}