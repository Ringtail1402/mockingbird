<?php

namespace Anthem\Core\Task;

use Silex\Application;

/**
 * An interface for task classes, suitable for complex tasks.
 */
interface TaskInterface
{
 /**
  * Gets a short (one line) help string.
  *
  * @abstract
  * @return   string
  */
  function getShortHelp();

 /**
  * Gets a long (multiline) help string.
  *
  * @abstract
  * @return   string
  */
  function getLongHelp();

 /**
  * Runs task.
  *
  * @abstract
  * @param    \Silex\Application $app
  * @param    string[]           $args
  * @return   integer
  */
  function run(Application $app, array $args);
}
