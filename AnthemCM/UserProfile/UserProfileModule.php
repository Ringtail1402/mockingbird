<?php

namespace AnthemCM\UserProfile;

use Anthem\Core\Module;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use AnthemCM\UserProfile\Controller\UserProfileController;
use AnthemCM\UserProfile\EventHandler\FillProfileFromSocialAccountHandler;

class UserProfileModule extends Module
                        implements ServiceProviderInterface,
                                   ControllerProviderInterface
{
  /**
   * Registers module services.
   *
   * @param \Silex\Application $app
   * @return void
   */
  public function register(Application $app)
  {
    // Event handlers
    $app['user_profile.event.user_attach_social.fill_profile_from_social'] = $app->share(function () use ($app) {
      return new FillProfileFromSocialAccountHandler($app['dispatcher']);
    });

    // Controller
    $app['user_profile.controller'] = $app->share(function () use ($app) {
      return new UserProfileController($app);
    });
  }

  /**
   * Connects module routes.
   *
   * @param \Silex\Application $app
   * @return \Silex\ControllerCollection
   */
  public function connect(Application $app)
  {
    $controllers = new ControllerCollection();

    $controllers->match('/profile',
      function(Request $request) use($app) { return $app['user_profile.controller']->editAction($request); }
    )->bind('user_profile.edit');

    return $controllers;
  }
}
