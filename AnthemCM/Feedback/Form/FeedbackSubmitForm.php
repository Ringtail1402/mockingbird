<?php

namespace AnthemCM\Feedback\Form;

use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\TextareaInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\EmailValidator;
use AnthemCM\Feedback\Model\Feedback;

/**
 * User feedback submission form.
 */
class FeedbackSubmitForm extends Form
{
  public function __construct($app)
  {
    $object = new Feedback();

    $fields = array();
    if ($app['auth']->isGuest())
    {
      $fields['email'] = new StringInput($app, array(
        'label'     => _t('Feedback.EMAIL'),
        'help'      => _t('Feedback.EMAIL_HELP'),
        'validator' => new EmailValidator(),
      ));
    }
    $fields['content'] = new TextareaInput($app, array(
      'label'        => _t('Feedback.CONTENT'),
      'validator'    => new RequiredValidator(),
    ));

    return parent::__construct($app, $object, array(
      'fields' => $fields
    ));
  }

  public function save()
  {
    /** @var \AnthemCM\Feedback\Model\Feedback $object */
    $object = parent::save();

    // Fill in fields from user, if any
    if (!$this->app['auth']->isGuest())
    {
      $object->setUser($this->app['auth']->getUser());
      $object->setEmail($this->app['auth']->getUser()->getEmail());
    }

    return $object;
  }
}
