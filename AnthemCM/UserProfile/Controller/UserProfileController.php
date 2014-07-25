<?php

namespace AnthemCM\UserProfile\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AnthemCM\UserProfile\Model\UserProfile;
use AnthemCM\UserProfile\Form\UserProfileForm;

/**
 * UserProfile module controller.
 */
class UserProfileController
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
    $app['auth']->checkAuthorization();
  }

  /**
   * Shows user profile edit form.
   *
   * @param Request $request
   * @return string
   */
  public function editAction(Request $request)
  {
    // Get or init profile
    $profile = $this->app['auth']->getUser()->getUserProfile();
    if (!$profile)
    {
      $profile = new UserProfile();
      $profile->setUser($this->app['auth']->getUser());
    }

    // Create form
    $form = new UserProfileForm($this->app, $profile);

    // Handle save
    if ($request->getMethod() == 'POST')
    {
      $form->setValue($request->request->all());
      if ($form->validate())
      {
        $form->save();
        $profile->save();
        return new RedirectResponse($this->app['url_generator']->generate('user_profile.edit'));
      }
    }

    return $this->app['core.view']->render('AnthemCM/UserProfile:edit.php', array(
      'form' => $form,
    ));
  }
}