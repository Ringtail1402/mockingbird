<?php

namespace AnthemCM\Feedback;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Anthem\Core\Module;
use AnthemCM\Feedback\ModelService\FeedbackService;
use AnthemCM\Feedback\Admin\FeedbackAdmin;
use AnthemCM\Feedback\Controller\FeedbackController;

/**
 * User feedback module.
 */
class FeedbackModule extends Module implements ServiceProviderInterface,
                                               ControllerProviderInterface
{
  /**
   * Registers feedback services.
   *
   * @param  Application $app
   * @return void
   */
  public function register(Application $app)
  {
    $app['feedback.controller'] = $app->share(function() use ($app)  { return new FeedbackController($app); });
    $app['feedback.model']      = $app->share(function()             { return new FeedbackService(); });
    $app['feedback.admin']      = $app->share(function() use ($app)  { return new FeedbackAdmin($app['feedback.model'], $app); });
  }

  /**
   * Registers feedback routes.
   *
   * @param  Application          $app An Application instance
   * @return ControllerCollection      A ControllerCollection instance
   */
  public function connect(Application $app)
  {
    $controllers = new ControllerCollection();

    $controllers->match('/feedback',
      function(Request $request) use($app) { return $app['feedback.controller']->feedbackAction($request); }
    )->bind('feedback.ignorehttps');

    return $controllers;
  }
}
