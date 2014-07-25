<?php

namespace Anthem\Auth\ModelService;

use Anthem\Propel\ModelService\PropelModelService;
use Anthem\Auth\Model\UserQuery;

/**
 * User model service.
 */
class UserService extends PropelModelService
{
  /**
   * Finds a user by id.  Performs all joins.
   *
   * @param  integer $user_id
   * @return \Anthem\Auth\Model\User|null
   */
  public function findUser($user_id)
  {
    $user = UserQuery::create()
                     ->filterById($user_id)
                     // FIXME: joins
                     /*->leftJoinPolicy()
                     ->useRefGroupQuery(null, \Criteria::LEFT_JOIN)
                       ->useGroupQuery(null, \Criteria::LEFT_JOIN)
                         ->leftJoinPolicy('gp')
                       ->endUse()
                     ->endUse()
                     ->with('Policy')
                     ->with('Group')
                     ->with('gp')*/
                     ->findOne();
    return $user;
  }

  /**
   * Finds a user by email.
   *
   * @param string $email
   * @return \Anthem\Auth\Model\User|null
   */
  public function findUserByEmail($email)
  {
    return UserQuery::create()
                    ->findOneByEmail($email);
  }

  /**
   * Searches by user email.
   *
   * @param string $q
   * @return \Anthem\Auth\Model\User
   */
  public function searchUsers($q)
  {
    return UserQuery::create()
                    ->filterByEmail('%' . $q . '%')
                    ->orderByEmail()
                    ->find();
  }

  /**
   * Counts available users.
   *
   * @return integer
   */
  public function countUsers()
  {
    return UserQuery::create()
                    ->count();
  }

  /**
   * Returns full name of the class managed by this service.
   *
   * @return string
   */
  public function getModelClass()
  {
    return 'Anthem\\Auth\\Model\\User';
  }
}