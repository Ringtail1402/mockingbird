<?php

namespace Anthem\Core\Task;

use Phar;
use Silex\Application;
use Anthem\Core\Task\TaskInterface;
use Anthem\Core\Task\TaskException;

/**
 * A service task generating swiftmailer.phar archive.
 */
class GenerateSwiftmailerPharTask implements TaskInterface
{
  /**
   * Gets a short (one line) help string.
   *
   * @return   string
   */
  function getShortHelp()
  {
    return 'Generates a swiftmailer.phar archive with SwiftMailer library.';
  }

  /**
   * Gets a long (multiline) help string.
   *
   * @return   string
   */
  function getLongHelp()
  {
    return <<<EOT
Usage: core:generate-swiftmailer-phar swiftmailer_base_dir

This task generates a swiftmailer.phar archive from SwiftMailer files.
CoreModule uses a .phar file for SwiftMailer library, keeping the number of
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
      throw new TaskException('Missing swiftmailer base dir argument.');

    try
    {
      $app['core.phar_generator']->generate($args[0], 'lib', 'classes/Swift.php', realpath(__DIR__ . '/../lib/'), 'swiftmailer.phar', 'swift_required.php');
    }
    catch (\RuntimeException $e)
    {
      throw new TaskException($e->getMessage());
    }
    echo 'swiftmailer.phar created successfully.' . PHP_EOL;
    return 0;
  }
}

