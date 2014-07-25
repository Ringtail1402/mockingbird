<?php

namespace Anthem\Auth\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Anthem\Auth\Model\User;

/**
 * Login check event.  Passes Request to possible event handlers, which may set user.
 */
class LoginCheckEvent extends Event
{
  protected $request = null;

  protected $user = null;

  public function __construct(Request $request)
  {
    $this->request = $request;
  }

  public function getRequest()
  {
    return $this->request;
  }

  public function getUser()
  {
    return $this->user;
  }

  public function setUser(User $user)
  {
    $this->user = $user;
  }
}