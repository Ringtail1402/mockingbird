<?php

namespace Anthem\Core\Task;

use Phar;
use Silex\Application;
use Anthem\Core\Task\TaskInterface;
use Anthem\Core\Task\TaskException;

/**
 * A service task generating monolog.phar archive.
 */
class GenerateMonologPharTask implements TaskInterface
{
  /**
   * Gets a short (one line) help string.
   *
   * @return   string
   */
  function getShortHelp()
  {
    return 'Generates a monolog.phar archive with Monolog library.';
  }

  /**
   * Gets a long (multiline) help string.
   *
   * @return   string
   */
  function getLongHelp()
  {
    return <<<EOT
Usage: core:generate-monolog-phar monolog_base_dir

This task generates a monolog.phar archive from Monolog files.
CoreModule uses a .phar file for Monolog library, keeping the number of
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
      throw new TaskException('Missing monolog base dir argument.');

    try
    {
      $app['core.phar_generator']->generate($args[0], 'src', 'Monolog/Logger.php', realpath(__DIR__ . '/../lib/'), 'monolog.phar');
    }
    catch (\RuntimeException $e)
    {
      throw new TaskException($e->getMessage());
    }
    echo 'monolog.phar created successfully.' . PHP_EOL;
    return 0;
  }
}

