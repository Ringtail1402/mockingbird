<?php

namespace Anthem\Core;

use Silex\Application;

/**
 * Base module class.  Doesn't do much.
 */
abstract class Module
{
  public function __construct(Application $app)
  {
  }
}
