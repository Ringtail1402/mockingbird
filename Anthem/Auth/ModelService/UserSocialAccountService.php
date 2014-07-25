<?php

namespace Anthem\Auth\ModelService;

use Anthem\Propel\ModelService\PropelModelService;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserSocialAccountQuery;

/**
 * User social account model service.
 */
class UserSocialAccountService extends PropelModelService
{
  /**
   * Finds a social account with an associated User record.
   *
   * @param string $provider
   * @param string $remote_user_id
   * @return \Anthem\Auth\Model\UserSocialAccount|null
   */
  public function findSocialAccount($provider, $remote_user_id)
  {
    return UserSocialAccountQuery::create()
                                 ->filterByProvider($provider)
                                 ->filterByRemoteUserId($remote_user_id)
                                 ->joinUser()
                                 ->with('User')
                                 ->findOne();
  }

  /**
   * Finds a social account for a user record.
   *
   * @param User $user
   * @param string $provider
   * @return \Anthem\Auth\Model\UserSocialAccount|null
   */
  public function findSocialAccountByUser(User $user, $provider)
  {
    return UserSocialAccountQuery::create()
                                 ->filterByProvider($provider)
                                 ->filterByUser($user)
                                 ->findOne();
  }


  /**
   * Returns full name of the class managed by this service.
   *
   * @return string
   */
  public function getModelClass()
  {
    return 'Anthem\\Auth\\Model\\UserSocialAccount';
  }
}