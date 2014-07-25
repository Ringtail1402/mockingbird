<?php

namespace Anthem\Cron;

use Silex\Application;
use Anthem\Core\CoreModule;
use Anthem\Cron\CronProviderInterface;

/**
 * Registers and runs cron tasks.
 */
class CronDispatcher
{
  /**
   * @var Application
   */
  protected $app;

  /**
   * @var array Cron tasks.
   */
  protected $crons = array();

  /**
   * The constructor.  Registers cron tasks.
   *
   * @param Application $app
   */
  public function __construct(Application $app)
  {
    $this->app = $app;
    foreach ($app['Core']['modules_loaded'] as $module)
    {
      if ($module instanceof CronProviderInterface)
      {
        $this->crons = array_merge_recursive($this->crons, $module->getCronTasks($app));
      }
    }
  }

  /**
   * Runs a task.
   *
   * @param  Application $app
   * @param  string      $taskname
   * @param  array       $args
   * @return integer
   */
  public function runCronTasks($time)
  {
    if (!isset($this->crons[$time])) return;  // no tasks for this time

    foreach ($this->crons[$time] as $id => $cron)
    {
      try
      {
        $cron();
      }
      catch (\Exception $e)
      {
        fprintf(STDERR, '%s: %s' . PHP_EOL, $id, $e->getMessage());
        if (!empty($this->app['debug'])) throw $e;
      }
    }
  }

  /**
   * Lists registered cron tasks on stdout.
   *
   * @return void
   */
  public function listCronTasks()
  {
    $project_name = CoreModule::FRAMEWORK_NAME;
    if (isset($this->app['Core']['project']))
      $project_name .= ' configured for ' . $this->app['Core']['project'] . ' project';
    echo 'This is ' . $project_name . '.  The following cron tasks are available:' . PHP_EOL;

    foreach ($this->crons as $time => $crons)
    {
      echo PHP_EOL . $time . ':' . PHP_EOL;
      foreach ($crons as $id => $cron)
        echo ' - ' . $id . PHP_EOL;
    }
  }
}