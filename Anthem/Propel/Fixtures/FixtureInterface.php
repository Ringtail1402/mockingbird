<?php

namespace Anthem\Propel\Fixtures;

use Silex\Application;

/**
 * An interface for fixture classes.
 */
interface FixtureInterface
{
 /**
  * Returns fixture priority (fixtures with higher priorities get loaded first).
  *
  * @abstract
  * @param  none
  * @return integer
  */
  public function getPriority();

  /**
   * Actually loads the fixtures.
   *
   * @abstract
   * @param  Application    $app
   * @param  object[string] &$references  References for other fixtures may be set here.
   * @return void
   */
  public function load(Application $app, array &$references);
}