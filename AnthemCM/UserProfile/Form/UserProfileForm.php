<?php

namespace AnthemCM\UserProfile\Form;

use Silex\Application;
use Anthem\Forms\Form\Form;
use AnthemCM\UserProfile\Model\UserProfile;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Validator\RequiredValidator;

/**
 * User profile edit form.
 */
class UserProfileForm extends Form
{
  /**
   * The constructor.
   *
   * @param \Silex\Application                      $app
   * @param \AnthemCM\UserProfile\Model\UserProfile $profile
   */
  public function __construct(Application $app, UserProfile $profile)
  {
    $options = array(
      'fields' => array(
        'firstname' => new StringInput($app, array(
          'label' => _t('UserProfile.FIRSTNAME'),
        )),
        'lastname' => new StringInput($app, array(
          'label' => _t('UserProfile.LASTNAME'),
        )),
        'nickname' => new StringInput($app, array(
          'label' => _t('UserProfile.NICKNAME'),
        )),
      ),
    );

    foreach ($app['UserProfile']['require_fields'] as $field)
      $options['fields'][$field]->addValidator(new RequiredValidator());

    foreach ($app['UserProfile']['exclude_fields'] as $field)
      unset($options['fields'][$field]);

    parent::__construct($app, $profile, $options);
  }
}