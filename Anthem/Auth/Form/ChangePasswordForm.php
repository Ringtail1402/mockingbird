<?php

namespace Anthem\Auth\Form;

use Silex\Application;
use Anthem\Forms\Form\Form;
use Anthem\Auth\Model\User;
use Anthem\Forms\Input\PasswordInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Auth\Validator\PasswordEqualityValidator;

/**
 * User password change form.
 */
class ChangePasswordForm extends Form
{
  public function __construct(Application $app, User $user)
  {
    $user->password2 = '';  // Emulate field
    parent::__construct($app, $user, array(
      'fields' => array(
        'password' => new PasswordInput($app, array(
          'label'     => _t('Auth.PASSWORD'),
          'validator' => new RequiredValidator()
        )),
        'password2' => new PasswordInput($app, array(
          'label'     => _t('Auth.PASSWORD2'),
          'validator' => new RequiredValidator(),
        )),
      ),
      'validator' => new PasswordEqualityValidator(),
    ));
  }

  public function save()
  {
    $user = parent::save();
    $this->app['auth']->changePassword($user, $user->getPassword());
    return $user;
  }
}