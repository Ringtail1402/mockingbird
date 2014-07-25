<?php

namespace Anthem\Propel\Task;

use Silex\Application;
use Anthem\Core\Task\TaskException;

/**
 * Base class for Propel model/SQL generation tasks.  Manages temporary project dir creation
 */
abstract class PropelGenWrapper
{
 /**
  * @var string Temporary project dir.
  */
  private $tmpdir;

 /**
  * @var string[] Modules which have schemas defined.
  */
  protected $valid_modules = array();

  /**
   * Creates temporary project dir.  Copies over schemas, creates build.properties.
   *
   * @param  \Silex\Application $app
   * @return void
   */
  protected function createTempProjectDir(Application $app)
  {
    // Source and temporary dirs
    $srcdir = $app['Core']['root_dir'];
    $this->tmpdir = sys_get_temp_dir() . '/propel-anthem-' . uniqid();
    if (!mkdir($this->tmpdir))
      throw new TaskException('Failed to create temporary dir \'' . $this->tmpdir . '\'.');

    // Copy schema files to temporary dir
    foreach ($app['Core']['modules'] as $module)
    {
      $srcschema = $srcdir . '/' . $module . '/schema.xml';
      if (file_exists($srcschema))
      {
        copy($srcschema, $this->tmpdir. '/' . str_replace('/', '_', $module) . '-schema.xml');
        $this->valid_modules[] = $module;
      }
    }

    // Make build.properties
    // Output dirs and some fixed settings
    $props = '';
    $props .= 'propel.php.dir = ' . $app['Core']['root_dir'] . PHP_EOL;
    $props .= 'propel.sql.dir = ' . $app['Core']['root_dir'] . '/SQL'. PHP_EOL;
    $props .= 'propel.behavior.default = alternative_coding_standards' . PHP_EOL;
    $props .= 'propel.packageObjectModel = true' . PHP_EOL;
   /**
    * @todo Fix phpDoc package specification in generated files.
    */

    // Translate connection information
    $error_text = 'connection configuration ($app[\'Propel\'][\'datasources\'][\'default\'])';
    if (!isset($app['Propel']) ||
        !isset($app['Propel']['datasources']) ||
        !isset($app['Propel']['datasources']['default']))
      throw new TaskException('Missing ' . $error_text);
    $datasource_config = $app['Propel']['datasources']['default'];
    if (!isset($datasource_config['adapter']))
      throw new TaskException('Missing adapter parameter in ' . $error_text);
    $props .= 'propel.database = ' . $datasource_config['adapter'] . PHP_EOL;
    if (!isset($datasource_config['connection']) || !isset($datasource_config['connection']['dsn']))
          throw new TaskException('Missing connection DSN parameter in ' . $error_text);
    $props .= 'propel.database.url = ' . $datasource_config['connection']['dsn'] . PHP_EOL;
    if (isset($datasource_config['connection']['user']))
      $props .= 'propel.database.user = ' . $datasource_config['connection']['user'] . PHP_EOL;
    if (isset($datasource_config['connection']['password']))
      $props .= 'propel.database.password = ' . $datasource_config['connection']['password'] . PHP_EOL;
    if (!empty($datasource_config['tableType']))
      $props .= 'propel.mysql.tableType = ' . $datasource_config['tableType'] . PHP_EOL;

    file_put_contents($this->tmpdir . '/build.properties', $props);
  }

 /**
  * Returns temporary project dir.
  *
  * @return string
  */
  protected function getTempProjectDir()
  {
    return $this->tmpdir;
  }

 /**
  * Removes temporary project dir.
  *
  * @param  none
  * @return void
  */
  protected function removeTempProjectDir()
  {
    foreach (glob($this->tmpdir . '/*') as $file)
      @unlink($file);
    @rmdir($this->tmpdir);
  }
}