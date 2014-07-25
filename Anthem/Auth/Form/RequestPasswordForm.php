<?php

namespace Anthem\Auth\Form;

use Silex\Application;
use Anthem\Forms\Form\Form;
use Anthem\Auth\Model\User;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\ClosureValidator;

/**
 * Request new password form.
 */
class RequestPasswordForm extends Form
{
  public function __construct(Application $app)
  {
    parent::__construct($app, new User(), array(
      'fields' => array(
        'email' => new StringInput($app, array(
          'label'     => _t('Auth.EMAIL'),
          'validator' => array(
            new RequiredValidator(),
            new ClosureValidator(array(
              'closure' => function ($email) use ($app) {
                return (boolean) $app['auth.model.user']->findUserByEmail($email);
              },
              'message' => _t('Auth.REQUEST_PASSWORD_EMAIL_NONEXISTENT_VALIDATOR_MESSAGE')
            )),
            new ClosureValidator(array(
              'closure' => function ($email) use ($app) {
                $user = $app['auth.model.user']->findUserByEmail($email);
                return $user && $user->isEmailValid();
              },
              'message' => _t('Auth.REQUEST_PASSWORD_EMAIL_INVALID_VALIDATOR_MESSAGE')
            )),
          ),
        )),
      ),
    ));
  }

  public function save()
  {
    $user = parent::save();

    // Find actual user instead of a dummy record
    return $this->app['auth.model.user']->findUserByEmail($user->getEmail());
  }
}