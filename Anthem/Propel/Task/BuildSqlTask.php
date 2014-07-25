<?php

namespace Anthem\Propel\Task;

use Silex\Application;
use Anthem\Core\CoreModule;
use Anthem\Core\Task\TaskInterface;
use Anthem\Core\Task\TaskException;
use Anthem\Propel\Task\PropelGenWrapper;

/**
 * Generates SQL from all schemas.
 */
class BuildSqlTask extends PropelGenWrapper implements TaskInterface
{
  /**
   * Gets a short (one line) help string.
   *
   * @return   string
   */
  function getShortHelp()
  {
    return 'Generates SQL from all schemas.';
  }

  /**
   * Gets a long (multiline) help string.
   *
   * @return   string
   */
  function getLongHelp()
  {
    return <<<EOT
Usage: propel:build-sql

This task generates SQL from all src/SomeModule/schema.xml files.
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
    system('./bin/propel-gen "' . $this->getTempProjectDir() . '" sql', $retval);

    if (!$retval)
      echo 'Generated SQL for modules: ' . implode(', ', $this->valid_modules) . '.' . PHP_EOL;
    else
      echo 'Failed to generate SQL for modules: ' . implode(', ', $this->valid_modules) . '.' . PHP_EOL;

    $this->removeTempProjectDir();

    // Merge generated SQL into one file
    // Prologue/epilogue is generated for MySQL only
    $fkey_off = '';
    $fkey_on = '';
    if ($app['Propel']['datasources']['default']['adapter'] == 'mysql')
    {
      $fkey_off = <<<EOT
-- This is a fix for InnoDB in MySQL >= 4.1.x
-- It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;
EOT;
      $fkey_on = <<<EOT
-- This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
EOT;
    }

    $merged_sql  = '-- ' . CoreModule::FRAMEWORK_NAME;
    if (isset($app['Core']['project']))
      $merged_sql .= ' configured for ' . $app['Core']['project'] . ' project';
    $merged_sql .= PHP_EOL . '-- SQL file merged for modules: ' . implode(', ', $this->valid_modules) . PHP_EOL . PHP_EOL;

    $merged_sql .= $fkey_off . PHP_EOL;

    foreach (glob('SQL/*.schema.sql') as $sql)
    {
      $sql = file_get_contents($sql);
      foreach (explode("\n", $fkey_off) as $line)
        $sql = str_replace(trim($line), '', $sql);
      foreach (explode("\n", $fkey_on) as $line)
        $sql = str_replace(trim($line), '', $sql);
      $merged_sql .= $sql;
    }

    $merged_sql .= $fkey_on . PHP_EOL;

    file_put_contents('SQL/schema.sql', $merged_sql);

    return $retval;
  }
}
