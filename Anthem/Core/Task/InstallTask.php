<?php

namespace Anthem\Core\Task;

use Silex\Application;
use Anthem\Core\Task\TaskInterface;
use Anthem\Core\Task\TaskException;

/**
 * A task installing front controllers and assets into a specified dir.
 */
class InstallTask implements TaskInterface
{
  /**
   * Gets a short (one line) help string.
   *
   * @return   string
   */
  function getShortHelp()
  {
    return 'Installs front controllers and static assets.';
  }

  /**
   * Gets a long (multiline) help string.
   *
   * @return   string
   */
  function getLongHelp()
  {
    return <<<EOT
Usage: core:install target_dir [path_to_localconfig.inc]

This task generates front controllers (index.php, console.php, etc.)
and symlinks static assets (images, JS, CSS, etc.) into a web server directory.
EOT;
  }

  /**
   * Runs task.
   *
   * @param    \Silex\Application $app
   * @param    string[]           $args
   * @return   integer
   */
  public function run(Application $app, array $args)
  {
    if (count($args) < 1)
      throw new TaskException('Missing target dir argument.');
    if (count($args) > 1)
    {
      $localconfig = realpath($args[1]);
      if (!is_readable($localconfig))
        throw new TaskException('Local config file ' . $localconfig . ' not found.');
    }
    else
    {
      $localconfig = realpath(__DIR__ . '/../../../localconfig.inc');
      if (!is_readable($localconfig))
        echo 'WARNING: local config file ' . $localconfig . ' not found, and no custom local config specified.';
    }

    // Source directory
    $source_dir = realpath(__DIR__ . '/../../..');

    // Target directory
    $target_dir = $args[0];
    if (!is_dir($target_dir))
    {
      if (!@mkdir($target_dir))
        throw new TaskException('Could not create target directory ' . $target_dir);
    }

    echo 'Installing ' . $source_dir . ' to ' . $target_dir . ':' . PHP_EOL;

    // Generate front controllers
    foreach (array('console.php', 'console.debug.php', 'index.php', 'index.debug.php') as $front_controller)
    {
      if (file_exists($target_dir . '/' . $front_controller))
      {
        echo '* Front controller: ' . $target_dir . '/' . $front_controller . ' (skipping, already exists)' . PHP_EOL;
        continue;
      }
      $code = $app['core.view']->render('Anthem/Core:front_controllers/' . $front_controller, array(
        'source_path' => $source_dir,
        'config_path' => $localconfig,
      ));
      echo '* Front controller: ' . $target_dir . '/' . $front_controller . PHP_EOL;
      if (!@file_put_contents($target_dir . '/' . $front_controller, $code))
        throw new TaskException('Could not create front controller ' . $target_dir . '/' . $front_controller);
    }

    // Look for modules (two levels deep).  Do not use $app['Core']['modules'], which might not be set at this point
    $modules1 = glob($source_dir . '/*/*Module.php');
    foreach ($modules1 as $i => $module)
      $modules1[$i] = preg_replace('#^.*/([^/]+)/[^/]+\.php$#', '\1', $module);
    $modules2 = glob($source_dir . '/*/*/*Module.php');
    foreach ($modules2 as $i => $module)
      $modules2[$i] = preg_replace('#^.*/([^/]+/[^/]+)/[^/]+\.php$#', '\1', $module);
    $modules = array_merge($modules1, $modules2);

    // Generate asset symlinks for modules
    // @todo Copy instead of symlinks for windows
    foreach ($modules as $module)
    {
      foreach ($app['Core']['asset_dirs'] as $dir)
      {
        if (is_dir($source_dir . '/' . $module . '/' . $dir))
        {
          $asset_dir = $target_dir . '/' . strtolower($dir);
          echo '* Assets: ' . $asset_dir . '/' . $module . ' -> ' .
                              $source_dir . '/' . $module . '/' . $dir . PHP_EOL;
          if (!is_dir($asset_dir))
          {
            if (!@mkdir($asset_dir))
              throw new TaskException('Could not create asset dir ' . $asset_dir);
          }

          $module_parts = explode('/', $module);
          $module_last = array_pop($module_parts);
          $module_prefix = implode('/', $module_parts);
          if ($module_prefix)
          {
            if (!is_dir($asset_dir . '/' . $module_prefix))
              if (!@mkdir($asset_dir . '/' . $module_prefix, 0777, true))
                throw new TaskException('Could not create asset dir ' . $asset_dir . '/' . $module_prefix);
          }

          if (file_exists($asset_dir . '/' . $module))
            @unlink($asset_dir . '/' . $module);
          if (!@symlink($source_dir . '/' . $module . '/' . $dir, $asset_dir . '/' . $module))
            throw new TaskException('Could not symlink ' . $asset_dir . '/' . $module . ' -> ' .
                                    $source_dir . '/' . $module . '/' . $dir);
        }
      }
    }

    foreach ($app['Core']['asset_dirs'] as $dir)
    {
      if (is_dir($source_dir . '/' . $dir))
      {
        $asset_dir = $target_dir . '/' . strtolower($dir);
        echo '* Assets: ' . $asset_dir . '/_ -> ' .
                            $source_dir . '/' . $dir . PHP_EOL;
        if (!is_dir($asset_dir))
        {
          if (!@mkdir($asset_dir))
            throw new TaskException('Could not create asset dir ' . $asset_dir);
        }

        if (!@symlink($source_dir . '/' . $dir, $asset_dir . '/_'))
          throw new TaskException('Could not symlink ' . $asset_dir . '/_ -> ' .
                                  $source_dir . '/' . $dir);
      }
    }

    echo '...success!' . PHP_EOL;

    return 0;
  }
}

