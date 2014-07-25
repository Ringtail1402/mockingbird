<?php

namespace AnthemCM\UserProfile\Form;

use Silex\Application;
use Anthem\Auth\Form\RegisterForm;
use Anthem\Auth\Model\User;
use AnthemCM\UserProfile\Model\UserProfile;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Validator\RequiredValidator;

/**
 * Modified version for user registration form with user profile.  Similar to UserWithProfileForm.
 */
class RegisterWithProfileForm extends RegisterForm
{
  /**
   * The constructor.
   *
   * @param \Silex\Application      $app
   */
  public function __construct(Application $app)
  {
    $extra_fields = array(
      'firstname' => new StringInput($app, array(
        'label' => _t('UserProfile.FIRSTNAME'),
        'is_virtual' => true,
      )),
      'lastname' => new StringInput($app, array(
        'label' => _t('UserProfile.LASTNAME'),
        'is_virtual' => true,
      )),
      'nickname' => new StringInput($app, array(
        'label' => _t('UserProfile.NICKNAME'),
        'is_virtual' => true,
      )),
    );

    foreach ($app['UserProfile']['require_fields'] as $field)
      $extra_fields[$field]->addValidator(new RequiredValidator());

    foreach ($app['UserProfile']['exclude_fields'] as $field)
      unset($extra_fields[$field]);

    parent::__construct($app, $extra_fields);
  }

  /**
   * Saves user and his/her profile from form.
   *
   * @return \Anthem\Auth\Model\User
   */
  public function save()
  {
    $user = parent::save();

    $profile = new UserProfile();
    $profile->setUser($user);

    if (!in_array('firstname', $this->app['UserProfile']['exclude_fields']))
      $profile->setFirstname($this->options['fields']['firstname']->getValue());
    if (!in_array('lastname',  $this->app['UserProfile']['exclude_fields']))
      $profile->setLastname ($this->options['fields']['lastname']->getValue());
    if (!in_array('nickname',  $this->app['UserProfile']['exclude_fields']))
      $profile->setNickname ($this->options['fields']['nickname']->getValue());

    return $user;
  }
}