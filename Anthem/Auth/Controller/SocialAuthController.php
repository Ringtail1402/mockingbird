<?php

namespace Anthem\Auth\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Anthem\Auth\AuthEvents;
use Anthem\Auth\Event\UserEvent;
use Anthem\Auth\Event\UserAttachSocialEvent;
use Anthem\Auth\Social\BaseSocialAuthProvider;
use Anthem\Auth\Form\AuthForm;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserSocialAccount;

/**
 * Social networks authentication controller.
 */
class SocialAuthController
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
   * Redirects user to authorization using the specified provider.
   *
   * @param  Request $request
   * @param  string  $provider_id
   * @return Response
   * @throws NotFoundHttpException
   */
  public function promptAction(Request $request, $provider_id)
  {
    if (empty($this->app['Auth']['features']['social_accounts'])) $this->app['auth']->abort();
    $this->checkHTTPS();

    // Pass over to provider
    $provider = $this->getProvider($provider_id);
    return $provider->prompt($request);
  }

  /**
   * Handles callback from auth provider.  Unless an error occurs, this action is executed on successful
   * login via 3rd party.  Possible courses of action are:
   * - social account record for this provider and remote user id exists -> login user via that account
   * - social account doesn't exist, user is logged in -> create social account record for user
   * - social account doesn't exist, user is not logged in, user with matching email exists ->
   *   create social account record for user and login
   * - social account doesn't exist, user is not logged in, user with matching email does not exist ->
   *   store provider and remote user id in session, redirect to "first login" page which allows
   *   registering a new user on the fly or logging in as existing user and creating social account
   *   record for him/her
   *
   * @param  Request $request
   * @param  string  $provider_id
   * @return Response
   * @throws NotFoundHttpException
   */
  public function authAction(Request $request, $provider_id)
  {
    if (empty($this->app['Auth']['features']['social_accounts'])) $this->app['auth']->abort();
    $this->checkHTTPS();

    $provider = $this->getProvider($provider_id);

    // Authenticate
    try
    {
      $remote_user_id = $provider->auth($request);
    }
    catch (\Exception $e)
    {
      // If failed, redirect back to main login page
      $this->app['notify']->addTransient($e->getMessage(), 'error');
      return new RedirectResponse($this->app['url_generator']->generate('auth.login'));
    }

    // User has authenticated via auth provider.  Look for existing record
    $social_account = $this->app['auth.model.user_social_account']->findSocialAccount($provider_id, $remote_user_id);
    if ($social_account)
    {
      // Can login!
      $this->app['auth']->logon($social_account->getUser());

      // Update social accout title
      $social_account->setTitle($provider->getUserDisplayName());
      $social_account->save();

      // Redirect to home page, or stored redirect page
      return $this->redirectAfterLogin();
    }

    // Social account does not yet exist.  If the user is already logged in, just create social account
    if (!$this->app['auth']->isGuest())
    {
      $social_account = new UserSocialAccount();
      $social_account->setUser($this->app['auth']->getUser());
      $social_account->setProvider($provider_id);
      $social_account->setRemoteUserId($remote_user_id);
      $social_account->setTitle($provider->getUserDisplayName());
      $social_account->save();
      $this->app['session']->remove('anthem.auth.attach_social_account');
      return $this->redirectAfterLogin();
    }

    // Look up for user with matching e-mail.  If exists, attach social account silently
    $user = $this->app['auth.model.user']->findUserByEmail($provider->getProperty('email'));
    if ($user)
    {
      // Init social account record
      $social_account = new UserSocialAccount();
      $social_account->setUser($user);
      $social_account->setProvider($provider_id);
      $social_account->setRemoteUserId($remote_user_id);
      $social_account->setTitle($provider->getUserDisplayName());
      $social_account->save();

      // Issue event
      $event = new UserAttachSocialEvent($user, $social_account, $provider);
      $this->app['dispatcher']->dispatch(AuthEvents::USER_ATTACH_SOCIAL, $event);

      // Login and redirect
      $this->app['auth']->logon($user);
      return $this->redirectAfterLogin();
    }

    // Store data for attaching account (if has not done this yet)
    $attach_social_account = $this->app['session']->get('anthem.auth.attach_social_account');
    if (!$attach_social_account)
    {
      $this->app['session']->set('anthem.auth.attach_social_account', array(
        'provider'          => $provider_id,
        'remote_user_id'    => $remote_user_id,
        'user_display_name' => $provider->getUserDisplayName()
      ));
    }
    else
    {
      // Avoid attaching recursively.  Show error message and go back to previous provider
      $this->app['notify']->addTransient(_t('Auth.SOCIAL_ATTACH_FAILED'), 'error');
      $provider_id = $attach_social_account['provider'];
    }

    // Redirect to "first login" form
    return new RedirectResponse($this->app['url_generator']->generate('auth.social.first_login', array('provider' => $provider_id)));
  }

  /**
   * Handles first login via auth provider (when no UserSocialAccount record exists yet).
   *
   * @param  Request $request
   * @param  string  $provider_id
   * @return Response
   * @throws NotFoundHttpException
   */
  public function firstLoginAction(Request $request, $provider_id)
  {
    if (empty($this->app['Auth']['features']['social_accounts'])) $this->app['auth']->abort();
    $this->checkHTTPS();

    // Provider and social account data
    $provider = $this->getProvider($provider_id);
    $attach_account_data = $this->app['session']->get('anthem.auth.attach_social_account');
    if (!$attach_account_data)
      throw new NotFoundHttpException('No account to attach.');

    // Other social auth providers
    $social = array();
    foreach ($this->app['Auth']['social_auth'] as $_provider_id => $options)
    {
      if ($_provider_id == $provider_id) continue;

      /** @var \Anthem\Auth\Social\BaseSocialAuthProvider $_provider */
      $_provider = $this->app[$_provider_id];
      $social[$_provider_id] = array('title' => $_provider->getTitle(), 'icon' => $_provider->getIconAsset());
    }

    // Default login form
    $form = new AuthForm($this->app);

    if ($request->getMethod() == 'POST')
    {
      // Requested an option to generate a new User record
      if ($request->get('new_user')) return $this->registerFromSocialAccount($provider, $attach_account_data);

      // Existing User record.  Default login handling, doesn't differ from AuthController::loginAction().
      // UserSocialAccount will be created and attached to User via SocialAccountAttachLoginHandler.
      // We cannot do this here as user may request social auth here as well
      $form->setValue($request->request->get('_login'));
      $valid = $form->validate();
      if ($valid)
      {
        $user = $form->save();
        $this->app['auth']->logon($user);
        return $this->redirectAfterLogin();
      }
    }

    return $this->app['core.view']->render('Anthem/Auth:social/first_login.php', array(
      'form'   => $form->render(),
      'social' => $social,
      'provider_id'    => $provider_id,
      'provider_title' => $provider->getTitle(),
      'provider_icon'  => $provider->getIconAsset(),
      'user_display_name' => $provider->getUserDisplayName()
    ));
  }

  /**
   * Lists existing bound social network accounts to user.
   *
   * @param Request $request
   * @return Response
   */
  public function listSocialAccountsAction(Request $request)
  {
    if (empty($this->app['Auth']['features']['social_accounts'])) $this->app['auth']->abort();
    $this->app['auth']->checkAuthorization();

    // Existing social accounts
    $social_accounts = array();
    foreach ($this->app['auth']->getUser()->getSocialAccounts() as $social_account)
      $social_accounts[$social_account->getProvider()] = $social_account;

    // Available social providers
    $providers = array();
    $can_add = false;
    foreach ($this->app['Auth']['social_auth'] as $provider_id => $options)
    {
      /** @var \Anthem\Auth\Social\BaseSocialAuthProvider $_provider */
      $provider = $this->app[$provider_id];
      $providers[$provider_id] = array(
        'title'     => $provider->getTitle(),
        'icon'      => $provider->getIconAsset(),
        'available' => !isset($social_accounts[$provider_id]),
      );
      if (!isset($social_accounts[$provider_id])) $can_add = true;
    }

    // Can user delete social accounts?  He cannot if he has only one social account
    // and has no password (and hence no means to log on normally) set.
    $can_delete = count($social_accounts) > 1 || $this->app['auth']->getUser()->getPassword();

    // Store redirect path in case user chooses to add an account
    $this->app['session']->set('anthem.auth.login_redir', $this->app['url_generator']->generate('auth.social.list_social_accounts'));

    return $this->app['core.view']->render('Anthem/Auth:social/list_social_accounts.php', array(
      'social_accounts' => $social_accounts,
      'providers'       => $providers,
      'can_add'         => $can_add,
      'can_delete'      => $can_delete,
    ));
  }

  /**
   * Deletes a social account record from current user.
   *
   * @param  Request $request
   * @param  string  $provider_id
   * @return Response
   * @throws NotFoundHttpException
   */
  public function deleteAction(Request $request, $provider_id)
  {
    if (empty($this->app['Auth']['features']['social_accounts'])) $this->app['auth']->abort();
    $this->app['auth']->checkAuthorization();

    $social_account = $this->app['auth.model.user_social_account']->findSocialAccountByUser($this->app['auth']->getUser(), $provider_id);
    if (!$social_account) throw new NotFoundHttpException('Social account not found.');
    $social_account->delete();

    return new RedirectResponse($this->app['url_generator']->generate('auth.social.list_social_accounts'));
  }

  /**
   * Finds a provider from its service id.
   *
   * @param string $provider
   * @return \Anthem\Auth\Social\BaseSocialAuthProvider
   * @throws \InvalidArgumentException
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  protected function getProvider($provider)
  {
    try
    {
      if (!isset($this->app['Auth']['social_auth'][$provider])) throw new \InvalidArgumentException();
      /** @var \Anthem\Auth\Social\BaseSocialAuthProvider $provider */
      $provider = $this->app[$provider];
      if (!$provider instanceof BaseSocialAuthProvider) throw new \InvalidArgumentException();
    }
    catch (\Exception $e)
    {
      throw new NotFoundHttpException('Unknown social auth provider.');
    }
    return $provider;
  }

  /**
   * Registers a new user from social network account.
   *
   * @param \Anthem\Auth\Social\BaseSocialAuthProvider $provider
   * @param array $attach_account_data
   * @return Response
   */
  protected function registerFromSocialAccount(BaseSocialAuthProvider $provider, $attach_account_data)
  {
    // TODO: put this in model service?

    // Init user record.  Do not set salt/password.  User will be able to login only through social account
    // until s/he changes password manually
    $user = new User();
    $user->setEmail($provider->getProperty('email'));

    // Init social account record
    $social_account = new UserSocialAccount();
    $social_account->setUser($user);
    $social_account->setProvider($attach_account_data['provider']);
    $social_account->setRemoteUserId($attach_account_data['remote_user_id']);
    $social_account->setTitle($attach_account_data['user_display_name']);

    // Persist these records
    $user->save();

    // Issue events
    $event = new UserEvent($user);
    $this->app['dispatcher']->dispatch(AuthEvents::USER_REGISTER, $event);
    $event = new UserAttachSocialEvent($user, $social_account, $provider);
    $this->app['dispatcher']->dispatch(AuthEvents::USER_ATTACH_SOCIAL, $event);

    // Clear temp data from session
    $this->app['session']->remove('anthem.auth.attach_social_account');

    // Log in new user
    $this->app['auth']->logon($user);
    return $this->redirectAfterLogin();
  }

  /**
   * Redirects user to appropriate page after successful login.
   * By default redirect goes to home page, but it can be set to something else
   * via anthem.auth.login_redir session var, set by AuthController::loginAction().
   * This parameter is stored in session, as it generally cannot be passed around via GET var
   * to and from 3rd party social service.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  protected function redirectAfterLogin()
  {
    // Determine URL to redirect to
    $url = $this->app['session']->get('anthem.auth.login_redir');
    if (!$url) $url = $this->app['request']->getBaseUrl() . '/';
    $this->app['session']->remove('anthem.auth.login_redir');

    // Redirect either to home page or to requested page upon successful login.
    return new RedirectResponse($url);
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
}