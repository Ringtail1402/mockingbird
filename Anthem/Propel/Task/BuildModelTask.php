<?php

namespace Anthem\Propel\Task;

use Silex\Application;
use Anthem\Core\Task\TaskInterface;
use Anthem\Core\Task\TaskException;
use Anthem\Propel\Task\PropelGenWrapper;

/**
 * Generates base model (om) classes.
 */
class BuildModelTask extends PropelGenWrapper implements TaskInterface
{
  /**
   * Gets a short (one line) help string.
   *
   * @return   string
   */
  function getShortHelp()
  {
    return 'Generates base model (om) classes.';
  }

  /**
   * Gets a long (multiline) help string.
   *
   * @return   string
   */
  function getLongHelp()
  {
    return <<<EOT
Usage: propel:build-model

This task generates base Propel model classes from src/SomeModule/schema.xml
files in src/SomeModule/Model dirs.
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
    $this->createTempProjectDir($app);

    chdir($app['Core']['root_dir']);
    system('./bin/propel-gen "' . $this->getTempProjectDir() . '" om', $retval);

    if (!$retval)
      echo 'Generated model classes for modules: ' . implode(', ', $this->valid_modules) . '.' . PHP_EOL;
    else
      echo 'Failed to generate model classes for modules: ' . implode(', ', $this->valid_modules) . '.' . PHP_EOL;

    $this->removeTempProjectDir();
    return $retval;
  }
}

