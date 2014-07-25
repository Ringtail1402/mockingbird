<?php

namespace Mockingbird\Util;

/**
 * This just registers some functoins for SQLite which are missing by default.
 */
class SQLiteFunctions
{
  static public function registerExtraSQLiteFunctions(\PDO $con)
  {
    $con->sqliteCreateFunction('sign', function($value) {
      return ($value < 0) ? -1 : (($value > 0) ? 1 : 0);
    }, 1);
  }
}