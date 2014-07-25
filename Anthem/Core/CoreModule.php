<?php

namespace Anthem\Core;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Anthem\Core\Task\TaskProviderInterface;
use Anthem\Core\CoreErrorHandlers;
use Anthem\Core\Task\TaskDispatcher;
use Anthem\Core\View\View;
use Anthem\Core\View\PHPViewEngine;
use Anthem\Core\View\HelperProviderInterface;
use Anthem\Core\View\ViewHelpers;
use Anthem\Core\View\AssetHelpers;
use Anthem\Core\View\LinkHelpers;
use Anthem\Core\CoreSettings;
use Anthem\Settings\SettingsProviderInterface;
use Anthem\Core\Util\PharGenerator;

/**
 * Core module.  Provides some basic functionality like console tasks.
 */
class CoreModule extends Module implements ServiceProviderInterface,
                                           ControllerProviderInterface,
                                           TaskProviderInterface,
                                           HelperProviderInterface,
                                           SettingsProviderInterface
{
  const FRAMEWORK_NAME = 'Anthem';

  /**
   * Registers core services.
   *
   * @param  Application $app
   * @return void
   */
  public function register(Application $app)
  {
    $self = $this;

    // Init Silex service providers
    $app->register(new SessionServiceProvider());
    $app->before(function ($request) use ($app, $self) {
        // TODO: start session and initialize L10n on demand
        $request->getSession()->start();
        if (!empty($app['Core']['l10n'])) $self->initL10n($app);
    });

    $app->register(new UrlGeneratorServiceProvider());

    // TODO: conditional loading
    if (!empty($app['Core']['mail.config']))
      $app['swiftmailer.options'] = $app['Core']['mail.config'];
    $app->register(new SwiftmailerServiceProvider());
    $app['mailer'];

    $app['core.error_handlers']  = $app->share(function() use($app) { return new CoreErrorHandlers($app); });
    $app['core.task_dispatcher'] = $app->share(function() { return new TaskDispatcher(); });
    $app['core.view']            = $app->share(function() use($app) { return new View($app); });
    $app['core.view.php_engine'] = $app->share(function() use($app) { return new PHPViewEngine($app); });
    $app['core.view.assets']     = $app->share(function() use($app) { return new AssetHelpers($app); });
    $app['core.phar_generator']  = $app->share(function() use($app) { return new PharGenerator(); });

    // Set up logging
    if ($app['Core']['log.file'])
    {
      $app['autoloader']->registerNamespaces(array(
          'Monolog' => 'phar://monolog.phar',
      ));
      $app->register(new MonologServiceProvider(), array(
        'monolog.logfile' => $app['Core']['log.file'],
        'monolog.level'   => !empty($app['Core']['log.level']) ? $app['Core']['log.level']
                                                               : ($app['debug'] ? \Monolog\Logger::DEBUG
                                                                                : \Monolog\Logger::ERROR),
        'monolog.name'    => $app['Core']['project']
      ));

    }
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

    $controllers->get('/ping', function () { return 'Pong!'; })->bind('_ping.ignorehttps');

    $controllers->get('/_js_l10n.js',
      function() use($app) {
        global $_tJS;
        return new Response($app['core.view']->render('Anthem/Core:_js_l10n.js.php', array('_t' => $_tJS)), 200,
          array('Content-Type' => 'text/javascript'));
      }
    )->bind('_l10n_js.ignorehttps');

    $controllers->get('/_iewarning',
      function () use ($app) { return $app['core.view']->render('Anthem/Core:iewarning.php'); }
    )->bind('_iewarning.ignorehttps');

    return $controllers;
  }

  /**
   * Returns core tasks.
   *
   * @param  none
   * @return array[string]
   */
  public function getTasks()
  {
    return array(
      'help' => array(
        'shorthelp' => 'Lists available tasks.',
        'longhelp'  => 'Usage: help

This tasks just lists all other available tasks.',
        'task'      => function(Application $app, array $args) { $app['core.task_dispatcher']->listTasksTask($app); }
      ),
      'core:install' => 'Anthem\\Core\\Task\\InstallTask',
      'core:generate-monolog-phar' => 'Anthem\\Core\\Task\\GenerateMonologPharTask',
      'core:generate-swiftmailer-phar' => 'Anthem\\Core\\Task\\GenerateSwiftmailerPharTask'
    );
  }

 /**
  * Returns core helpers.
  *
  * @param  \Silex\Application $app
  * @return object[string]
  */
  public function getHelpers(Application $app)
  {
    return array(
      'view'  => new ViewHelpers($app),
      'asset' => new AssetHelpers($app),
      'link'  => new LinkHelpers($app),
    );
  }

  /**
   * Returns core settings.
   *
   * @return SettingsInterface
   */
  public function getSettings()
  {
    return new CoreSettings();
  }


  /**
   * Initializes Anthem's simple localization mechanism.
   *
   * @param Application $app
   * @throws \InvalidArgumentException
   */
  public function initL10n(Application $app)
  {
    $language = null;
    if (isset($app['settings']))
      $language = $app['settings']->get('core.l10n.language');
    if (empty($language))
      $language = $app['Core']['l10n.default_language'];
    if (empty($language))
      throw new \InvalidArgumentException('Default language for l10n not defined.');
    $app['l10n.language'] = $language;

    global $_t, $_tJS;
    $_t = array();
    $_tJS = array();

    foreach ($app['Core']['modules'] as $module_name)
    {
      $langfile = $app['Core']['root_dir'] . '/' . $module_name . '/L10n/' . $language . '.php';
      if (is_readable($langfile)) require_once($langfile);
    }

    $langfile = $app['Core']['root_dir'] . '/L10n/' . $language . '.php';
    if (is_readable($langfile)) require_once($langfile);
  }
}
