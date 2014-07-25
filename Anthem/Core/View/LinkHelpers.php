<?php

namespace Anthem\Core\View;

use Silex\Application;

/**
 * Link generation helpers.
 */
class LinkHelpers
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
  * Generates an URL to route.
  *
  * @param  string  $route
  * @param  array   $params
  * @param  boolean $absolute
  * @return string
  */
  public function url($route, $params = array(), $absolute = false)
  {
    if ($route[0] == '/') return $route;
    $pos = strpos($route, '?');
    if ($pos !== false)
    {
      parse_str(substr($route, $pos + 1), $params);
      $route = substr($route, 0, $pos);
    }
    return $this->app['url_generator']->generate($route, $params, $absolute);
  }
}