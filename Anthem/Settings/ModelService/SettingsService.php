<?php

namespace Anthem\Settings\ModelService;

use Silex\Application;
use Anthem\Propel\ModelService\PropelModelService;
use Anthem\Settings\SettingsProviderInterface;
use Anthem\Settings\Model\Setting;
use Anthem\Settings\Model\SettingQuery;

/**
 * Model service for Setting model.
 */
class SettingsService extends PropelModelService
{
  /**
   * @var Application
   */
  protected $app;

  /**
   * @var array DB global settings.
   */
  protected $db_global_settings = array();

  /**
   * @var array DB per-user settings.
   */
  protected $db_user_settings = array();

  /**
   * @var array Settings metadata, tree-like.
   */
  protected $settings_tree = array();

  /**
   * @var array Settings metadata, flat.
   */
  protected $settings;

  /**
   * The constructor.  Collects all settings from modules.
   * @todo This can definitely be cached, but there is no caching mechanism in Anthem so far.
   *
   * @param \Silex\Application $app
   */
  public function __construct(Application $app)
  {
    $this->app = $app;
    foreach ($app['Core']['modules_loaded'] as $module)
    {
      if ($module instanceof SettingsProviderInterface)
      {
        $settings = $module->getSettings()->getSettings($app);

        // Hierarchical setting cache
        if (empty($app['Settings']['flatten']))
          $this->settings_tree = array_merge_recursive($this->settings_tree, $settings);

        // Flat setting cache
        foreach ($settings as $id => $setting)
        {
          if (!empty($setting['page_contents']))
          {
            foreach ($setting['page_contents'] as $page_id => $page_setting)
              $this->settings[$page_id] = $page_setting;
          }
          else
            $this->settings[$id] = $setting;
        }
      }
    }
    if (!empty($app['Settings']['flatten']))
      $this->settings_tree = $this->settings;

    // Load settings from DB.  Ignore errors
    try
    {
      if (class_exists('ModelCriteria') && class_exists('\Anthem\Settings\Model\om\BaseSettingQuery'))
      {
        // Global settings
        $db_settings = SettingQuery::create()
                                   ->filterByUserId(null, \Criteria::ISNULL)
                                   ->find();
        foreach ($db_settings as $setting)
          $this->db_global_settings[$setting->getKey()] = $setting->getValue();

        // Per-user settings
        if (!$app['auth']->isGuest())
        {
          $db_settings = SettingQuery::create()
                                     ->filterByUser($app['auth']->getUser())
                                     ->find();
          foreach ($db_settings as $setting)
            $this->db_user_settings[$setting->getKey()] = $setting->getValue();
        }
      }
    }
    catch (\PropelException $e)
    {
    }
  }

 /**
  * Returns a global setting.
  *
  * @param string $setting
  * @return mixed
  */
 public function getGlobal($setting)
 {
    if (!isset($this->settings[$setting]))
      throw new \InvalidArgumentException('Unknown setting: ' . $setting . '.');

    if (isset($this->db_global_settings[$setting]))  // Look for global setting
      return json_decode($this->db_global_settings[$setting], true);
    elseif (isset($this->settings[$setting]['default']))  // Otherwise look for default
      return $this->settings[$setting]['default'];
    else  // Otherwise null
      return null;
 }

  /**
   * Returns a setting.  Priority: cached setting value, DB per-user, DB global, default, null.
   *
   * @param string $setting
   * @return mixed
   */
  public function get($setting)
  {
    if (!isset($this->settings[$setting]))
      throw new \InvalidArgumentException('Unknown setting: ' . $setting . '.');

    // Look for cached value
    if (!isset($this->settings[$setting]['value']))
    {
      if (isset($this->db_user_settings[$setting]))  // Look for per-user setting
        $this->settings[$setting]['value'] = json_decode($this->db_user_settings[$setting], true);
      elseif (isset($this->db_global_settings[$setting]))  // Look for global setting
        $this->settings[$setting]['value'] = json_decode($this->db_global_settings[$setting], true);
      elseif (isset($this->settings[$setting]['default']))  // Otherwise look for default
        $this->settings[$setting]['value'] = $this->settings[$setting]['default'];
      else  // Otherwise null
        $this->settings[$setting]['value'] = null;
    }
    return $this->settings[$setting]['value'];
  }

  /**
   * Sets a single global setting.
   *
   * @param string $setting
   * @param mixed $value
   * @return void
   */
  public function setGlobal($setting, $value)
  {
    // Cached value
    unset($this->settings[$setting]['value']);
    $this->db_global_settings[$setting] = json_encode($value);

    // Store in DB
    $_setting = SettingQuery::create()
                            ->filterByUserId(null, \Criteria::ISNULL)
                            ->filterByKey($setting)
                            ->findOne();
    if (!$_setting)
    {
      $_setting = new Setting();
      $_setting->setUser(null);
      $_setting->setKey($setting);
    }
    $_setting->setValue(json_encode($value));
    $_setting->save();
  }

  /**
   * Sets a single per-user setting.
   *
   * @param string $setting
   * @param mixed $value
   * @return void
   * @throws \LogicException
   */
  public function set($setting, $value)
  {
    if (empty($this->app['Auth']['enable']))
    {
      $this->setGlobal($setting, $value);
      return;
    }
    if ($this->app['auth']->isGuest())
      throw new \LogicException('Cannot set a setting for guest user.');
    $user = $this->app['auth']->getUser();

    // Cached value
    $this->settings[$setting]['value'] = $value;
    $this->db_user_settings[$setting] = json_encode($value);

    // Store in DB
    $_setting = SettingQuery::create()
                            ->filterByUser($user)
                            ->filterByKey($setting)
                            ->findOne();
    if (!$_setting)
    {
      $_setting = new Setting();
      $_setting->setUser($user);
      $_setting->setKey($setting);
    }
    $_setting->setValue(json_encode($value));
    $_setting->save();
  }


  /**
   * Returns all settings pages, as id => title array.
   *
   * @return array
   */
  public function enumPages()
  {
    $result = array();
    $general_page = false;
    foreach ($this->settings_tree as $id => $setting)
    {
      if (isset($setting['page_contents']))
        $result[$id] = $setting['title'];
      else
        $general_page = true;
    }
    if ($general_page)
    {
      $tmp = array(null => _t('Settings.GENERAL'));
      $result = array_merge($tmp, $result);
    }
    return $result;
  }

  /**
   * Returns all settings on specified page.
   *
   * @param string  $page
   * @param boolean $global
   * @return array
   */
  public function getSettingsOnPage($page, $global = null)
  {
    // If global flag is specified, first get all settings and then filter them by this flag
    if ($global !== null)
    {
      $settings = $this->getSettingsOnPage($page);
      foreach ($settings as $id => $setting)
      {
        if (isset($setting['global']) && $setting['global'] != $global)
          unset($settings[$id]);
      }
      return $settings;
    }

    if ($page)
    {
      if (isset($this->settings_tree[$page]))
        return $this->settings_tree[$page]['page_contents'];
      return null;
    }

    // Default page
    $result = array();
    foreach ($this->settings_tree as $id => $setting)
    {
      if (!isset($setting['page_contents']))
        $result[$id] = $setting;
    }
    return $result;
  }

  /**
   * Returns underlying model class.
   *
   * @return string
   */
  public function getModelClass()
  {
    return 'Anthem\\Settings\\Model\\Setting';
  }
}
