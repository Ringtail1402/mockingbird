<?php

namespace Anthem\Auth\Form;

use Silex\Application;
use Anthem\Forms\Form\Form;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserQuery;
use Anthem\Auth\Model\GroupQuery;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\PasswordInput;
use Anthem\Forms\Input\CheckboxInput;
use Anthem\Forms\Input\SelectInput;
use Anthem\Forms\Input\PropelManyToManyInput;
use Anthem\Auth\Input\PoliciesInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\UniqueValidator;
use Anthem\Auth\Validator\PasswordEqualityValidator;

/**
 * User editing form.
 */
class UserForm extends Form
{
  /**
   * @param \Silex\Application       $app
   * @param \Anthem\Auth\Model\User  $user
   * @param array                    $extra_fields
   */
  public function __construct(Application $app, $user, array $extra_fields = array())
  {
    $user->password1 = '';  // Emulate a non-existent field
    $user->password2 = '';

    $locked_values = array('' => '');
    foreach ($app['Auth']['lock_reasons'] as $reason)
      $locked_values[$reason] = _t('LOCK_REASON.BRIEF.' . $reason);

    $options = array(
      'label'  => function () use ($user) { return ($user->getEmail() ? htmlspecialchars($user->getEmail()) : _t('Auth.USER_NEW')); },
      'fields' => array(
        'email'    => new StringInput($app, array(
          'label'     => _t('Auth.EMAIL'),
          'help'      => _t('Auth.EMAIL_HELP'),
          'validator' => array(
            new RequiredValidator(),
            new UniqueValidator(array(
              'query' => function ($email) use ($user) {
                return UserQuery::create()
                                ->_if($user->getId())
                                  ->filterById($user->getId(), \Criteria::NOT_EQUAL)
                                ->_endif()
                                ->filterByEmail($email);
              },
              'message' => _t('Auth.UNIQUE_EMAIL_VALIDATOR_MESSAGE'),
            ))
          ),
        )),
        'password1' => new PasswordInput($app, array(
          'label'     => _t('Auth.PASSWORD'),
          'help'      => _t('Auth.PASSWORD_HELP'),
        )),
        'password2' => new PasswordInput($app, array(
          'label'     => _t('Auth.PASSWORD2'),
        )),
        'groups' => new PropelManyToManyInput($app, array(
          'label'     => _t('Auth.GROUPS'),
          'help'      => _t('Auth.GROUPS_HELP'),
          'ref_model' => 'Anthem\\Auth\\Model\\RefUserGroup',
          'query_target_objects' => function () { return GroupQuery::create()->orderByTitle()->find(); },
          'query_ref_objects' => function ($user) { return $user->getRefGroups(); },
          'set_ref_master_method' => 'setUser',
          'get_ref_target_id_method' => 'getGroupId',
          'set_ref_target_id_method' => 'setGroupId',
        )),
        'is_superuser' => new CheckboxInput($app, array(
          'label'     => _t('Auth.IS_SUPERUSER'),
          'help'      => _t('Auth.IS_SUPERUSER_HELP'),
        )),
        'locked' => new SelectInput($app, array(
          'label'     => _t('Auth.LOCKED'),
          'help'      => _t('Auth.LOCKED_HELP'),
          'values'    => $locked_values,
        )),
        'policies' => new PoliciesInput($app, array(
          'label'     => _t('Auth.POLICIES'),
          'mode'      => 'user',
        )),
      ),
      'validator' => new PasswordEqualityValidator(array('password1_field' => 'password1')),
    );
    $options['fields'] = array_merge($options['fields'], $extra_fields);

    // Require password for user creation
    if ($user->isNew())
    {
      $options['fields']['password1']->addValidator(new RequiredValidator());
      $options['fields']['password2']->addValidator(new RequiredValidator());
    }

    // Only allow superusers to change superuser flag
    if (!$app['auth']->getUser()->getIsSuperuser())
      $options['fields']['is_superuser']->setReadOnly(true);

    parent::__construct($app, $user, $options);

    // Only allow superusers to change superusers
    if (!$app['auth']->getUser()->getIsSuperuser() && $user->getIsSuperuser())
      $this->setReadOnly(true);
  }

  public function save()
  {
    $user = parent::save();
    if ($user->password1)
      $this->app['auth']->changePassword($user, $user->password1);
    return $user;
  }
}
