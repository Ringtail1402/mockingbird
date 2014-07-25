<?php

namespace Anthem\Auth\Input;

use Silex\Application;
use Anthem\Forms\Input\VirtualInputInterface;
use Anthem\Forms\Input\BaseInput;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\Group;
use Anthem\Auth\Model\UserPolicy;
use Anthem\Auth\Model\GroupPolicy;

/**
 * An input which allows associating a user or a group to policies.
 * Options are:
 * - mode (string, required): user or group
 */
class PoliciesInput extends BaseInput
                    implements VirtualInputInterface
{
  /**
   * The constructor.  Checks that options are set, and initializes sub-forms.
   *
   * @param \Silex\Application $app
   * @param array $options
   * @throws \LogicException
   */
  public function __construct(Application $app, array $options = array())
  {
    if (empty($options['mode']))
      throw new \LogicException('mode option must be set.');

    $options['all_policies'] = $app['Auth']['policy_definitions'];

    parent::__construct($app, $options);
  }

  /**
   * Loads a value from object.
   *
   * @param  $object
   * @return void
   * @throws \LogicException
   */
  public function load($object)
  {
    $this->value = array();

    // Load user policies, including inherited
    if ($this->options['mode'] == 'user' && $object instanceof User)
    {
      $inherited_policies = $this->app['auth']->getPolicies($object, true);
      $own_policies       = $this->app['auth']->getPolicies($object, false);

      foreach ($this->options['all_policies'] as $policy_group)
      {
        foreach ($policy_group as $policy)
        {
          if (isset($own_policies[$policy]))
            $this->value[$policy] = $own_policies[$policy] ? 'own_enable' : 'own_disable';
          elseif (!empty($inherited_policies[$policy]))
            $this->value[$policy] = 'inherited_enable';
          else
            $this->value[$policy] = 'inherited_disable';
        }
      }

      // Store for use in template
      $this->options['inherited_policies'] = $inherited_policies;
    }

    // Load group policies
    elseif ($this->options['mode'] == 'group' && $object instanceof Group)
    {
      $group_policies = array();
      foreach ($object->getPolicys() as $policy)
        $group_policies[$policy->getPolicy()] = true;

      foreach ($this->options['all_policies'] as $policy_group)
      {
        foreach ($policy_group as $policy)
        {
          if (!empty($group_policies[$policy]))
            $this->value[$policy] = 'own_enable';
          else
            $this->value[$policy] = 'own_disable';
        }
      }
    }

    else
      throw new \LogicException('PoliciesInput mode unknown or doesn\'t match passed object.');
  }

  /**
   * Saves a value into object.
   *
   * @param  $object
   * @return void
   */
  public function save($object)
  {
    // Save user policies
    if ($this->options['mode'] == 'user' && $object instanceof User)
    {
      $user_policies = array();
      foreach ($object->getPolicys() as $policy)
        $user_policies[$policy->getPolicy()] = $policy;

      foreach ($this->value as $id => $enable)
      {
        if ($enable == 'own_enable' || $enable == 'own_disable')
        {
          // Policy remains set (whether enabled or disabled)
          if (isset($user_policies[$id]))
          {
            $user_policies[$id]->setEnable($enable == 'own_enable');
            unset($user_policies[$id]);
          }

          // Policy newly set (whether enabled or disabled)
          else
          {
            $policy = new UserPolicy();
            $policy->setUser($object);
            $policy->setPolicy($id);
            $policy->setEnable($enable == 'own_enable');
          }
        }
      }

      // Unset policies
      foreach ($user_policies as $policy) $policy->delete();
    }

    // Save group policies
    elseif ($this->options['mode'] == 'group' && $object instanceof Group)
    {
      $group_policies = array();
      foreach ($object->getPolicys() as $policy)
        $group_policies[$policy->getPolicy()] = $policy;

      foreach ($this->value as $id => $enable)
      {
        // Policy remains set
        if (isset($group_policies[$id]) && $enable == 'own_enable')
          unset($group_policies[$id]);

        // Policy newly set
        elseif ($enable == 'own_enable')
        {
          $policy = new GroupPolicy();
          $policy->setGroup($object);
          $policy->setPolicy($id);
        }
      }

      // Unset policies
      foreach ($group_policies as $policy) $policy->delete();
    }

    else
      throw new \LogicException('PoliciesInput mode unknown or doesn\'t match passed object.');
  }

  /**
   * Returns default template.
   *
   * @param  none
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Auth:input/policies.php';
  }
}
