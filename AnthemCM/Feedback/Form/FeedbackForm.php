<?php

namespace AnthemCM\Feedback\Form;

use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\TextareaInput;
use Anthem\Auth\Input\UserInput;
use AnthemCM\Feedback\Model\Feedback;

/**
 * Feedback admin form.  This is always read-only.
 */
class FeedbackForm extends Form
{
  public function __construct($app, $object)
  {
    parent::__construct($app, $object, array('fields' => array(
      'user_id' => new UserInput($app, array(
        'label'     => _t('Feedback.USER'),
      )),
      'email' => new StringInput($app, array(
        'label'     => _t('Feedback.EMAIL'),
      )),
      'content' => new TextareaInput($app, array(
        'label'     => _t('Feedback.CONTENT'),
      ))
    )));

    $this->setReadOnly(true);
  }
}
