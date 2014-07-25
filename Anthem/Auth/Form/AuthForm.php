<?php

namespace Anthem\Auth\Form;

use Silex\Application;
use Anthem\Forms\Form\Form;
use Anthem\Auth\Model\User;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\PasswordInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Auth\Validator\AuthValidator;

/**
 * User authentication form.
 */
class AuthForm extends Form
{
  public function __construct(Application $app)
  {
    parent::__construct($app, new User(), array(
      'name' => '_login',
      'fields' => array(
        'email'    => new StringInput($app, array(
          'label'     => _t('Auth.EMAIL'),
          'validator' => new RequiredValidator()
        )),
        'password' => new PasswordInput($app, array(
          'label'     => _t('Auth.PASSWORD'),
          'validator' => new RequiredValidator()
        )),
      ),
      'validator' => new AuthValidator($app),
    ));
  }

  /**
   * Returns valid user for log on.
   *
   * @return \Anthem\Auth\Model\User
   */
  public function save()
  {
    return $this->options['validator']->getUser();
  }
}