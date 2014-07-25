<?php

namespace Anthem\Auth\Model;

use Anthem\Auth\Model\om\BaseUser;

class User extends BaseUser
{
  public function isEmailValid()
  {
    return preg_match('/^[^@\s]+@[^\s.]+\.[^\s]+$/', $this->getEmail());
  }
}
