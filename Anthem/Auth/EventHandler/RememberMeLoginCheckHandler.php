<?php

namespace Anthem\Auth\EventHandler;

use Symfony\Component\HttpFoundation\Request;
use Anthem\Auth\EventHandler\BaseLoginCheckHandler;
use Anthem\Auth\ModelService\UserKeyService;

/**
 * "Remember Me" login check handler.  Tries to find user by "Remember Me" cookie.
 */
class RememberMeLoginCheckHandler extends BaseLoginCheckHandler
{
  /**
   * @var \Anthem\Auth\ModelService\UserKeyService
   */
  protected $key_service = null;

  /**
   * @var integer
   */
  protected $max_age;

  /**
   * The constructor.
   *
   * @param \Anthem\Auth\ModelService\UserKeyService           $key_service
   * @param integer                                            $max_age
   */
  public function __construct(UserKeyService $key_service, $max_age)
  {
    $this->key_service = $key_service;
    $this->max_age     = $max_age;
  }

  /**
   * The actual worker function.  Returns a user to be logged in, or null if failed.
   *
   * @param  \Symfony\Component\HttpFoundation\Request $request
   * @return \Anthem\Auth\Model\User|null
   */
  protected function check(Request $request)
  {
    if (!$this->max_age) return null;

    // Look for a cookie
    $uniqid = $request->cookies->get('REMEMBER');
    if ($uniqid)
    {
      // Look for a matching key
      $key = $this->key_service->findKey('remember_me', $uniqid, $request->getClientIp(true));
      if ($key && time() - $key->getCreatedAt('U') < 86400 * $this->max_age) return $key->getUser();
    }
    return null;
  }
}
