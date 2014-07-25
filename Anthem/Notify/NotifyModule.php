<?php

namespace Anthem\Notify;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Anthem\Core\Module;
use Anthem\Core\View\HelperProviderInterface;
use Anthem\Notify\ModelService\NotifyService;
use Anthem\Notify\Controller\NotifyController;

/**
 * Notify module, a simple notification manager.
 */
class NotifyModule extends Module implements ServiceProviderInterface,
                                             ControllerProviderInterface,
                                             HelperProviderInterface
{
  /**
   * Registers notification services.
   *
   * @param  Application $app
   * @return void
   */
  public function register(Application $app)
  {
    $app['notify']            = $app->share(function() use ($app) { return new NotifyService($app); });
    $app['notify.controller'] = $app->share(function() use ($app) { return new NotifyController($app); });
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
    $self = $this;

    $controllers->get('/_notify_update',
      function(Request $request) use($app)          { return $app['notify.controller']->updateAction($request); }
    )->bind('_notify_update');
    $controllers->post('/_notify_dismiss/{uniqid}',
      function(Request $request, $uniqid) use($app) { return $app['notify.controller']->dismissAction($request, $uniqid); }
    )->bind('_notify_dismiss');

    return $controllers;
  }

  /**
   * Returns Mockingbird helpers.
   *
   * @param  \Silex\Application $app
   * @return object[string]
   */
  public function getHelpers(Application $app)
  {
    return array(
      //'notify' => new NotifyHelpers($app),
    );
  }
}
