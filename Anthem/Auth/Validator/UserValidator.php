<?php

namespace Anthem\Auth\Validator;

use Anthem\Forms\Validator\BaseValidator;
use Anthem\Auth\ModelService\UserService;

/**
 * A validator that checks that username matches a valid user.
 *
 * Options:
 * - message (string) Custom message.
 */
class UserValidator extends BaseValidator
{
  /**
   * @var \Anthem\Auth\ModelService\UserService
   */
  protected $user_service;

  public function __construct(UserService $user_service, array $options = array())
  {
    $this->user_service = $user_service;
    parent::__construct($options);
  }

  /**
   * Performs validation.
   *
   * @param  mixed $value
   * @return boolean|string
   */
  public function validate($value)
  {
    if ($value)
    {
      $user = $this->user_service->findUserByEmail($value);
      if (!$user)
        return _t('Auth.USER_VALIDATOR_MESSAGE');
    }
    return true;
  }
}