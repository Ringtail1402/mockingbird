<?php

namespace Anthem\Core\View;

use Silex\Application;
use Anthem\Core\View\ViewEngineInterface;
use Anthem\Core\View\HelperProviderInterface;

/**
 * This is the basic view engine, using simple PHP templates.
 */
class PHPViewEngine implements ViewEngineInterface
{
 /**
  * @var \Silex\Application
  */
  protected $app;

 /**
  * @var object[string] Helpers.
  */
  protected $helpers = array();

 /**
  * The constructor.  Loads any registered helpers.
  *
  * @param \Silex\Application $app
  */
  public function __construct(Application $app)
  {
    $this->app = $app;

    foreach ($app['Core']['modules_loaded'] as $module)
    {
      if ($module instanceof HelperProviderInterface)
        $this->helpers = array_merge($this->helpers, $module->getHelpers($app));
    }
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
    if (!is_readable($template))
      throw new \InvalidArgumentException('View template file \'' . $template . '\' not found.');

    $app = $this->app;
    extract($this->helpers);
    extract($params);

    ob_start();
    ob_implicit_flush(0);
    try
    {
      require ($template);
    }
    catch (\Exception $e)
    {
      ob_end_clean();
      throw $e;
    }

    return ob_get_clean();
  }
}