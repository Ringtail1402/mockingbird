<?php

namespace Anthem\Cron;

use Silex\Application;

/**
 * If a module implements this interface, it provides some cron tasks.
 */
interface CronProviderInterface
{
  /**
   * This function must return an array of tasks.  Cron tasks are simple functions with no arguments or return values.
   * They may output things on stderr, but should do so only if they really require user attention.
   * All thrown exception will be caught and displayed on stderr, and execution of other tasks will resume.
   *
   * This function should return an array with the following structure:
   *
   * array(
   *   'hourly' => array('task1' => function() { ... }, 'task2' => function() { ... }, ...),
   *   'daily' => array(...),
   *   ...
   * )
   *
   * @abstract
   * @param Application $app
   * @return array
   */
  public function getCronTasks(Application $app);
}
