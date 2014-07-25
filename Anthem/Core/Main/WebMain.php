<?php

namespace Anthem\Core\Main;

require_once __DIR__ . '/../Core.php';

use Anthem\Core\Core;

/**
 * Main Anthem class.
 */
class WebMain
{
 /**
  * Main function for Anthem.
  *
  * @method main
  * @return void
  */
  static function main()
  {
    $app = new Core();
    $app->run();
  }
}