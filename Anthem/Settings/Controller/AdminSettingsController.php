<?php

namespace Anthem\Settings\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Anthem\Settings\Form\SettingsForm;

/**
 * Settings UI.
 */
class AdminSettingsController
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

    // Require authorization
    if (!empty($app['Auth']['enable'])) $app['auth']->checkAuthorization();
  }

  /**
   * Shows/posts a settings form.
   *
   * @param  Request $request
   * @param  boolean $global
   * @return string
   */
  public function indexAction(Request $request, $global = false)
  {
    // If auth feature is turned off, always run in global mode
    if (empty($this->app['Auth']['enable']))
      $global = true;
    // Otherwise check admin permission for global mode
    elseif ($global)
      $this->app['auth']->checkPolicies('settings.admin.ro');

    $page = $request->get('page');
    $pages = $this->app['settings']->enumPages();
    if (!isset($pages[$page]))
      throw new NotFoundHttpException('Unknown settings page: ' . $page);
    $settings = $this->app['settings']->getSettingsOnPage($page, $global);
    $form = new SettingsForm($this->app, $settings, $global);
    $valid = true;

    // Handle save
    if ($request->getMethod() == 'POST')
    {
      // Check admin permission for global mode
      if (!empty($this->app['Auth']['enable']) && $global)
        $this->app['auth']->checkPolicies('settings.admin.rw');

      $form->setValue($request->request->all());
      if ($form->validate())
      {
        $form->save();
        $form = new SettingsForm($this->app, $this->app['settings']->getSettingsOnPage($page, $global), $global);
      }
      else
        $valid = false;
    }

    return $this->app['core.view']->render('Anthem/Settings:settings_admin.php', array(
      'ro'      => $global && !$this->app['auth']->hasPolicies('settings.admin.rw'),
      'global'  => $global,
      'is_ajax' => $request->isXmlHttpRequest(),
      'pages'   => $pages,
      'page'    => $page,
      'form'    => $form,
      'valid'   => $valid
    ));
  }
}