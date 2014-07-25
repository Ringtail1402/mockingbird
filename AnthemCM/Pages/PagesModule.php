<?php

namespace AnthemCM\Pages;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Anthem\Core\Module;
use Anthem\Propel\Fixtures\FixtureProviderInterface;
use AnthemCM\Pages\ModelService\PageService;
use AnthemCM\Pages\Admin\PageAdmin;
use AnthemCM\Pages\Controller\PageController;

/**
 * Static page module.
 */
class PagesModule extends Module implements ServiceProviderInterface,
                                            ControllerProviderInterface,
                                            FixtureProviderInterface
{
  /**
   * The constructor.  Bind the 404 error handler.
   *
   * @param \Silex\Application $app
   */
  public function __construct(Application $app)
  {
    $app->error(function(\Exception $e, $code) use ($app) {
      if ($code == 404)
      {
        $request = $app['request'];
        $url  = trim($app['request']->getPathInfo(), '/');
        $page = $app['pages.model']->findOneByUrl($url);
        if ($page)
        {
          return $app['pages.controller']->pageAction($page);
        }
      }
    });
  }

  /**
   * Registers page services.
   *
   * @param  Application $app
   * @return void
   */
  public function register(Application $app)
  {
    $app['pages.controller'] = $app->share(function() use ($app)  { return new PageController($app); });
    $app['pages.model']      = $app->share(function()             { return new PageService(); });
    $app['pages.admin']      = $app->share(function() use ($app)  { return new PageAdmin($app['pages.model'], $app); });
  }

  /**
   * Registers page routes.
   *
   * @param  Application          $app An Application instance
   * @return ControllerCollection      A ControllerCollection instance
   */
  public function connect(Application $app)
  {
    $controllers = new ControllerCollection();
    $self = $this;

    // This isn't actually used.  We intercept 404 errors to display pages.  That way,
    // page URL may actually be arbitrary and include slashes.

    //$controllers->get('/{url}',
    //  function($url) use($app) { return $app['pages.controller']->pageAction($url); }
    //)->bind('page');

    return $controllers;
  }

  /**
   * Registers page fixtures.
   *
   * @param  none
   * @return string[]
   */
  public function getFixtureClasses()
  {
    return array('AnthemCM\\Pages\\Fixtures\\PageFixtures');
  }
}
