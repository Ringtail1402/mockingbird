<?php

namespace Anthem\Auth\EventHandler;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Anthem\Auth\AuthEvents;
use Anthem\Auth\Event\LoginCheckEvent;

/**
 * Base login checker class.  Listens to AuthEvent::LOGIN_CHECK events.
 */
abstract class BaseLoginCheckHandler implements EventSubscriberInterface
{
  /**
   * The actual worker function.  Returns a user to be logged in, or null if failed.
   *
   * @param  \Symfony\Component\HttpFoundation\Request $request
   * @return \Anthem\Auth\Model\User|null
   */
  abstract protected function check(Request $request);

  /**
   * AuthEvents::LOGIN_CHECK handler.  Passes actual work to check() method.
   *
   * @param  \Anthem\Auth\Event\LoginCheckEvent $event
   * @return void
   */
  public function onLoginCheck(LoginCheckEvent $event)
  {
    if ($event->getUser()) return;  // User already found

    $user = $this->check($event->getRequest());
    if ($user) $event->setUser($user);
  }

  /**
   * Return events to subscribe for.
   *
   * @return array
   */
  static function getSubscribedEvents()
  {
    return array(
      AuthEvents::LOGIN_CHECK => 'onLoginCheck'
    );
  }
}