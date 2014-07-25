<?php

namespace Anthem\Auth\EventHandler;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Anthem\Auth\AuthEvents;
use Anthem\Auth\Event\UserAttachSocialEvent;
use Anthem\Auth\Social\BaseSocialAuthProvider;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserSocialAccount;

/**
 * Base social account attach handler class.  Listens to AuthEvent::USER_SOCIAL_ATTACH events.
 */
abstract class BaseUserAttachSocialHandler implements EventSubscriberInterface
{
  /**
   * The actual worker function.
   *
   * @param  \Anthem\Auth\Model\User $user
   * @param  \Anthem\Auth\Model\UserSocialAccount $social_account
   * @param  \Anthem\Auth\Social\BaseSocialAuthProvider $provider
   * @return void
   */
  abstract protected function handle(User $user, UserSocialAccount $social_account, BaseSocialAuthProvider $provider);

  /**
   * AuthEvents::LOGOUT handler.  Passes actual work to logout() method.
   *
   * @param  \Anthem\Auth\Event\UserAttachSocialEvent $event
   * @return void
   */
  public function onUserAttachSocial(UserAttachSocialEvent $event)
  {
    $this->handle($event->getUser(), $event->getSocialAccount(), $event->getProvider());
  }

  /**
   * Return events to subscribe for.
   *
   * @return array
   */
  static function getSubscribedEvents()
  {
    return array(
      AuthEvents::USER_ATTACH_SOCIAL => 'onUserAttachSocial',
    );
  }
}