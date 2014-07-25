<?php

namespace Anthem\Admin;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Anthem\Core\Module;
use Anthem\Core\View\HelperProviderInterface;
use Anthem\Admin\Controller\AdminController;
use Anthem\Admin\Controller\ColumnEditController;
use Anthem\Admin\View\AdminHelpers;
use Anthem\Admin\Admin\TableColumn\ColumnFactory;


/**
 * An admin interface framework module.
 */
class AdminModule extends Module implements ServiceProviderInterface,
                                            ControllerProviderInterface,
                                            HelperProviderInterface
{
  /**
   * Registers admin services.
   *
   * @param  Application $app
   * @return void
   */
  function register(Application $app)
  {
    $app['admin.controller'] = $app->share(function() use ($app) { return new AdminController($app); });
    $app['admin.column_edit.controller'] = $app->share(function() use ($app) { return new ColumnEditController($app); });
    $app['admin.view.admin'] = $app->share(function() use ($app) { return new AdminHelpers($app); });
    $app['admin.table.column_factory'] = $app->share(function() use ($app) { return new ColumnFactory($app); });
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

    $controllers->get('/admin',
      function(Request $request) use ($app)       { return $app['admin.controller']->indexAction($request); }
    )->bind('admin');
    $controllers->get('/admin/{page}',
      function(Request $request, $page) use($app) { return $app['admin.controller']->indexAction($request, $page); }
    )->bind('admin.page');
    $controllers->match('/admin/{page}/ajax/{function}',
      function(Request $request, $page, $function) use($app) {
        return $app['admin.controller']->ajaxFunctionAction($request, $page, $function);
      }
    )->bind('admin.ajax');

    $controllers->post('/admin/{page}/ajax/column/update',
      function(Request $request, $page) use($app) { return $app['admin.column_edit.controller']->updateAction($request, $page); }
    )->bind('admin.column_edit.update');

    return $controllers;
  }

 /**
  * Returns helpers.
  *
  * @return array
  */
  public function getHelpers(Application $app)
  {
    return array(
      'admin' => $app['admin.view.admin']
    );
  }
}
