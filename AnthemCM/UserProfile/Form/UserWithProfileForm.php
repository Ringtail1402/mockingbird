<?php

namespace AnthemCM\UserProfile\Form;

use Silex\Application;
use Anthem\Auth\Form\UserForm;
use Anthem\Auth\Model\User;
use AnthemCM\UserProfile\Model\UserProfile;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Validator\RequiredValidator;

/**
 * Modified version for admin user editing form with user profile.
 */
class UserWithProfileForm extends UserForm
{
  /**
   * The constructor.
   *
   * @param \Silex\Application      $app
   * @param \Anthem\Auth\Model\User $user
   */
  public function __construct(Application $app, User $user)
  {
    $profile = $this->getProfile($user);

    $extra_fields = array(
      'firstname' => new StringInput($app, array(
        'label' => _t('UserProfile.FIRSTNAME'),
        'value' => $profile->getFirstname(),
        'is_virtual' => true,
      )),
      'lastname' => new StringInput($app, array(
        'label' => _t('UserProfile.LASTNAME'),
        'value' => $profile->getLastname(),
        'is_virtual' => true,
      )),
      'nickname' => new StringInput($app, array(
        'label' => _t('UserProfile.NICKNAME'),
        'value' => $profile->getNickname(),
        'is_virtual' => true,
      )),
    );

    foreach ($app['UserProfile']['require_fields'] as $field)
      $extra_fields[$field]->addValidator(new RequiredValidator());

    foreach ($app['UserProfile']['exclude_fields'] as $field)
      unset($extra_fields[$field]);

    parent::__construct($app, $user, $extra_fields);
  }

  /**
   * Saves user and his/her profile from form.
   *
   * @return \Anthem\Auth\Model\User
   */
  public function save()
  {
    $user = parent::save();

    $profile = $this->getProfile($user);

    if (!in_array('firstname', $this->app['UserProfile']['exclude_fields']))
      $profile->setFirstname($this->options['fields']['firstname']->getValue());
    if (!in_array('lastname',  $this->app['UserProfile']['exclude_fields']))
      $profile->setLastname ($this->options['fields']['lastname']->getValue());
    if (!in_array('nickname',  $this->app['UserProfile']['exclude_fields']))
      $profile->setNickname ($this->options['fields']['nickname']->getValue());

    return $user;
  }

  /**
   * Retrieves or creates a profile for user.
   *
   * @param \Anthem\Auth\Model\User $user
   * @return \AnthemCM\UserProfile\Model\UserProfile
   */
  protected function getProfile(User $user)
  {
    if ($user->getUserProfile()) return $user->getUserProfile();

    $profile = new UserProfile();
    $profile->setUser($user);
    return $profile;
  }
}