<?php

namespace Anthem\Auth\Event;

use Symfony\Component\EventDispatcher\Event;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserSocialAccount;
use Anthem\Auth\Social\BaseSocialAuthProvider;

/**
 * Login check event.  Passes user and attached social account to possible event handlers.
 */
class UserAttachSocialEvent extends Event
{
  protected $user = null;
  protected $social_account = null;
  protected $provider = null;

  public function __construct(User $user, UserSocialAccount $social_account, BaseSocialAuthProvider $provider)
  {
    $this->user           = $user;
    $this->social_account = $social_account;
    $this->provider       = $provider;
  }

  public function getUser()
  {
    return $this->user;
  }

  public function getSocialAccount()
  {
    return $this->social_account;
  }

  public function getProvider()
  {
    return $this->provider;
  }
}