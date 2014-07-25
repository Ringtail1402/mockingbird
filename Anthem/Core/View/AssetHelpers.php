<?php

namespace Anthem\Core\View;

use Silex\Application;

/**
 * Asset helpers (CSS, JS, etc.)
 */
class AssetHelpers
{
 /**
  * @var \Silex\Application
  */
  protected $app;

 /**
  * The constructor.
  *
  * @param \Silex\Application $app
  */
  public function __construct(Application $app)
  {
    $this->app = $app;
  }

 /**
  * Returns web path to a file in specified directory.
  *
  * @param  string  $file
  * @param  string  $dir
  * @param  boolean $absolute
  * @return string
  */
  public function asset($file, $dir, $absolute = false)
  {
    if ($file[0] == '/' || !strncmp($file, 'http://', 7) || !strncmp($file, 'https://', 8)) return $file;
    $file = trim(str_replace(':', '/', $file), '/');
    // Allow override
    if (file_exists($this->app['Core']['root_dir'] . '/' . $dir . '/' . $file))
      $file = $this->app['Core']['web_root'] . '/' . strtolower($dir) . '/_/' . $file;
    else
      $file = $this->app['Core']['web_root'] . '/' . strtolower($dir) . '/' . $file;

    if ($absolute)
      $file = $this->app['request']->getScheme().'://'.$this->app['request']->getHttpHost().$file;

    return $file;
  }

 /**
  * Returns web path to JS file.
  *
  * @param  string  $file
  * @param  boolean $absolute
  * @return string
  */
  public function js($file, $absolute = false)
  {
    return $this->asset($file, 'JS', $absolute);
  }

 /**
  * Returns web path to CSS file.
  *
  * @param  string  $file
  * @param  boolean $absolute
  * @return string
  */
  public function css($file, $absolute = false)
  {
    return $this->asset($file, 'CSS', $absolute);
  }

  /**
   * Returns web path to image file.
   *
   * @param  string  $file
   * @param  boolean $absolute
   * @return string
   */
  public function image($file, $absolute = false)
  {
    return $this->asset($file, 'Images', $absolute);
  }
}