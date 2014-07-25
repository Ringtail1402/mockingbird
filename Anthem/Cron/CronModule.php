<?php

namespace Anthem\Cron;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Anthem\Core\Module;
use Anthem\Core\Task\TaskProviderInterface;
use Anthem\Cron\CronDispatcher;

/**
 * Cron module, unified cron task support.
 */
class CronModule extends Module implements ServiceProviderInterface,
                                           TaskProviderInterface
{
  /**
   * Registers cron dispatcher services.
   *
   * @param  Application $app
   * @return void
   */
  public function register(Application $app)
  {
    $app['cron.dispatcher'] = $app->share(function() use ($app) { return new CronDispatcher($app); });
  }

  /**
   * Returns registered tasks.
   *
   * @return array
   */
  public function getTasks()
  {
    return array(
      'cron:list' => array(
        'shorthelp' => 'Lists registered cron tasks.',
        'longhelp'  => 'Usage: cron:list' . "\n\n" . 'Lists all registered cron tasks.',
        'task'      => function ($app, $args) { $app['cron.dispatcher']->listCronTasks(); }
      ),
      'cron:hourly' => array(
        'shorthelp' => 'Runs hourly cron tasks.',
        'longhelp'  => 'Usage: cron:hourly' . "\n\n" . 'Runs all registered hourly cron tasks.',
        'task'      => function ($app, $args) { $app['cron.dispatcher']->runCronTasks('hourly'); }
      ),
      'cron:daily' => array(
        'shorthelp' => 'Runs daily cron tasks.',
        'longhelp'  => 'Usage: cron:daily' . "\n\n" . 'Runs all registered daily cron tasks.',
        'task'      => function ($app, $args) { $app['cron.dispatcher']->runCronTasks('daily'); }
      ),
      'cron:weekly' => array(
        'shorthelp' => 'Runs weekly cron tasks.',
        'longhelp'  => 'Usage: cron:weekly' . "\n\n" . 'Runs all registered weekly cron tasks.',
        'task'      => function ($app, $args) { $app['cron.dispatcher']->runCronTasks('weekly'); }
      ),
      'cron:monthly' => array(
        'shorthelp' => 'Runs monthly cron tasks.',
        'longhelp'  => 'Usage: cron:mothly' . "\n\n" . 'Runs all registered monthly cron tasks.',
        'task'      => function ($app, $args) { $app['cron.dispatcher']->runCronTasks('monthly'); }
      ),
      'cron:custom' => array(
        'shorthelp' => 'Runs cron tasks set up to run at custom time.',
        'longhelp'  => 'Usage: cron:custom time' . "\n\n" . 'Runs all cron tasks registered to run at specified time.',
        'task'      => function ($app, $args) {
          if (count($args) < 1)
            throw new TaskException('Missing time argument.');
          $app['cron.dispatcher']->runCronTasks($args[0]);
        }
      ),

    );
  }
}
