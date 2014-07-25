<?php

namespace Anthem\Auth\Input;

use Silex\Application;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\VirtualInputInterface;
use Anthem\Auth\Model\UserQuery;

/**
 * An input field for user id selection.
 */
class UserInput extends StringInput
                implements VirtualInputInterface
{
  /**
   * Loads a value from object.
   *
   * @param  \BaseObject $object
   * @return void
   */
  public function load($object)
  {
    $user_id = $object->getByName($this->getName(), \BasePeer::TYPE_FIELDNAME);
    if ($user_id)
    {
      $user = UserQuery::create()
                       ->findPk($user_id);
      $value = $user->getEmail();
      parent::setValue($value);
    }
  }

  /**
   * Saves a value into object.
   *
   * @param  $object
   * @return void
   */
  public function save($object)
  {
    $value = null;
    if ($this->value)
    {
      $user = UserQuery::create()
                       ->findOneByEmail($this->value);
      if ($user) $value = $user->getId();
    }

    $object->setByName($this->getName(), $value, \BasePeer::TYPE_FIELDNAME);
  }
}
