<?php

namespace Anthem\Admin\Controller;

use Silex\Application;
use Anthem\Admin\Admin\AdminPageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Main admin controller.
 */
class AdminController
{
 /**
  * @var \Silex\Application
  */
  protected $app;

 /**
  * @var string Active page service name.
  */
  protected $active_page = null;

 /**
  * The constructor.
  *
  * @param \Silex\Application $app
  */
  public function __construct(Application $app)
  {
    $this->app = $app;

    // Require authorization
    if (!empty($app['Auth']['enable'])) $app['auth']->checkAuthorization();
  }

 /**
  * Main admin action.
  *
  * @param  Request $request
  * @param  string $page
  * @return string
  */
  public function indexAction(Request $request, $page = null)
  {
    if (!$page)
      $page = $this->app['Admin']['default'];
    $this->active_page = $page;

    return $this->app[$page]->render($request);
  }

  /**
   * AJAX function action.  Function are provided by admin page services.
   *
   * @param  Request $request
   * @param  string  $page
   * @param  string  $function
   * @return string
   */
   public function ajaxFunctionAction(Request $request, $page, $function)
   {
     $this->active_page = $page;
     $function = $function . 'Ajax';
     if (method_exists($this->app[$page], $function))
      return $this->app[$page]->$function($request);
     throw new NotFoundHttpException('Unknown AJAX function: \'' . $function . '\'.');
   }

 /**
  * Returns active page service name.
  *
  * @return string
  */
  public function getActivePageName()
  {
    return $this->active_page;
  }

 /**
  * Returns active page service.
  *
  * @return \Anthem\Admin\Admin\AdminPageInterface
  */
  public function getActivePage()
  {
    if (!$this->active_page) return null;

    return $this->app[$this->active_page];
  }
}