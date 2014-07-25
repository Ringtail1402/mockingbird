<?php

namespace Anthem\Auth\ModelService;

use Anthem\Propel\ModelService\PropelModelService;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserKey;
use Anthem\Auth\Model\UserKeyQuery;

/**
 * User key model service.
 */
class UserKeyService extends PropelModelService
{
  /**
   * @var string
   */
  protected $algorithm;

  /**
   * @var string
   */
  protected $prefix;

  /**
   * The constructor
   * @param string $algorithm
   * @param string $prefix
   */
  public function __construct($algorithm, $prefix)
  {
    $this->algorithm = $algorithm;
    $this->prefix    = $prefix;
  }

  /**
   * Creates a unique key of specified type.  Overwrites any previous key.
   *
   * @param \Anthem\Auth\Model\User $user
   * @param string                  $type
   * @param string|null             $data
   * @return \Anthem\Auth\Model\UserKey
   */
  public function createKey(User $user, $type, $data = null)
  {
    $this->deleteKey($user, $type);

    $key = new UserKey();
    $key->setUser($user);
    $key->setType($type);
    $key->setUniqid(hash($this->algorithm,
        uniqid($this->prefix . '|' . $type . '|' . $user->getId() . '|', true)));
    $key->setData($data);
    $key->save();
    return $key;
  }

  /**
   * Finds a specified key.
   *
   * @param string      $type
   * @param string      $uniqid
   * @param string|null $data
   * @return \Anthem\Auth\Model\UserKey|null
   */
  public function findKey($type, $uniqid, $data = null)
  {
    return UserKeyQuery::create()
                       ->filterByUniqid($uniqid)
                       ->filterByType($type)
                       ->_if($data)
                         ->filterByData($data)
                       ->_endif()
                       ->joinUser()
                       ->with('User')
                       ->findOne();
  }

  /**
   * Checks that the specified key is valid.  Return false if invalid, true if valid with no data
   * associated, otherwise any associated data.
   *
   * @param \Anthem\Auth\Model\User $user
   * @param string                  $type
   * @param string                  $uniqid
   * @param string|null             $data
   * @return bool|string
   */
  public function checkKey(User $user, $type, $uniqid, $data = null)
  {
    $key = UserKeyQuery::create()
                       ->filterByUniqid($uniqid)
                       ->filterByUser($user)
                       ->filterByType($type)
                       ->_if($data)
                         ->filterByData($data)
                       ->_endif()
                       ->findOne();
    if (!$key) return false;
    if (!$key->getData()) return true;
    return $key->getData();
  }

  /**
   * Deletes a unique key of specified type, if any.
   *
   * @param \Anthem\Auth\Model\User $user
   * @param string                  $type
   * @param string|null             $data
   * @return void
   */
  public function deleteKey(User $user, $type, $data = null)
  {
    UserKeyQuery::create()
                ->filterByUser($user)
                ->filterByType($type)
                ->_if($data)
                  ->filterByData($data)
                ->_endif()
                ->delete();
  }

  /**
   * Returns full name of the class managed by this service.
   *
   * @return string
   */
  public function getModelClass()
  {
    return 'Anthem\\Auth\\Model\\UserKey';
  }
}