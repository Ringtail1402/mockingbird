<?php

namespace Anthem\Propel\Fixtures;

/**
 * Module classes which provide some fixtures must implement this interface.
 */
interface FixtureProviderInterface
{
 /**
  * Returns an array of class names implementing FixtureInterface in this module.
  *
  * @abstract
  * @return string[]
  */
  function getFixtureClasses();
}