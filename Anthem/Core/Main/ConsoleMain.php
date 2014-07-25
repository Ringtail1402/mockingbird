<?php

namespace Anthem\Core\Main;

require_once __DIR__ . '/../Core.php';

use Anthem\Core\Core;
use Anthem\Core\Task\TaskException;

/**
 * Main Anthem class for CLI
 */
class ConsoleMain
{
 /**
  * Main function for Anthem CLI.
  *
  * @method main
  * @param  integer $argc
  * @param  array   $argv
  * @return integer
  */
  static function main($argc, array $argv)
  {
    $app = new Core();
    $app['core.task_dispatcher']->registerAllTasks($app);
    if (!empty($app['Core']['l10n'])) $app['Core']['modules_loaded']['Anthem/Core']->initL10n($app);

    if ($argc < 2 || in_array($argv[1], array('--help', '-h', '-?', '/?')))
      die('Usage: ' . $argv[0] . ' task [arguments...]' . PHP_EOL .
          'Use \'' . $argv[0] . ' help\' to view available tasks.' . PHP_EOL);

    $app['script_name'] = array_shift($argv);
    $task = array_shift($argv);

    try
    {
      return $app['core.task_dispatcher']->runTask($app, $task, $argv);
    }
    catch (TaskException $e)
    {
      fprintf(STDERR, $app['script_name'] . ': ' . $e->getMessage() . PHP_EOL);
      return 1;
    }
  }
}