<?php

namespace Anthem\Auth\EventHandler;

use Symfony\Component\HttpFoundation\Request;
use Anthem\Auth\EventHandler\BaseLoginCheckHandler;
use Anthem\Auth\ModelService\UserService;

/**
 * Session login check handler.  Tries to retrieve user from session.
 */
class SessionLoginCheckHandler extends BaseLoginCheckHandler
{
  /**
   * @var \Anthem\Auth\ModelService\UserService
   */
  protected $user_service = null;

  /**
   * The constructor.
   *
   * @param \Anthem\Auth\ModelService\UserService              $user_service
   */
  public function __construct(UserService $user_service)
  {
    $this->user_service = $user_service;
  }

  /**
   * The actual worker function.  Returns a user to be logged in, or null if failed.
   *
   * @param  \Symfony\Component\HttpFoundation\Request $request
   * @return \Anthem\Auth\Model\User|null
   */
  protected function check(Request $request)
  {
    $session = $request->getSession();
    if ($session)
    {
      $uid = $session->get('anthem.auth.user');
      if ($uid) return $this->user_service->findUser($uid);
    }
    return null;
  }
}
