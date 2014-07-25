<?php

namespace Anthem\Propel;

use Silex\Application;
use Propel;
use PropelConfiguration;
use Anthem\Core\Module;
use Anthem\Core\Task\TaskProviderInterface;

if (file_exists(__DIR__.'/lib/propel-runtime.phar'))
  require_once __DIR__.'/lib/propel-runtime.phar';

/**
 * A Propel bridge for Anthem.  Sets up autoloading and stuff.
 */
class PropelModule extends Module implements TaskProviderInterface
{
 /**
  * Propel initialization.
  *
  * @param Application $app
  */
  public function __construct(Application $app)
  {
    // Allow this to happen so that e.g. propel:generate-runtime-phar can work for the first time
    if (!class_exists('Propel'))
    {
      fprintf(STDERR, 'warning: Propel runtime not loaded' . PHP_EOL);
      return;
    }

    $configuration = new PropelConfiguration($app['Propel']);
    Propel::setConfiguration($configuration);
    Propel::initialize();
  }

 /**
  * Sets up PropelModule tasks.
  *
  * @param  none
  * @return array[string]
  */
  function getTasks()
  {
    return array(
      'propel:generate-runtime-phar' => 'Anthem\\Propel\\Task\\GenerateRuntimePharTask',
      'propel:build'                 => 'Anthem\\Propel\\Task\\BuildTask',
      'propel:build-model'           => 'Anthem\\Propel\\Task\\BuildModelTask',
      'propel:build-sql'             => 'Anthem\\Propel\\Task\\BuildSqlTask',
      'propel:load-fixtures'         => 'Anthem\\Propel\\Task\\LoadFixturesTask',
    );
  }
}