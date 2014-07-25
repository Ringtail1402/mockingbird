<?php

namespace Anthem\Auth\EventHandler;

use Symfony\Component\HttpFoundation\Session;
use Anthem\Auth\EventHandler\BaseLogoutHandler;
use Anthem\Auth\Model\User;

/**
 * Session logout handler.  Destroys session with user id and other data.
 */
class SessionLogoutHandler extends BaseLogoutHandler
{
  /**
   * @var \Symfony\Component\HttpFoundation\Session
   */
  protected $session = null;

  /**
   * The constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Session          $session
   */
  public function __construct(Session $session)
  {
    $this->session = $session;
  }

  /**
   * The actual worker function.
   *
   * @param  \Anthem\Auth\Model\User $user
   * @return void
   */
  protected function logout(User $user)
  {
    // Clear out session
    $this->session->invalidate();
  }
}
