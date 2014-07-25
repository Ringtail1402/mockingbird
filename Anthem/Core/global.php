<?php

/**
 * This file contains global code in root namespaces.
 * While this might not be a perfectly clean solution, having _t() and $cfg as globals
 * simplifies things a lot.
 */

/**
 * @var array Global translation strings array for PHP code.
 */
$_t = array();

/**
 * @var array Global translation strings array for JS code.
 */
$_tJS = array();

/**
 * Shortcut l10n function.
 *
 * @param $stringid
 * @param ...
 * @return string
 */
function _t($stringid)
{
  global $_t;
  $args = func_get_args();
  array_shift($args);
  if (isset($_t) && isset($_t[$stringid]))
    return vsprintf($_t[$stringid], $args);
  else
    return vsprintf($stringid, $args);
}