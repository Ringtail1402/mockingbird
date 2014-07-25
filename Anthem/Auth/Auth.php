<?php

namespace Anthem\Auth;

use Silex\Application;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserKey;
use Anthem\Auth\AuthEvents;
use Anthem\Auth\Event\LoginCheckEvent;
use Anthem\Auth\Event\LoginEvent;
use Anthem\Auth\Event\LogoutEvent;

/**
 * Worker service for Auth module.
 */
class Auth
{
  /**
   * @var \Silex\Application
   */
  protected $app;

  /**
   * @var \Anthem\Auth\Model\User
   */
  protected $user;

  /**
   * @var array
   */
  protected $policies = array();

  /**
   * The constructor.
   *
   * @param \Silex\Application $app
   */
  public function __construct(Application $app, $force_disable = false)
  {
    $this->app = $app;

    // Default to guest user
    $this->user = $this->createGuest();

    // Allow shutting down entire auth infrastructure, e.g. for CLI
    if ($force_disable)
    {
      $this->elevateToRoot();
      return;
    }

    // Ensure HTTPS setting matches
    if (isset($app['request']))
    {
      $this->checkHTTPS($app['Auth']['https']);
    }

    // Perform login check
    $event = new LoginCheckEvent($app['request']);
    $app['dispatcher']->dispatch(AuthEvents::LOGIN_CHECK, $event);
    $user = $event->getUser();
    if ($user)
    {
      if (!$user->getLocked())
        $this->logon($user, true);
      else
      {
        // User locked while logged on
        $this->logout();
        $app['notify']->addTransient(_t('LOCK_REASON.FULL.' . $user->getLocked()), 'error');
      }
    }
  }

  /**
   * Creates a guest user object.
   *
   * @return \Anthem\Auth\Model\User
   */
  public function createGuest()
  {
    $user = new User();
    $user->setEmail('guest@localhost');
    return $user;
  }

  /**
   * Is no one currently logged on?
   *
   * @return boolean
   */
  public function isGuest()
  {
    return $this->user->isNew();
  }

  /**
   * Does the current user have specified policies?
   *
   * @param array|string $policy
   * @return boolean
   */
  public function hasPolicies($policies)
  {
    if ($this->user->getIsSuperuser()) return true;  // Superuser can do anything

    if (is_string($policies)) $policies = array($policies);

    foreach ($policies as $policy)
      if (empty($this->policies[$policy])) return false;
    return true;
  }

  /**
   * Returns currently logged on user (or guest).
   *
   * @return \Anthem\Auth\Model\User
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * Checks that user is appropriately authorized.
   *
   * @param boolean $redirect Redirect to login if a guest, otherwise 403.
   * @return void
   */
  public function checkAuthorization($redirect = true)
  {
    if ($this->isGuest())
    {
      if ($redirect)
      {
        // XXX It is presumably incorrect to short circuit execution like this.
        $redir = ltrim($this->app['request']->getPathInfo(), '/');
        $this->app->redirect($this->app['url_generator']->generate('auth.login') . ($redir ? '?redir=' . urlencode($redir) : ''))->send();
        die;
      }
      else
        $this->abort();
    }
  }

  /**
   * Checks that user has appropriate policies.
   *
   * @param array|string $policies
   * @return void
   */
  public function checkPolicies($policies)
  {
    if (!$this->hasPolicies($policies)) $this->abort();
  }

  /**
   * Aborts app execution due to insufficient permissions.
   *
   * @return never
   */
  public function abort()
  {
    $this->app->abort(403, 'Insufficient permissions');
  }

  /**
   * Grants currently logged on user (or guest) super-user status.
   * Naturally this is dangerous and should not be called unless really required.
   *
   * @return \Anthem\Auth\Model\User
   */
  public function elevateToRoot()
  {
    if ($this->user->getIsSuperuser()) return;

    if ($this->user->isNew())
      $this->user->setEmail('root@localhost');
    else
      $this->user = $this->user->copy();
    $this->user->setIsSuperuser(true);
    return $this->user;
  }

  /**
   * Logs the user in.
   *
   * @param \Anthem\Auth\Model\User $user
   * @param boolean                 $automatic
   * @return void
   * @throws \LogicException
   */
  public function logon(User $user, $automatic = false)
  {
    if ($user->isNew())
      throw new \LogicException('Cannot log in a non-saved user.');

    // Store new user and his policies
    $this->user = $user;
    $this->policies = $this->getPolicies($user);

    // Call login handlers
    $event = new LoginEvent($user);
    $this->app['dispatcher']->dispatch($automatic ? AuthEvents::LOGIN_AUTO : AuthEvents::LOGIN_MANUAL, $event);

    // Update user's last login time
    $user->setLastLogin(time());
    $user->save();
  }

  /**
   * Logs the user out.
   *
   * @return void
   */
  public function logout()
  {
    // Call logout handlers
    $event = new LogoutEvent($this->user);
    $this->app['dispatcher']->dispatch(AuthEvents::LOGOUT, $event);

    // Reset user and policies
    $this->user = $this->createGuest();
    $this->policies = array();
  }

  /**
   * Retrieves all user's policies.
   *
   * @param \Anthem\Auth\Model\User $user
   * @param boolean|null            $inherited true = only group policies, false = only own policies,
   *                                           default is combined
   * @return array
   */
  public function getPolicies(User $user, $inherited = null)
  {
    $result = array();

    // Group policies
    if ($inherited !== false)
    {
      foreach ($user->getGroups() as $group)
      {
        foreach ($group->getPolicys() as $policy)
          $result[$policy->getPolicy()] = true;
      }
    }

    // User policies, which may override group policies
    if ($inherited !== true)
    {
      foreach ($user->getPolicys() as $policy)
        $result[$policy->getPolicy()] = $policy->getEnable();
    }

    return $result;
  }

  /**
   * Finds a user by credentials.
   *
   * @param string $email
   * @param string $password
   * @return \Anthem\Auth\Model\User|null
   */
  public function checkUser($email, $password)
  {
    $user = $this->app['auth.model.user']->findUserByEmail($email);
    if ($user)
      return $this->checkPassword($user, $password) ? $user : null;
    else
      return null;
  }

  /**
   * Checks that the user password is valid.
   *
   * @param \Anthem\Auth\Model\User $user
   * @param string                  $password
   * @return boolean
   */
  public function checkPassword($user, $password)
  {
    if (!$user->getPassword())
      return false;  // won't log in passwordless users

    $algorithm = $user->getAlgorithm();
    $salt = $user->getSalt();
    return ($user->getPassword() == hash($algorithm, $salt . '|' . $password));
  }

  /**
   * Sets a new hashed password for user.
   *
   * @param \Anthem\Auth\Model\User $user
   * @param string                  $password
   * @return void
   */
  public function changePassword(User $user, $password)
  {
    if (!$password)
    {
      $user->setAlgorithm(null);
      $user->setSalt(null);
      $user->setPassword(null);
      return;
    }

    $algorithm = $this->app['Auth']['hash'];
    $salt = uniqid($this->app['Core']['project'] . '|');
    $password = hash($algorithm, $salt . '|' . $password);
    $user->setAlgorithm($algorithm);
    $user->setSalt($salt);
    $user->setPassword($password);
  }

  /**
   * Checks that protocol (http/https) of current request matches the setting.
   * Redirects to appropriate protocol and exits if necessary.
   *
   * @param string $strategy
   * @return void
   */
  public function checkHTTPS($strategy)
  {
    /** @var \Symfony\Component\HttpFoundation\Request $request */
    $request = $this->app['request'];

    switch ($strategy)
    {
      case 'never':
        // Always downgrade to HTTP
        if ($request->isSecure()) $this->redirectHTTPS(false);
        break;

      case 'always':
        // Always upgrade to HTTPS
        if (!$request->isSecure()) $this->redirectHTTPS(true);
        break;
    }
  }

  /**
   * Redirects to https or http version of current page and exits, if protocol doesn't match.
   *
   * @param boolean $https
   * @return void|never
   */
  public function redirectHTTPS($https)
  {
    $uri = $this->app['request']->getURI();
    $uri = preg_replace('/^' . $this->app['request']->getScheme() . '/', $https ? 'https' : 'http', $uri);
    if ($uri != $this->app['request']->getURI())
    {
      $this->app->redirect($uri)->send();
      die;
    }
  }

  /**
   * Do we need to use https-only cookies for session etc?
   *
   * @return boolean
   */
  public function needSecureCookies()
  {
    return ($this->app['Auth']['https'] == 'always' || $this->app['Auth']['https'] == 'auth');
  }
}
