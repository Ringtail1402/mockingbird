<?php

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Anthem\Core\Module;

/**
 * Global app class.
 */
class App extends Module
          implements ControllerProviderInterface
{
  /**
   * Set up controllers.
   *
   * @param Silex\Application $app
   * @return Silex\ControllerCollection
   */
  public function connect(Application $app)
  {
    $controllers = new \Silex\ControllerCollection();

    $app['core.error_handlers']->register404Handlers();
    $app['core.error_handlers']->register403Handlers();
    $app['core.error_handlers']->registerFatalHandlers();

    // Homepage
    $controllers->get('/', function (Request $request) use ($app) {
      if ($app['auth']->isGuest())
        return $app['core.view']->render(':homepage.php');
      else
        return new RedirectResponse($app['url_generator']->generate('dashboard'));
    });

    // Demo login
    $controllers->get('/demo', function (Request $request) use ($app) {
      $app['auth']->redirectHTTPS(true);
      $user = $app['auth.model.user']->find($app['demo_user_id']);
      if ($user)
      {
        $app['auth']->logon($user);
        return new RedirectResponse($app['url_generator']->generate('dashboard'));
      }
      else
        throw new NotFoundHttpException('Demo user not found.');
    })->bind('auth.demo');

    return $controllers;
  }
}
