<?php

namespace Anthem\Auth\EventHandler;

use Symfony\Component\HttpFoundation\Session;
use Anthem\Auth\EventHandler\BaseLoginHandler;
use Anthem\Auth\Model\User;

/**
 * Session login handler.  Stores user id into session.
 */
class SessionLoginHandler extends BaseLoginHandler
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
   * @param  boolean                 $automatic
   * @return void
   */
  protected function login(User $user, $automatic)
  {
    // Regenerate session ID
    if (!$automatic) $this->session->migrate();

    // Store user
    $this->session->set('anthem.auth.user', $user->getId());
  }
}
