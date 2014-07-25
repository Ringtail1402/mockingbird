<?php

namespace Anthem\Auth\Form;

use Silex\Application;
use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\EmailValidator;

/**
 * User email change form.
 */
class ChangeEmailForm extends Form
{
  public function __construct(Application $app)
  {
    parent::__construct($app, array('email' => ''), array(
      'fields' => array(
        'email' => new StringInput($app, array(
          'label'     => _t('Auth.EMAIL'),
          'validator' => array(
            new RequiredValidator(),
            new EmailValidator(),
          ),
        )),
      ),
    ));
  }

  public function save()
  {
    $result = parent::save();
    return $result['email'];
  }
}