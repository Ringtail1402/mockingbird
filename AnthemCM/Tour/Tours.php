<?php

namespace AnthemCM\Tour;

use Silex\Application;

/**
 * Tour service class.
 */
class Tours
{
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
   * Returns all screens which may be shown at the moment.
   * This checks url and custom_server_condition.  custom_client_condition will be checked by JS.
   *
   * @param string $tour
   * @param string $url
   * @return array
   * @throws \InvalidArgumentException
   */
  public function getApplicableScreens($tour, $url)
  {
    if (!isset($this->app['Tour'][$tour]))
      throw new \InvalidArgumentException('Unknown tour: ' . $tour);

    // Visited tour screens
    $tour_state = $this->app['settings']->get('tour.state');
    if (isset($tour_state[$tour]))
      $tour_state = $tour_state[$tour];
    else
      $tour_state = array();

    $tour = $this->app['Tour'][$tour];
    foreach ($tour as $screen => $params)
    {
      // Screen already shown
      if (!empty($tour_state[$screen]))
        unset($tour[$screen]);
      // Dependencies on other screens
      elseif (isset($params['require']))
      {
        foreach ($params['require'] as $dependency => $isset)
        {
          if ($isset && empty($tour_state[$dependency]))
            unset($tour[$screen]);
          elseif (!$isset && !empty($tour_state[$dependency]))
            unset($tour[$screen]);
        }
      }
      // URL doesn't match
      elseif (isset($params['url']) && !preg_match('#^' . $params['url'] . '$#', $url))
        unset($tour[$screen]);
      // Custom check doesn't match
      elseif (isset($params['custom_server_condition']) && !$params['custom_server_condition']($this->app))
        unset($tour[$screen]);
    }

    return $tour;
  }

  /**
   * Retrieves data part of the specified screen and marks it as visited, so it would not be displayed anymore.
   *
   * @param string $tour
   * @param string $screen
   * @return array
   * @throws \InvalidArgumentException
   */
  public function getTourScreenAndMarkVisited($tour, $screen)
  {
    if (!isset($this->app['Tour'][$tour]))
      throw new \InvalidArgumentException('Unknown tour: ' . $tour);
    if (!isset($this->app['Tour'][$tour][$screen]))
      throw new \InvalidArgumentException('Unknown tour screen: ' . $screen);

    // Mark visited
    $tour_state = $this->app['settings']->get('tour.state');
    if (!isset($tour_state[$tour]))
      $tour_state[$tour] = array();
    $tour_state[$tour][$screen] = true;
    $this->app['settings']->set('tour.state', $tour_state);

    return $this->app['Tour'][$tour][$screen]['text'];
  }
}