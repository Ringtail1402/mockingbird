<?php

namespace Anthem\Auth\EventHandler;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Anthem\Auth\AuthEvents;
use Anthem\Auth\Event\LoginEvent;
use Anthem\Auth\Model\User;

/**
 * Base login handler class.  Listens to AuthEvent::LOGIN_MANUAL/LOGIN_AUTO events.
 */
abstract class BaseLoginHandler implements EventSubscriberInterface
{
  /**
   * The actual worker function.
   *
   * @param  \Anthem\Auth\Model\User $user
   * @param  boolean                 $automatic
   * @return void
   */
  abstract protected function login(User $user, $automatic);

  /**
   * AuthEvents::LOGIN_AUTO handler.  Passes actual work to login() method.
   *
   * @param  \Anthem\Auth\Event\LoginEvent $event
   * @return void
   */
  public function onLoginAuto(LoginEvent $event)
  {
    $this->login($event->getUser(), true);
  }

  /**
   * AuthEvents::LOGIN_MANUAL handler.  Passes actual work to login() method.
   *
   * @param  \Anthem\Auth\Event\LoginEvent $event
   * @return void
   */
  public function onLoginManual(LoginEvent $event)
  {
    $this->login($event->getUser(), false);
  }

  /**
   * Return events to subscribe for.
   *
   * @return array
   */
  static function getSubscribedEvents()
  {
    return array(
      AuthEvents::LOGIN_AUTO   => 'onLoginAuto',
      AuthEvents::LOGIN_MANUAL => 'onLoginManual',
    );
  }
}