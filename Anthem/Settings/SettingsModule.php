<?php

namespace Anthem\Settings;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Anthem\Core\Module;
use Anthem\Settings\ModelService\SettingsService;
use Anthem\Settings\Controller\SettingsController;
use Anthem\Settings\Controller\AdminSettingsController;

/**
 * Settings module, unified setting interface.
 */
class SettingsModule extends Module implements ServiceProviderInterface,
                                               ControllerProviderInterface
{
  /**
   * Registers settings services.
   *
   * @param  Application $app
   * @return void
   */
  public function register(Application $app)
  {
    $app['settings']                  = $app->share(function() use ($app) { return new SettingsService($app); });
    $app['settings.controller']       = $app->share(function() use ($app) { return new SettingsController($app); });
    $app['settings.controller.admin'] = $app->share(function() use ($app) { return new AdminSettingsController($app); });

  }

  /**
   * Returns routes to connect to the given application.
   *
   * @param  Application $app
   * @return ControllerCollection
   */
  public function connect(Application $app)
  {
    $controllers = new ControllerCollection();

    $controllers->get('/_get',
      function(Request $request) use($app) { return $app['settings.controller']->getAction($request); }
    )->bind('settings.get');
    $controllers->post('/_set',
      function(Request $request) use($app) { return $app['settings.controller']->setAction($request); }
    )->bind('settings.set');

    $controllers->match('/settings/global',
      function(Request $request) use($app) { return $app['settings.controller.admin']->indexAction($request, true); }
    )->bind('settings.admin.global');
    $controllers->match('/settings',
      function(Request $request) use($app) { return $app['settings.controller.admin']->indexAction($request); }
    )->bind('settings.admin');

    return $controllers;
  }
}
