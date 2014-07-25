<?php

namespace Anthem\Auth\EventHandler;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Cookie;
use Anthem\Auth\EventHandler\BaseLoginHandler;
use Anthem\Auth\Model\User;
use Anthem\Auth\ModelService\UserKeyService;

/**
 * "Remember Me" login handler.  Sets "Remember Me" cookie.
 */
class RememberMeLoginHandler extends BaseLoginHandler
{
  /**
   * @var \Anthem\Auth\ModelService\UserKeyService
   */
  protected $key_service = null;

  /**
   * @var integer
   */
  protected $max_age = null;

  /**
   * @var boolean
   */
  protected $secure = null;

  /**
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request = null;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcher
   */
  protected $dispatcher = null;

  /**
   * @var \Anthem\Auth\Model\UserKey
   */
  protected $key = null;

  /**
   * The constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Request          $request
   * @param \Anthem\Auth\ModelService\UserKeyService           $key_service
   * @param integer                                            $max_age
   * @param boolean                                            $secure
   * @param \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
   */
  public function __construct(Request $request, UserKeyService $key_service, $max_age, $secure, EventDispatcher $dispatcher)
  {
    $this->request     = $request;
    $this->key_service = $key_service;
    $this->max_age     = $max_age;
    $this->secure      = $secure;
    $this->dispatcher  = $dispatcher;
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
    if (!$this->max_age) return;

    // Handle "remember" parameter on manual login
    if (!$automatic && $this->request->get('remember') && $this->max_age)
    {
      // Generate key
      $this->key = $this->key_service->createKey($user, 'remember_me', $this->request->getClientIp(true));

      // Add an event handler to set cookie with key
      $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));
    }
  }

  /**
   * KernelEvents::RESPONSE handler.  Adds a cookie.
   *
   * @param  \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   * @return void
   */
  public function onKernelResponse(FilterResponseEvent $event)
  {
    $cookie = new Cookie('REMEMBER', $this->key->getUniqid(), time() + $this->max_age, '/', null, $this->secure);
    $event->getResponse()->headers->setCookie($cookie);
  }
}
