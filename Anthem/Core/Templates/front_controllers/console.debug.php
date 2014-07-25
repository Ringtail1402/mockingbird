<?php echo '<?php' ?>

if (php_sapi_name() != 'cli')
  die('Console tasks must be run in cli environment.');

const DEBUG = true;

require_once '<?php echo $config_path ?>';
require_once '<?php echo $source_path ?>/Anthem/Core/Main/ConsoleMain.php';

$retval = Anthem\Core\Main\ConsoleMain::main($_SERVER['argc'], $_SERVER['argv']);
exit($retval);
