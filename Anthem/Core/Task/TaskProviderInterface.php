<?php

namespace Anthem\Core\Task;

/**
 * If a module implements this interface, it provides some console tasks.
 */
interface TaskProviderInterface
{
 /**
  * This function must return an array of tasks.  Tasks are functions which
  * have the following signature:
  *
  *   function task(Application $app, array $args);
  *
  * Tasks may output things directly on stdout/stderr.  Tasks should return
  * an exit status (0 = success, anything else = failure).  (No return value
  * is treated as success.
  *
  * This function should return an array with the following structure:
  *
  * array(
  *   'task1' => function($app, $args) { ... },  // No help, not recommended
  *   'task2' => array(
  *     'shorthelp' => 'Task 2',
  *     'longhelp'  => 'This is a task that does blah blah blah, usage blah blah blah.',
  *     'task'      => function($app, $args) { ... }
  *   ),
  *   'task3' => 'SomeModule\\SomeTask',  // SomeTask is a class implementing TaskInterface
  * )
  *
  * @abstract
  * @return array
  */
  function getTasks();
}
