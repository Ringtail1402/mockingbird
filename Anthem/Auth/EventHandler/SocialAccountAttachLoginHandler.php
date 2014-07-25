<?php

namespace Anthem\Auth\EventHandler;

use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session;
use Anthem\Auth\AuthEvents;
use Anthem\Auth\Event\UserAttachSocialEvent;
use Anthem\Auth\EventHandler\BaseLoginHandler;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserSocialAccount;

/**
 * Login handler that attached authenticated social network account to app's own User account.
 */
class SocialAccountAttachLoginHandler extends BaseLoginHandler
{
  /**
   * @var \Silex\Application
   */
  protected $app = null;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcher
   */
  protected $dispatcher = null;

  /**
   * @var \Symfony\Component\HttpFoundation\Session
   */
  protected $session = null;

  /**
   * The constructor.
   * XXX Takes in Application object to retrieve providers by their id.  Seems unavoidable.
   *
   * @param \Silex\Application                                 $app
   * @param \Symfony\Component\HttpFoundation\Session          $session
   * @param \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
   */
  public function __construct(Application $app, Session $session, EventDispatcher $dispatcher)
  {
    $this->app        = $app;
    $this->dispatcher = $dispatcher;
    $this->session    = $session;
  }

  /**
   * The actual worker function.
   *
   * @param  \Anthem\Auth\Model\User $user
   * @param  boolean                 $automatic
   * @return void
   */
  protected function login(User $user, $automatic)
  {
    if (!$automatic && $this->session)
    {
      $attach_social_account = $this->session->get('anthem.auth.attach_social_account');
      if ($attach_social_account)
      {
        // Create new UserSocialAccount record
        $social_account = new UserSocialAccount();
        $social_account->setUser($user);
        $social_account->setProvider($attach_social_account['provider']);
        $social_account->setRemoteUserId($attach_social_account['remote_user_id']);
        $social_account->setTitle($attach_social_account['user_display_name']);
        $social_account->save();
        $this->session->remove('anthem.auth.attach_social_account');

        // Issue an event
        $provider = $this->app[$social_account->getProvider()];
        $event = new UserAttachSocialEvent($user, $social_account, $provider);
        $this->dispatcher->dispatch(AuthEvents::USER_ATTACH_SOCIAL, $event);
      }
    }
  }
}
