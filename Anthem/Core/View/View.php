<?php

namespace Anthem\Core\View;

use Silex\Application;
use Anthem\Core\View\ViewEngineInterface;

/**
 * This class renders a template, deciding which view engine to use.
 */
class View implements ViewEngineInterface
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
  * Renders a template.
  *
  * @param  string $template
  * @param  array  $params
  * @return string
  * @throws \LogicException
  */
  public function render($template, $params = array())
  {
    $template_type = pathinfo($template, PATHINFO_EXTENSION);
    if (!isset($this->app['Core']['view_engines'][$template_type]))
      throw new \LogicException('Unknown view engine for template \'' . $template . '\'.');

    // Allow override
    $template_file = $this->app['Core']['root_dir'] . '/Templates/' . str_replace(':', '/', $template);
    if (!is_readable($template_file))
      $template_file = $this->app['Core']['root_dir'] . '/' . str_replace(':', '/Templates/', $template);
    $view_engine = $this->app[$this->app['Core']['view_engines'][$template_type]];
    return $view_engine->render($template_file, $params);
  }
}