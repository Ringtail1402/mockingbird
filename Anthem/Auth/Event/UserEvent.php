<?php

namespace Anthem\Auth\Event;

use Symfony\Component\EventDispatcher\Event;
use Anthem\Auth\Model\User;

/**
 * Generic user event.  Passes User to possible event handlers.
 */
class UserEvent extends Event
{
  protected $user = null;

  public function __construct(User $user)
  {
    $this->user = $user;
  }

  public function getUser()
  {
    return $this->user;
  }
}