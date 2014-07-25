<?php

namespace Anthem\Core;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

/**
 * Error handlers setup.
 */
class CoreErrorHandlers
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
   * Registers 404 error handlers.
   *
   * @param string $template
   * @return void
   */
  public function register404Handlers($template = 'Anthem/Core:404.php')
  {
    if ($this->app['debug']) return;
    $self = $this;
    $app  = $this->app;
    $this->app->error(function(\Exception $e, $code) use ($app, $self, $template) {
      if (!in_array($code, array(400, 404, 405, 410))) return;
      $self->logException($app, $e, $code, false);
      return new Response($app['core.view']->render($template), $code);
    });
  }

  /**
   * Registers 403 error handlers.
   *
   * @param string $template
   * @return void
   */
  public function register403Handlers($template = 'Anthem/Core:403.php')
  {
    if ($this->app['debug']) return;
    $self = $this;
    $app  = $this->app;
    $this->app->error(function(\Exception $e, $code) use ($app, $self, $template) {
      if (!in_array($code, array(403))) return;
      $self->logException($app, $e, $code, false);
      return new Response($app['core.view']->render($template), $code);
    });
  }

  /**
   * Registers 500 and other fatal error handlers.
   *
   * @return void
   */
  public function registerFatalHandlers()
  {
    if ($this->app['debug']) return;
    $self = $this;
    $app  = $this->app;
    $this->app->error(function(\Exception $e, $code) use ($app, $self) {
      $self->logException($app, $e, $code);

      // Very minimal page for 500/other errors.  Avoid using Anthem templating engine.
      $file = __DIR__ . '/../../Templates/500.php';
      if (!file_exists($file))
        $file = __DIR__ . '/Templates/500.php';
      ob_start();
      require $file;
      return new Response(ob_get_clean(), 500);
    });
  }

  /**
   * Logs an exception via monolog.
   *
   * @param \Silex\Application $app
   * @param \Exception $e
   * @param integer $code
   * @param boolean $full_dump
   * @return void
   */
  public function logException($app, \Exception $e, $code, $full_dump = true)
  {
    if (isset($app['monolog']))
    {
      if ($full_dump)
      {
        $app['monolog']->addError(sprintf('=8<=============== ERROR %d ===============8<=', $code));
        $app['monolog']->addError(sprintf('>>> Exception %s, message: %s', get_class($e), $e->getMessage()));
        $app['monolog']->addError(sprintf('>>> At %s:%d.  Backtrace:' .PHP_EOL. '%s', $e->getFile(), $e->getLine(), $e->getTraceAsString()));
        ob_start();
        echo '>>> Request parameters:' . PHP_EOL;
        echo '>>> $_GET: ';
        print_r($_GET);
        echo '>>> $_POST: ';
        print_r($_POST);
        echo '>>> $_SERVER: ';
        print_r($_SERVER);
        echo '>>> $_COOKIE: ';
        print_r($_COOKIE);
        echo '>>> $_SESSION: ';
        print_r($_SESSION);
        $app['monolog']->addError(ob_get_clean());
        $app['monolog']->addError(sprintf('=>8=============== END ERROR ===============>8='));
      }
      else
        $app['monolog']->addError(sprintf('=== ERROR %d.  Exception %s, message: %s', $code, get_class($e), $e->getMessage()));
    }
  }
}