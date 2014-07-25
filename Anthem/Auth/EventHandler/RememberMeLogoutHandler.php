<?php

namespace Anthem\Auth\EventHandler;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Anthem\Auth\EventHandler\BaseLogoutHandler;
use Anthem\Auth\Model\User;
use Anthem\Auth\ModelService\UserKeyService;

/**
 * "Remember Me" logout handler.  Unsets "Remember Me" cookie.
 */
class RememberMeLogoutHandler extends BaseLogoutHandler
{
  /**
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request = null;

  /**
   * @var \Anthem\Auth\ModelService\UserKeyService
   */
  protected $key_service = null;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcher
   */
  protected $dispatcher = null;

  /**
   * The constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Request          $request
   * @param \Anthem\Auth\ModelService\UserKeyService           $key_service
   * @param \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
   */
  public function __construct(Request $request, UserKeyService $key_service, EventDispatcher $dispatcher)
  {
    $this->request     = $request;
    $this->key_service = $key_service;
    $this->dispatcher  = $dispatcher;
  }

  /**
   * The actual worker function.
   *
   * @param  \Anthem\Auth\Model\User $user
   * @return void
   */
  protected function logout(User $user)
  {
    // Delete key
    $this->key_service->deleteKey($user, 'remember_me', $this->request->getClientIp(true));

    // Add an event handler to remove cookie with key
    $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));
  }

  /**
   * KernelEvents::RESPONSE handler.  Removes a cookie.
   *
   * @param  \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   * @return void
   */
  public function onKernelResponse(FilterResponseEvent $event)
  {
    $event->getResponse()->headers->clearCookie('REMEMBER');
  }
}
