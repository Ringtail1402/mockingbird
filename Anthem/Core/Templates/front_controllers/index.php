<?php echo '<?php' ?>

const DEBUG = false;

require_once '<?php echo $config_path ?>';
require_once '<?php echo $source_path ?>/Anthem/Core/Main/WebMain.php';

Anthem\Core\Main\WebMain::main();
