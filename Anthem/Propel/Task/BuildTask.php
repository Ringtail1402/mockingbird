<?php

namespace Anthem\Propel\Task;

use Silex\Application;
use Anthem\Core\Task\TaskInterface;
use Anthem\Core\Task\TaskException;
use Anthem\Propel\Task\BuildModelTask;
use Anthem\Propel\Task\BuildSqlTask;

/**
 * Generates both model classes and SQL
 */
class BuildTask implements TaskInterface
{
  /**
   * Gets a short (one line) help string.
   *
   * @return   string
   */
  function getShortHelp()
  {
    return 'Generates both SQL and model classes.';
  }

  /**
   * Gets a long (multiline) help string.
   *
   * @return   string
   */
  function getLongHelp()
  {
    return <<<EOT
Usage: propel:build

This task generates both SQL schema in SQL/* dir and model classes in */Model/om, */Model/map dirs.
EOT;
  }

  /**
   * Runs task.
   *
   * @param    \Silex\Application $app
   * @param    string[]           $args
   * @return   integer
   */
  public function run(Application $app, array $args)
  {
    $modeltask = new BuildModelTask();
    $sqltask = new BuildSqlTask();
    return !$modeltask->run($app, $args) && !$sqltask->run($app, $args);
  }
}

