<?php

namespace Anthem\Settings;

use Silex\Application;

/**
 * This is an interface for classes implementing user-configurable settings.
 */
interface SettingsInterface
{
  /**
   * This function must return an array of settings.  Settings can be arranged into
   * pages.  Each entry can have these elements:
   * - title (string, required): Setting title.
   * - help (string): Setting help text.
   * - default (mixed): Default value for the settings.  Will default to null if not set.
   * - input (callable): function() which returns an input for editing this settings.
   *     The setting is not directly editable from settings UI if this is not set.
   * - page_contents (array): A set of settings within some page.
   *     Settings outside this will go to General page.
   *
   * Note that settings are encoded in JSON for storage, so they might be any simple
   * values or normal or associative arrays.
   *
   * @abstract
   * @return array
   */
  public function getSettings(Application $app);
}
