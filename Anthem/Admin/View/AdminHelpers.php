<?php

namespace Anthem\Admin\View;

use Silex\Application;

/**
 * Admin interface-specific helpers.
 */
class AdminHelpers
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
  * Returns current page service name.
  *
  * @return string
  */
  public function getActivePageName()
  {
    return $this->app['admin.controller']->getActivePageName();
  }

 /**
  * Generates URL for an AJAX function in current page.
  *
  * @param  string $function
  * @param  array $params
  * @return string
  */
  public function ajax($function, $params = array())
  {
    return $this->app['url_generator']->generate('admin.ajax', array_merge($params, array(
      'page' => $this->app['admin.controller']->getActivePageName(),
      'function' => $function,
    )));
  }

 /**
  * Returns template for the specified part of admin page.
  *
  * @param  string $template
  * @return string
  */
  public function getTemplate($template)
  {
    return $this->app['admin.controller']->getActivePage()->getTemplate($template);
  }

 /**
  * Checks if link/action is enabled for this object.
  *
  * @param  object $object
  * @param  array  $options
  * @return boolean
  */
  public function testLinkOrAction($object, array $options)
  {
    // Default is enabled
    if (!isset($options['test'])) return true;

    return $options['test']($object);
  }

 /**
  * Returns pages which will have links to them displayed.  0 corresponds to ellipsis.
  *
  * @param  integer $page
  * @param  integer $total_pages
  * @return integer[]
  */
  public function getPagerLinks($page, $total_pages)
  {
    if ($total_pages <= 11)
    {
      return range(1, $total_pages);
    }
    elseif ($page <= 6)
    {
      $result = range(1, $page + 2);
      $result[] = 0;
      $result = array_merge($result, range($total_pages - 2, $total_pages));
      return $result;
    }
    elseif ($total_pages - $page < 6)
    {
      $result = range(1, 3);
      $result[] = 0;
      $result = array_merge($result, range($page - 2, $total_pages));
      return $result;
    }
    else
    {
      $result = range(1, 3);
      $result[] = 0;
      $result = array_merge($result, range($page - 2, $page + 2));
      $result[] = 0;
      $result = array_merge($result, range($total_pages - 2, $total_pages));
      return $result;
    }
  }
}