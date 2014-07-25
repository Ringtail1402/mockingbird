<?php

namespace Anthem\Propel\Task;

use Silex\Application;
use \Propel;
use Anthem\Core\Task\TaskInterface;
use Anthem\Core\Task\TaskException;
use Anthem\Propel\Fixtures\FixtureInterface;
use Anthem\Propel\Fixtures\FixtureProviderInterface;

/**
 * Loads fixture definitions in database.
 */
class LoadFixturesTask implements TaskInterface
{
  /**
   * Gets a short (one line) help string.
   *
   * @return   string
   */
  function getShortHelp()
  {
    return 'Loads fixture definitions in database.';
  }

  /**
   * Gets a long (multiline) help string.
   *
   * @return   string
   */
  function getLongHelp()
  {
    return <<<EOT
Usage: propel:load-fixtures [module1 module2 ...]

This task loads fixture definitions into database.  Fixtures must go into
src/SomeModule/Fixtures directory.  By default fixtures from all modules are
loaded, but you can specify modules to look for on command line.  Note that
fixtures loading doesn't re-create tables and doesn't automatically clear them
(although fixtures may do that in code).
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
    if (count($args))
      $modules = $args;
    else
      $modules = $app['Core']['modules'];

    $valid_modules = array();
    $fixtures = array();

    // Walk through modules and locate fixtures
    foreach ($modules as $module_name)
    {
      if (!isset($app['Core']['modules_loaded'][$module_name]))
        throw new TaskException('Module \'' . $module_name . '\' does not exist or is not enabled.');
      $module = $app['Core']['modules_loaded'][$module_name];
      if (!$module instanceof FixtureProviderInterface) continue;

      foreach ($module->getFixtureClasses() as $fixture_class)
      {
        if (!class_exists($fixture_class))
          throw new TaskException('Fixture class \'' . $fixture_class . '\' does not exist.');
        $fixture = new $fixture_class();
        if (!$fixture instanceof FixtureInterface)
          throw new TaskException('Class \'' . $fixture_class . '\' is not a fixture class.');

        if (!isset($fixtures[$fixture->getPriority()]))
          $fixtures[$fixture->getPriority()] = array();
        $fixtures[$fixture->getPriority()][] = $fixture;
        $valid_modules[$module_name] = true;
      }
    }

    // Load fixtures
    krsort($fixtures);
    $references = array();
    Propel::getConnection()->beginTransaction();
    $count = 0;
    try
    {
      foreach ($fixtures as $priority => $_fixtures)
      {
        foreach ($_fixtures as $fixture)
        {
          $fixture->load($app, $references);
          $count++;
        }
      }
      Propel::getConnection()->commit();
    }
    catch (\Exception $e)
    {
      Propel::getConnection()->rollback();
      throw $e;
    }

    echo $count . ' fixture(s) loaded from modules: ' .
         implode(', ', array_keys($valid_modules)) . '.' . PHP_EOL;
    return 0;
  }
}
