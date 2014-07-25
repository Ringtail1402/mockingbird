<?php

namespace Anthem\Auth\Form;

use Silex\Application;
use Anthem\Forms\Form\Form;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserQuery;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\PasswordInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\EmailValidator;
use Anthem\Forms\Validator\UniqueValidator;
use Anthem\Auth\Validator\PasswordEqualityValidator;

/**
 * User registration form.
 */
class RegisterForm extends Form
{
  public function __construct(Application $app, array $extra_fields = array())
  {
    $user = new User();
    $user->password2 = '';  // Emulate a non-existent field

    $options = array(
      'fields' => array(
        'email'    => new StringInput($app, array(
          'label'     => _t('Auth.EMAIL'),
          'validator' => array(
            new RequiredValidator(),
            new EmailValidator(),
            new UniqueValidator(array(
              'query' => function ($email) use ($app) {
                return UserQuery::create()
                                ->filterByEmail($email);
              },
              'message' => _t('Auth.UNIQUE_EMAIL_VALIDATOR_MESSAGE'),
            ))
          ),
        )),
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
    );

    $options['fields'] = array_merge($options['fields'], $extra_fields);

    parent::__construct($app, $user, $options);
  }

  public function save()
  {
    $user = parent::save();
    $this->app['auth']->changePassword($user, $user->getPassword());
    return $user;
  }
}