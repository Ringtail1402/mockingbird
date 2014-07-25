<?php

// This script allows running core:install task before Anthem app is actually installed.

if (php_sapi_name() != 'cli')
  die('bootstrap.php must be run in cli environment.');

if ($_SERVER['argc'] < 2)
  die('Usage: bootstrap.php target_dir [path_to_local_config.php]' . PHP_EOL);

// Insert a "core:install" argument into arguments array
$argc = $_SERVER['argc'] + 1;
$argv = array($_SERVER['argv'][0], 'core:install');
for ($i = 1; $i < $_SERVER['argc']; $i++)
  $argv[] = $_SERVER['argv'][$i];

const DEBUG = true;
// Load only core module
$localconfig = array('modules' => array('Anthem/Core'));
require_once __DIR__ . '/Core/Main/ConsoleMain.php';

$retval = Anthem\Core\Main\ConsoleMain::main($argc, $argv);
exit($retval);
