<?php

namespace Anthem\Auth\Event;

use Anthem\Auth\Event\UserEvent;

/**
 * Logout event.  Passes User to possible event handlers.
 */
class LogoutEvent extends UserEvent
{
}
