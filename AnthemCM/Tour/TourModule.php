<?php

namespace AnthemCM\Tour;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Anthem\Core\Module;
use Anthem\Core\View\HelperProviderInterface;
use Anthem\Settings\SettingsInterface;
use Anthem\Settings\SettingsProviderInterface;
use AnthemCM\Tour\Controller\TourController;
use AnthemCM\Tour\Tours;
use AnthemCM\Tour\View\TourHelpers;
use AnthemCM\Tour\TourSettings;

/**
 * Tour module, a module which allows creating hints for first use.
 */
class TourModule extends Module implements ServiceProviderInterface,
                                           ControllerProviderInterface,
                                           HelperProviderInterface,
                                           SettingsProviderInterface
{
  /**
   * Registers Tour services.
   *
   * @param  Application $app
   * @return void
   */
  public function register(Application $app)
  {
    $app['tour']            = $app->share(function() use ($app) { return new Tours($app); });
    $app['tour.controller'] = $app->share(function() use ($app) { return new TourController($app); });
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

    // Dashboard
    $controllers->get('/tour/{tour}/tour-data.js',
      function(Request $request, $tour) use($app) { return $app['tour.controller']->dataAction($request, $tour); }
    )->bind('tour.data');
    $controllers->post('/tour/{tour}/{screen}',
      function(Request $request, $tour, $screen) use($app) { return $app['tour.controller']->screenAction($request, $tour, $screen); }
    )->bind('tour.screen');

    return $controllers;
  }

  /**
   * Returns Tour helpers.
   *
   * @param  \Silex\Application $app
   * @return object[string]
   */
  public function getHelpers(Application $app)
  {
    return array(
      'tour'  => new TourHelpers($app),
    );
  }

  /**
   * Returns Tour settings.
   *
   * @return SettingsInterface
   */
  public function getSettings()
  {
    return new TourSettings();
  }
}
