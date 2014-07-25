<?php

namespace Anthem\Core;

require_once __DIR__.'/lib/silex.phar';
require_once __DIR__.'/lib/monolog.phar';
require_once __DIR__.'/lib/swiftmailer.phar';
require_once __DIR__.'/global.php';

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Kernel class for Anthem.  Basically a Silex\Application extended with initialization and some useful methods.
 */
class Core extends BaseApplication
{
  /**
   * Initializes application object.
   *
   * @throws \InvalidArgumentException
   */
  public function __construct()
  {
    // Instance application
    parent::__construct();

    // Load configs
    $config = $this->loadConfigs();
    foreach ($config as $key => $value) $this[$key] = $value;

    // All sources will live in root dir
    if (!isset($this['Core']['root_dir']))
      throw new \InvalidArgumentException('$config[\'Core\'][\'root_dir\'] option must be set.');
    $this['autoloader']->registerNamespaceFallbacks(array($this['Core']['root_dir']));
    $this['autoloader']->registerPrefixFallbacks(array($this['Core']['root_dir']));

    // Walk through modules
    if (!is_array($this['Core']['modules']))
      throw new \InvalidArgumentException('$config[\'Core\'][\'modules\'] option must be set.');
    $modules = array();
    foreach ($this['Core']['modules'] as $module_name)
    {
      // Walk through Foo\FooModule classes
      $tmp = explode('/', $module_name);
      $lastname = $tmp[count($tmp) - 1];
      $class  = str_replace('/', '\\', $module_name).'\\'.$lastname.'Module';
      if (class_exists($class))
      {
        // Instance and initialize module
        $module = new $class($this);
        // Register services
        if ($module instanceof ServiceProviderInterface)
        {
          $this->register($module, $config);
        }
        // Register controllers
        if ($module instanceof ControllerProviderInterface)
        {
          $this->mount('', $module);
        }
        // Register events
        if (!empty($this[$lastname]) && !empty($this[$lastname]['handlers']))
          foreach ($this[$lastname]['handlers'] as $event => $handlers)
            foreach ($handlers as $handler)
              $this->on($event, $handler);
        $modules[$module_name] = $module;
      }
    }

    // Load global module, if any
    if (class_exists('\App'))
    {
      $appmodule = new \App($this);
      if ($appmodule instanceof ServiceProviderInterface)    $this->register($appmodule, $config);
      if ($appmodule instanceof ControllerProviderInterface) $this->mount('', $appmodule);
      $modules['_'] = $appmodule;
    }

    $core = $this['Core'];
    $core['modules_loaded'] = $modules;
    $this['Core'] = $core;
  }

  /**
   * Loads all config.inc files, first for modules in order they are defined, then for the application.
   * This assumes localconfig.inc has already been loaded.
   *
   * @return array
   * @throws \InvalidArgumentException
   */
  public function loadConfigs()
  {
    // User-configurable config
    global $localconfig;
    if (!is_array($localconfig['modules']))
      throw new \InvalidArgumentException('$localconfig[\'modules\'] option must be set.');

    // Module configs
    $config = array();
    foreach ($localconfig['modules'] as $module)
      if (file_exists(__DIR__ . '/../../' . $module . '/config.inc'))
        require_once (__DIR__ . '/../../' . $module . '/config.inc');

    // App config
    if (file_exists(__DIR__ . '/../../config.inc'))
      require_once(__DIR__ . '/../../config.inc');

    return $config;
  }

  /**
   * Allows lazy binding of events to services, so that a service will get instanteated on the fly
   * upon issuing of the event.
   *
   * @param string $eventName
   * @param string $service
   * @param int $priority
   * @return void
   */
  public function on($eventName, $service, $priority = 0)
  {
    $self = $this;
    $this['dispatcher']->addListener($eventName, function (Event $event) use ($eventName, $self, $service) {
      /** @var EventSubscriberInterface $subscriber */
      $subscriber = $self[$service];
      foreach ($subscriber->getSubscribedEvents() as $_eventName => $params)
      {
        if ($_eventName != $eventName) continue;
        if (is_string($params))
          call_user_func(array($subscriber, $params), $event);
        elseif (is_string($params[0]))
          call_user_func(array($subscriber, $params[0]), $event);
        else
        {
          foreach ($params as $listener)
            call_user_func(array($subscriber, $listener[0]), $event);
        }
      }
    }, $priority);
  }

  /**
   * Return true if the application is running in "online" mode (Request/Response lifecycle),
   * false if in "offline" mode (no Request object, probably running as a CLI/cron task).
   *
   * @return boolean
   */
  public function isOnline()
  {
    return isset($this['request']);
  }

  /**
   * Renders a template.  Just a shortcut.
   *
   * @param string $template
   * @param array  $params
   * @return string
   */
  public function render($template, $params = array())
  {
    return $this['core.view']->render($template, $params);
  }
}