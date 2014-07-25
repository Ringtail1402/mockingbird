<?php

namespace Anthem\Core\View;

use Silex\Application;

/**
 * Module classes which provide some helpers must implement this interface.
 */
interface HelperProviderInterface
{
 /**
  * Returns all helper objects for this module, indexed by their names.
  *
  * @abstract
  * @param  \Silex\Application $app
  * @return object[string]
  */
  function getHelpers(Application $app);
}