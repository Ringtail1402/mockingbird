<?php

namespace Anthem\Auth\EventHandler;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Anthem\Auth\AuthEvents;
use Anthem\Auth\Event\UserEvent;
use Anthem\Auth\Model\User;

/**
 * Base registration handler class.  Listens to AuthEvent::USER_REGISTER events.
 */
abstract class BaseUserRegisterHandler implements EventSubscriberInterface
{
  /**
   * The actual worker function.
   *
   * @param  \Anthem\Auth\Model\User $user
   * @return void
   */
  abstract protected function handle(User $user);

  /**
   * AuthEvents::LOGOUT handler.  Passes actual work to logout() method.
   *
   * @param  \Anthem\Auth\Event\UserEvent $event
   * @return void
   */
  public function onUserRegister(UserEvent $event)
  {
    $this->handle($event->getUser());
  }

  /**
   * Return events to subscribe for.
   *
   * @return array
   */
  static function getSubscribedEvents()
  {
    return array(
      AuthEvents::USER_REGISTER => 'onUserRegister',
    );
  }
}