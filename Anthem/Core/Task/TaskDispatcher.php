<?php

namespace Anthem\Core\Task;

use Silex\Application;
use Anthem\Core\CoreModule;
use Anthem\Core\Task\TaskInterface;
use Anthem\Core\Task\TaskException;

/**
 * Registers and runs console tasks.
 */
class TaskDispatcher
{
 /**
  * @var array[string] Tasks.
  */
  protected $tasks = array();

 /**
  * Adds tasks frim all loaded modules to registered tasks.
  *
  * @param  \Silex\Application $app
  * @return void
  */
  public function registerAllTasks(Application $app)
  {
    foreach ($app['Core']['modules_loaded'] as $module)
    {
      if ($module instanceof TaskProviderInterface)
      {
        $this->registerTasks($module);
      }
    }
  }

 /**
  * Adds task from module to registered tasks.
  *
  * @param  TaskProviderInterface $module
  * @return void
  */
  protected function registerTasks(TaskProviderInterface $module)
  {
    $this->tasks = array_merge($this->tasks, $module->getTasks());
  }

 /**
  * Runs a task.
  *
  * @param  Application $app
  * @param  string      $taskname
  * @param  array       $args
  * @return integer
  */
  public function runTask($app, $taskname, array $args)
  {
    if (!isset($this->tasks[$taskname]))
      throw new TaskException('Unknown task: \'' . $taskname . '\'.');
    $task = $this->tasks[$taskname];
    if (is_string($task)) $task = new $task();

    // Handle help
    if (count($args) && strtolower($args[0]) == 'help')
    {
      if (is_object($task) && $task instanceof TaskInterface)
        echo $task->getLongHelp();
      elseif (is_array($task) && isset($task['longhelp']))
        echo $task['longhelp'];
      else
        echo 'Sorry, no help available for task \'' . $taskname . '\'.';
      return 1;
    }

    // Get the task callable
    if (is_object($task) && $task instanceof TaskInterface)
    {
      return (integer) $task->run($app, $args);
    }
    if (is_array($task))
    {
      if (!isset($task['task']) || !is_callable($task['task']))
        throw new TaskException('No callable provided for task \'' . $taskname . '\'.');
      $task = $task['task'];
    }

    return (integer) $task($app, $args);
  }

 /**
  * Lists registered tasks on stdout.
  *
  * @param  Application $app
  * @return void
  */
  public function listTasksTask(Application $app)
  {
    $project_name = CoreModule::FRAMEWORK_NAME;
    if (isset($app['Core']['project']))
      $project_name .= ' configured for ' . $app['Core']['project'] . ' project';
    echo 'This is ' . $project_name . '.  The following console tasks are available:' . PHP_EOL;

    foreach ($this->tasks as $taskname => $task)
    {
      if (is_string($task)) $task = new $task();
      if (is_object($task) && $task instanceof TaskInterface)
        echo ' - ' . sprintf('%-30s', $taskname) . ' ' . $task->getShortHelp() . PHP_EOL;
      elseif (is_array($task) && isset($task['shorthelp']))
        echo ' - ' . sprintf('%-30s', $taskname) . ' ' . $task['shorthelp'] . PHP_EOL;
      else
        echo ' - ' . $taskname . PHP_EOL;
    }

    echo 'Use \'task help\' to get extended help for a task.' . PHP_EOL;
  }
}