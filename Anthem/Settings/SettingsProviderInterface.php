<?php

namespace Anthem\Settings;

use Silex\Application;
use Anthem\Settings\SettingsInterface;

/**
 * If a module implements this interface, it provides some settings.
 */
interface SettingsProviderInterface
{
  /**
   * This function just returns an object with settings.
   *
   * @abstract
   * @return SettingsInterface
   */
  public function getSettings();
}
