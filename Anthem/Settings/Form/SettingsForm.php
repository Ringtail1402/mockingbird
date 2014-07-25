<?php

namespace Anthem\Settings\Form;

use Anthem\Forms\Form\Form;
use Anthem\Core\View\ViewHelpers;

/**
 * Dynamic settings form.
 */
class SettingsForm extends Form
{
  /**
   * @var boolean
   */
  protected $global;

  /**
   * Creates a form.
   *
   * @param \Silex\Application $app
   * @param array              $settings
   * @param boolean            $global
   */
  public function __construct($app, $settings, $global)
  {
    $this->global = $global;
    $view = new ViewHelpers($app);
    $object = array();
    $fields = array();
    foreach ($settings as $id => $setting)
    {
      if (!isset($setting['input'])) continue;

      $fields[$id] = $setting['input']();
      if (isset($setting['title']))
        $fields[$id]->setOption('label', $view->str($setting['title']));
      if (isset($setting['help']))
        $fields[$id]->setOption('help', $view->str($setting['help']));

      $object[$id] = $global ? $app['settings']->getGlobal($id)
                             : $app['settings']->get($id);
    }

    return parent::__construct($app, $object, array('fields' => $fields));
  }

  /**
   * Saves settings form.
   */
  public function save()
  {
    $object = parent::save();
    foreach ($object as $id => $value)
    {
      if ($this->global)
        $this->app['settings']->setGlobal($id, $value);
      else
        $this->app['settings']->set($id, $value);
    }
  }
}
