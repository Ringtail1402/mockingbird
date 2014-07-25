<?php

namespace Anthem\Auth\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Anthem\Auth\AuthEvents;
use Anthem\Auth\Event\UserEvent;
use Anthem\Auth\Form\AuthForm;
use Anthem\Auth\Form\ChangeEmailForm;
use Anthem\Auth\Form\ChangePasswordForm;
use Anthem\Auth\Form\RequestPasswordForm;

/**
 * Authentication controller.
 */
class AuthController
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
   * Logs in as a user without prompting for password.
   * This requires auth.admin.force_login policy.
   *
   * @param  Request $request
   * @return Response
   * @throws NotFoundHttpException
   */
  public function loginForceAction(Request $request)
  {
    $this->app['auth']->checkPolicies('auth.admin.force_login');

    $user = $this->app['auth.model.user']->find($request->get('id'));
    if (!$user)
      throw new NotFoundHttpException('Unknown user id.');

    $this->app['auth']->logon($user);
    return $this->redirectToHomepage();
  }

  /**
   * Gets/posts authentication form.  AJAX version.  Uses JSONP to circumvent same origin policy
   * (this might be posted via HTTPS from a HTTP page).  This also means this form always uses GET.
   *
   * @param  Request $request
   * @return Response
   */
  public function loginAjaxAction(Request $request)
  {
    $form = new AuthForm($this->app);
    $valid = true;

    if ($request->query->get('_login'))
    {
      $form->setValue($request->query->get('_login'));
      $valid = $form->validate();
      if ($valid)
      {
        $user = $form->save();
        $this->app['auth']->logon($user);
      }
    }

    return new Response($request->get('callback') . '(' . json_encode(array(
      'form'  => $this->app['core.view']->render('Anthem/Auth:login_ajax.php', array(
        'form' => $form->render(),
        'social' => $this->getSocial(),
        'https_login' => $this->app['auth']->needSecureCookies(),
      )),
      'valid' => $valid,
      'https_login' => $this->app['auth']->needSecureCookies(),
      'already_logged_on' => !$this->app['auth']->isGuest(),
    )) . ');',
    200, array('Content-Type' => 'application/javascript'));
  }

  /**
   * Gets/posts authentication form.
   *
   * @param  Request $request
   * @return RedirectResponse|string
   */
  public function loginAction(Request $request)
  {
    $this->checkHTTPS();

    $redir = $request->query->get('redir');
    $url = $request->getBaseUrl() . '/' . $redir;
    if (!$this->app['auth']->isGuest())
      return new RedirectResponse($url);
    $this->app['session']->set('anthem.auth.login_redir', $url);

    // Usual login clears social account attachment
    $this->app['session']->remove('anthem.auth.attach_social_account');

    $form = new AuthForm($this->app);

    if ($request->getMethod() == 'POST')
    {
      $form->setValue($request->request->get('_login'));
      $valid = $form->validate();
      if ($valid)
      {
        $user = $form->save();
        $this->app['auth']->logon($user);
        $this->app['session']->remove('anthem.auth.login_redir');

        // Redirect either to home page or to requested page upon successful login.
        return new RedirectResponse($url);
      }
    }

    return $this->app['core.view']->render('Anthem/Auth:login.php', array(
      'form'   => $form->render(),
      'redir'  => $redir,
      'social' => $this->getSocial(),
      'https_login' => $this->app['auth']->needSecureCookies(),
    ));
  }

  /**
   * Logs out current user.  Redirects to home page.
   *
   * @param  Request $request
   * @return RedirectResponse
   */
  public function logoutAction(Request $request)
  {
    $this->app['auth']->logout();
    $this->checkHTTPS(false);
    return $this->redirectToHomepage();
  }

  /**
   * Gets/posts registration form.
   *
   * @param  Request $request
   * @return RedirectResponse|string
   */
  public function registerAction(Request $request)
  {
    $this->checkHTTPS();
    $redir = $request->query->get('redir');
    $url = $request->getBaseUrl() . '/' . $redir;
    if (!$this->app['auth']->isGuest())
      return new RedirectResponse($url);

    if (empty($this->app['Auth']['features']['registration']))
      $this->app['auth']->abort();

    $form = new $this->app['Auth']['register_form_class']($this->app);

    if ($request->getMethod() == 'POST')
    {
      $form->setValue($request->request->all());
      $valid = $form->validate();
      if ($valid)
      {
        $user = $form->save();
        $user->save();

        // Issue an event
        $event = new UserEvent($user);
        $this->app['dispatcher']->dispatch(AuthEvents::USER_REGISTER, $event);

        // Immediate registration and login (no verification required)
        if (empty($this->app['Auth']['features']['email_validation']))
        {
          $this->app['auth']->logon($user);
          return new RedirectResponse($url);
        }

        // Otherwise, lock user for pending validation and redirect to email validation page
        $user->setLocked('email_validation');
        $user->save();
        $request->getSession()->set('anthem.auth.email_validation', $user->getId());
        return new RedirectResponse($this->app['url_generator']->generate('auth.register.email_validation_needed'));
      }
    }

    return $this->app['core.view']->render('Anthem/Auth:register.php', array(
      'form'   => $form->render(),
      'redir'  => $redir,
      'social' => $this->getSocial(),
    ));
  }

  /**
   * Sends initial e-mail validation message to user and displays relevant message.
   *
   * @param  Request $request
   * @return RedirectResponse|string
   */
  public function registerEmailValidationNeededAction(Request $request)
  {
    if (empty($this->app['Auth']['features']['email_validation'])) $this->app['auth']->abort();

    // Find user to validate
    $id = $request->getSession()->get('anthem.auth.email_validation');
    $request->getSession()->remove('anthem.auth.email_validation');
    if (!$id || !($user = $this->app['auth.model.user']->find($id)))
      throw new NotFoundHttpException('Unknown or missing user id to verify.');

    // Create a e-mail validation key
    $key = $this->app['auth.model.user_key']->createKey($user, 'initial_email_validation', $user->getEmail());

    // Mail a message
    $mail = \Swift_Message::newInstance();
    $mail->setFrom(array($this->app['Core']['mail.default_from']));
    $mail->setTo(array($user->getEmail()));
    $mail->setSubject(_t('Auth.REGISTER_EMAIL_VALIDATION', $this->app['Core']['project']));
    $mail->setBody($this->app['core.view']->render('Anthem/Auth:mail/email_validation.php', array(
      'email' => $user->getEmail(),
      'key'   => $key->getUniqid(),
    )), 'text/html');
    $this->app['mailer']->send($mail);

    // Show a message
    return $this->app['core.view']->render('Anthem/Auth:register_email_validation_needed.php', array(
      'email' => $user->getEmail(),
    ));
  }

  /**
   * Validates e-mail address.
   *
   * @param  Request $request
   * @return RedirectResponse|string
   */
  public function registerEmailValidate(Request $request)
  {
    $this->checkHTTPS();

    if (empty($this->app['Auth']['features']['email_validation'])) $this->app['auth']->abort();

    if (!$this->app['auth']->isGuest() || !$request->query->get('key'))
      return $this->redirectToHomepage();

    // Verify key
    $key = $this->app['auth.model.user_key']->findKey('initial_email_validation', $request->query->get('key'));
    if (!$key || time() - $key->getCreatedAt('U') > $this->app['Auth']['mailed_keys_age'])
    {
      $this->app['notify']->addTransient(_t('Auth.REGISTER_EMAIL_VALIDATION_INVALID_KEY'), 'error');

      // Delete user.  Take care not to delete a valid account
      if ($key && $key->getUser()->getLocked() == 'email_validation')
        $key->getUser()->delete();

      return $this->redirectToHomepage();
    }

    // Unlock user
    $user = $key->getUser();
    $user->setLocked(null);
    $user->save();

    // Logon
    $this->app['auth']->logon($user);

    // Clear key
    $this->app['auth.model.user_key']->deleteKey($user, 'initial_email_validation');

    // Notify and redirect to home page
    $this->app['notify']->addTransient(_t('Auth.REGISTER_EMAIL_VALIDATION_SUCCESS'), 'info');
    return $this->redirectToHomepage();
  }

  /**
   * Gets/posts email change form.
   *
   * @param  Request $request
   * @return RedirectResponse|string
   */
  public function changeEmailAction(Request $request)
  {
    $this->app['auth']->checkAuthorization();
    $user = $this->app['auth']->getUser();

    $form = new ChangeEmailForm($this->app);

    if ($request->getMethod() == 'POST')
    {
      $form->setValue($request->request->all());
      $valid = $form->validate();
      if ($valid)
      {
        $email = $form->save();

        // Do nothing
        if ($email == $user->getEmail()) return $this->redirectToHomepage();

        // Immediate update
        if (empty($this->app['Auth']['features']['email_validation']))
        {
          $user->setEmail($email);
          $user->save();
          $this->app['notify']->addTransient(_t('Auth.CHANGE_EMAIL_SUCCESS'), 'info');
        }
        // Usual confirmation email routine
        else
        {
          // Create a e-mail validation key
          $key = $this->app['auth.model.user_key']->createKey($this->app['auth']->getUser(),
            'change_email_validation', $email);

          // Mail a message
          $mail = \Swift_Message::newInstance();
          $mail->setFrom(array($this->app['Core']['mail.default_from']));
          $mail->setTo($email);
          $mail->setSubject(_t('Auth.CHANGE_EMAIL'));
          $mail->setBody($this->app['core.view']->render('Anthem/Auth:mail/change_email.php', array(
            'email' => $email,
            'key'   => $key->getUniqid(),
          )), 'text/html');
          $this->app['mailer']->send($mail);

          // Notice
          $this->app['notify']->addTransient(_t('Auth.CHANGE_EMAIL_NOTICE', htmlspecialchars($email)), 'info');
        }

        // Redirect to home page
        return $this->redirectToHomepage();
      }
    }

    return $this->app['core.view']->render('Anthem/Auth:change_email.php', array(
      'form'  => $form->render(),
    ));
  }

  /**
   * Validates e-mail address change.
   *
   * @param  Request $request
   * @return RedirectResponse|string
   */
  public function changeEmailValidateAction(Request $request)
  {
    if (empty($this->app['Auth']['features']['email_validation'])) $this->app['auth']->abort();
    $this->app['auth']->checkAuthorization();
    if (!$request->query->get('key'))
      return $this->redirectToHomepage();

    // Verify key
    $key = $this->app['auth.model.user_key']->findKey('change_email_validation', $request->query->get('key'));
    if (!$key || time() - $key->getCreatedAt('U') > $this->app['Auth']['mailed_keys_age'])
    {
      $this->app['notify']->addTransient(_t('Auth.CHANGE_EMAIL_INVALID_KEY'), 'error');
      return $this->redirectToHomepage();
    }

    // Change user
    $user = $key->getUser();
    $user->setEmail($key->getData());
    $user->save();

    // Clear key
    $this->app['auth.model.user_key']->deleteKey($user, 'change_email_validation');

    // Notify and redirect to home page
    $this->app['notify']->addTransient(_t('Auth.CHANGE_EMAIL_SUCCESS'), 'info');
    return $this->redirectToHomepage();
  }

  /**
   * Gets/posts password change form.
   *
   * @param  Request $request
   * @return RedirectResponse|string
   */
  public function changePasswordAction(Request $request)
  {
    $this->app['auth']->checkAuthorization();
    $form = new ChangePasswordForm($this->app, $this->app['auth']->getUser());
    if ($request->getMethod() == 'POST')
    {
      $form->setValue($request->request->all());
      $valid = $form->validate();
      if ($valid)
      {
        $user = $form->save();
        $user->save();

        // Redirect to home page
        return $this->redirectToHomepage();
      }
    }

    return $this->app['core.view']->render('Anthem/Auth:change_password.php', array(
      'form'  => $form->render(),
    ));
  }

  /**
   * Gets/posts password reset request form.
   *
   * @param  Request $request
   * @return RedirectResponse|string
   */
  public function requestPasswordAction(Request $request)
  {
    $this->checkHTTPS();    
    if (!$this->app['auth']->isGuest())
      return new RedirectResponse($this->app['url_generator']->generate('auth.change_password'));

    if (empty($this->app['Auth']['features']['password_recovery'])) $this->app['auth']->abort();

    $form = new RequestPasswordForm($this->app);
    if ($request->getMethod() == 'POST')
    {
      $form->setValue($request->request->all());
      $valid = $form->validate();
      if ($valid)
      {
        $user = $form->save();

        // Create a recover password key
        $key = $this->app['auth.model.user_key']->createKey($user, 'password_recovery', $user->getEmail());

        // Mail a message
        $mail = \Swift_Message::newInstance();
        $mail->setFrom(array($this->app['Core']['mail.default_from']));
        $mail->setTo(array($user->getEmail()));
        $mail->setSubject(_t('Auth.REQUEST_PASSWORD_MAIL', $this->app['Core']['project']));
        $mail->setBody($this->app['core.view']->render('Anthem/Auth:mail/request_password.php', array(
          'email' => $user->getEmail(),
          'key'   => $key->getUniqid(),
        )), 'text/html');
        $this->app['mailer']->send($mail);

        // Notify user
        $this->app['notify']->addTransient(htmlspecialchars(_t('Auth.REQUEST_PASSWORD_NOTICE', $user->getEmail())), 'info');

        // Redirect to home page
        return $this->redirectToHomepage();
      }
    }

    return $this->app['core.view']->render('Anthem/Auth:request_password.php', array(
      'form'  => $form->render(),
    ));
  }

  /**
   * Gets/posts password reset form.
   *
   * @param  Request $request
   * @return RedirectResponse|string
   */
  public function resetPasswordAction(Request $request)
  {
    $this->checkHTTPS();    
    if (!$this->app['auth']->isGuest())
      return new RedirectResponse($this->app['url_generator']->generate('auth.change_password'));

    if (empty($this->app['Auth']['features']['password_recovery'])) $this->app['auth']->abort();

    if (!$request->query->get('key'))
      return $this->redirectToHomepage();

    // Verify key
    $key = $this->app['auth.model.user_key']->findKey('password_recovery', $request->query->get('key'));
    if (!$key || time() - $key->getCreatedAt('U') > $this->app['Auth']['mailed_keys_age'])
    {
      $this->app['notify']->addTransient(_t('Auth.RESET_PASSWORD_INVALID_KEY'), 'error');
      return $this->redirectToHomepage();
    }

    // Offer a change password form
    $form = new ChangePasswordForm($this->app, $key->getUser());
    if ($request->getMethod() == 'POST')
    {
      $form->setValue($request->request->all());
      $valid = $form->validate();
      if ($valid)
      {
        // Change password
        $user = $form->save();
        $user->save();

        // Logon
        $this->app['auth']->logon($user);

        // Clear key
        $this->app['auth.model.user_key']->deleteKey($user, 'password_recovery');

        // Notify and redirect to home page
        $this->app['notify']->addTransient(_t('Auth.RESET_PASSWORD_SUCCESS'), 'info');
        return $this->redirectToHomepage();
      }
    }

    return $this->app['core.view']->render('Anthem/Auth:reset_password.php', array(
      'key'   => $key->getUniqid(),
      'form'  => $form->render(),
    ));
  }

  /**
   * Returns icons and titles of all enabled social auth providers.
   *
   * @return array
   */
  protected function getSocial()
  {
    // Social auth providers
    $social = array();
    foreach ($this->app['Auth']['social_auth'] as $provider_id => $options)
    {
      /** @var \Anthem\Auth\Social\BaseSocialAuthProvider $provider */
      $provider = $this->app[$provider_id];
      $social[$provider_id] = array('title' => $provider->getTitle(), 'icon' => $provider->getIconAsset());
    }
    return $social;
  }

  /**
   * Redirects to HTTPS (or non-HTTPS) version of page, if https setting is set to 'auth'.
   *
   * @param boolean $https
   * @return void|never
   */
  protected function checkHTTPS($https = true)
  {
    if ($this->app['Auth']['https'] == 'auth')
      $this->app['auth']->redirectHTTPS($https);
  }

  /**
   * Redirects to homepage.  Downgrades to http if necessary.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  protected function redirectToHomepage()
  {
    /** @var Request $request */
    $request = $this->app['request'];
    $uri = $request->getUriForPath('/');
    if ($this->app['Auth']['https'] == 'auth' && $this->app['auth']->isGuest())
      $uri = preg_replace('/^https:/', 'http:', $uri);
    return new RedirectResponse($uri);
  }
}