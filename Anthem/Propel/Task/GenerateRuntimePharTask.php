<?php

namespace Anthem\Propel\Task;

use Phar;
use Silex\Application;
use Anthem\Core\Task\TaskInterface;
use Anthem\Core\Task\TaskException;

/**
 * A service task generating propel-runtime.phar archive.
 */
class GenerateRuntimePharTask implements TaskInterface
{
  /**
   * Gets a short (one line) help string.
   *
   * @return   string
   */
  function getShortHelp()
  {
    return 'Generates a propel-runtime.phar archive with Propel library.';
  }

  /**
   * Gets a long (multiline) help string.
   *
   * @return   string
   */
  function getLongHelp()
  {
    return <<<EOT
Usage: propel:generate-runtime-phar propel_base_dir

This task generates a propel-runtime.phar archive from Propel files.
PropelModule uses a .phar file for Propel library, keeping the number of
files in project down.
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
    if (count($args) < 1)
      throw new TaskException('Missing propel base dir argument.');

    try
    {
      $app['core.phar_generator']->generate($args[0], 'runtime/lib', 'Propel.php', realpath(__DIR__ . '/../lib/'), 'propel-runtime.phar', 'Propel.php');
    }
    catch (\RuntimeException $e)
    {
      throw new TaskException($e->getMessage());
    }
    echo 'propel-runtime.phar created successfully.' . PHP_EOL;
    return 0;
  }
}

