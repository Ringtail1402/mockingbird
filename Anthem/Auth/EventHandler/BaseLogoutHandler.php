<?php

namespace Anthem\Auth\EventHandler;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Anthem\Auth\AuthEvents;
use Anthem\Auth\Event\LogoutEvent;
use Anthem\Auth\Model\User;

/**
 * Base logout handler class.  Listens to AuthEvent::LOGOUT events.
 */
abstract class BaseLogoutHandler implements EventSubscriberInterface
{
  /**
   * The actual worker function.
   *
   * @param  \Anthem\Auth\Model\User $user
   * @return void
   */
  abstract protected function logout(User $user);

  /**
   * AuthEvents::LOGOUT handler.  Passes actual work to logout() method.
   *
   * @param  \Anthem\Auth\Event\LoginEvent $event
   * @return void
   */
  public function onLogout(LogoutEvent $event)
  {
    $this->logout($event->getUser());
  }

  /**
   * Return events to subscribe for.
   *
   * @return array
   */
  static function getSubscribedEvents()
  {
    return array(
      AuthEvents::LOGOUT => 'onLogout',
    );
  }
}