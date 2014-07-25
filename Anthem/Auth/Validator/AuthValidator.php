<?php

namespace Anthem\Auth\Validator;

use Silex\Application;
use Anthem\Forms\Validator\BaseValidator;

/**
 * A validator that checks that username and password match a valid and unlocked user.
 *
 * Options:
 * - message (string) Custom message.
 * - email_field (string) Email field, defaults to "email".
 * - password_field (string) Email field, defaults to "password".
 */
class AuthValidator extends BaseValidator
{
  /**
   * @var \Silex\Application
   */
  protected $app;

  /**
   * @var \Anthem\Auth\Model\User
   */
  protected $user = null;

  /**
   * The constructor.
   *
   * @param \Silex\Application $app
   * @param array $options
   */
  public function __construct(Application $app, array $options = array())
  {
    $this->app = $app;
    parent::__construct($options);
  }

  /**
   * Returns validated user.
   *
   * @param \Anthem\Auth\Model\User
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * Performs validation.
   *
   * @param  mixed $value
   * @return boolean|string
   */
  public function validate($value)
  {
    $email_field    = isset($this->options['email_field'])    ? $this->options['email_field']    : 'email';
    $password_field = isset($this->options['password_field']) ? $this->options['password_field'] : 'password';
    $this->user = $this->app['auth']->checkUser($value[$email_field], $value[$password_field]);

    if (empty($this->user))
      return (isset($this->options['message']) ? $this->options['message'] : _t('Auth.AUTH_VALIDATOR_MESSAGE'));
    if ($this->user->getLocked())
      return _t('LOCK_REASON.FULL.' . $this->user->getLocked());
    return true;
  }
}