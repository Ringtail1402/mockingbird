<?php

namespace Anthem\Settings\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Simple settings get/set AJAX controller.
 */
class SettingsController
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
   * Gets a setting.
   *
   * @param  Request $request
   * @return string
   */
  public function getAction(Request $request)
  {
    return new Response(json_encode($this->app['settings']->get($request->get('key'))),
      200, array('Content-Type' => 'application/json'));
  }

  /**
   * Sets a setting.
   *
   * @param  Request $request
   * @return string
   */
  public function setAction(Request $request)
  {
    // Require authorization
    if (!empty($app['Auth']['enable'])) $this->app['auth']->checkAuthorization();

    $this->app['settings']->set($request->get('key'), $request->get('value'));
    return new Response(json_encode($this->app['settings']->get($request->get('key'))),
          200, array('Content-Type' => 'application/json'));
  }
}