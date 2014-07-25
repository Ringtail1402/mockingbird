<?php

namespace AnthemCM\Pages\Controller;

use Silex\Application;

/**
 * Pages front-end controller.
 */
class PageController
{
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
  * Displays a single page.
  *
  * @param  \AnthemCM\Pages\Model\Page|string $page
  * @return string
  */
  public function pageAction($page)
  {
    if (is_string($page))
      $page = $this->app['pages.model']->findOneByUrl($page);

    if (!$page)
      $this->app->abort(404, 'Page not found.');

    return $this->app['core.view']->render('AnthemCM/Pages:page.php', array(
      'page' => $page,
    ));
  }
}
