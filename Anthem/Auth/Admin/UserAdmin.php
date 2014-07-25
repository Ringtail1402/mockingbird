<?php

namespace Anthem\Auth\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Anthem\Admin\Admin\TableAdminPage;
use Anthem\Auth\Model\UserQuery;

/**
 * User admin page.
 */
class UserAdmin extends TableAdminPage
{
  public function getTitle()
  {
    return _t('Auth.USER_ADMIN_TITLE');
  }

  public function getSubtitle()
  {
    return _t('Auth.USER_ADMIN_SUBTITLE');
  }

  protected function getOptions()
  {
    $app = $this->app;
    $self = $this;

    return array(
      'form'          => $this->app['Auth']['user_form_class'],
      'table_columns' => array(
        'email'         => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\StringColumn',
                                 'title' => _t('Auth.EMAIL'), 'width' => 'auto', 'sort' => true, 'filter' => true, 'link_form' => true,
                                 'options' => array('auto_wbr' => true)),
        'groups'        => array('class' => 'Anthem\\Auth\\Admin\\TableColumn\\GroupsColumn',
                                 'title' => _t('Auth.GROUPS'), 'width' => '200px', 'filter' => true, 'is_virtual' => true),
        'created_at'    => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\DateTimeColumn',
                                 'title' => _t('Auth.CREATED_AT'), 'width' => '150px', 'sort' => true, 'filter' => true),
        'last_login'    => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\DateTimeColumn',
                                 'title' => _t('Auth.LAST_LOGIN'), 'width' => '150px', 'sort' => true, 'filter' => true),
        'is_superuser'  => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\BooleanColumn',
                                 'title' => _t('Auth.IS_SUPERUSER'), 'width' => '80px', 'sort' => true, 'filter' => true,
                                 'options' => array('false_empty' => true)),
        'locked'        => array('class' => 'Anthem\\Auth\\Admin\\TableColumn\\LockedColumn',
                                 'title' => _t('Auth.LOCKED'), 'width' => '100px', 'sort' => true, 'filter' => true),
      ),
      'action_column_width' => '350px',
      'default_sort'  => array('created_at', 'asc'),
      'can_purge' => false,
      'extra_links' => array(
        'login' => array(
          'title'      => '<i class="icon-eye-open"></i> ' . _t('Auth.LOGIN_AS_USER'),
          'js'         => function($object) { return 'return confirm("' . _t('Auth.LOGIN_AS_USER_CONFIRM') . '");'; },
          'url'        => function($object) use ($app) {
            return $app['url_generator']->generate('auth.login.force') . '?id=' . $object->getId();
          },
          'test'       => function($object) use ($app) {
            return !$object->getIsSuperuser() &&
                   $object->getId() != $app['auth']->getUser()->getId() &&
                   $app['auth']->hasPolicies('auth.admin.force_login');
          }
        ),
      ),
      'extra_css'     => array(
        'Anthem/Forms:lib/bootstrap-datepicker.css',
        'Anthem/Auth:admin.css',
      ),
      'extra_js'      => array(
        'Anthem/Forms:lib/bootstrap-datepicker.js',
        'Anthem/Auth:admin/users.js'
      )
    );
  }

  // Access permissions on actions

  public function testEdit($object)
  {
    // Only superusers can edit superusers
    if (!$this->app['auth']->getUser()->getIsSuperuser())
      return !$object->getIsSuperuser();

    return parent::testEdit($object);
  }

  public function delete($object)
  {
    // Only superusers can delete superusers
    if (!$this->app['auth']->getUser()->getIsSuperuser() && $object->getIsSuperuser())
      throw new \LogicException('Only superusers can delete superusers.');

    return parent::delete($object);
  }

  public function testDelete($object)
  {
    // Only superusers can delete superusers
    if (!$this->app['auth']->getUser()->getIsSuperuser())
      return !$object->getIsSuperuser();
    // Forbid deleting ourselves
    if ($object->getId() == $this->app['auth']->getUser()->getId())
      return false;

    return parent::testDelete($object);
  }

  public function deleteMass(array $ids)
  {
    // Only superusers can delete superusers
    if (!$this->app['auth']->getUser()->getIsSuperuser())
    {
      return UserQuery::create()
                      ->filterByPrimaryKeys($ids)
                      ->filterByIsSuperuser(false)
                      ->delete();
    }

    return parent::deleteMass($ids);
  }

  public function testDeleteMass(array $ids)
  {
    // Forbid deleting ourselves
    $own_id = array_search($this->app['auth']->getUser()->getId(), $ids);
    if ($own_id) unset($ids[$own_id]);

    // Only superusers can delete superusers
    if (!$this->app['auth']->getUser()->getIsSuperuser())
    {
      return UserQuery::create()
                      ->filterByPrimaryKeys($ids)
                      ->filterByIsSuperuser(false)
                      ->count();
    }

    return parent::testDeleteMass($ids);
  }

  /**
   * AJAX action for search by email.
   *
   * @param Request $request
   * @return string JSON.
   */
  public function searchEmailAjax(Request $request)
  {
    $users = $this->app['auth.model.user']->searchUsers($request->get('q'));
    $result = array();
    foreach ($users as $_user)
      $result[] = $_user->getEmail();
    return new Response(json_encode($result), 200, array('Content-Type' => 'application/json'));
  }

  /**
   * AJAX action for search by groups.
   *
   * @param Request $request
   * @return string JSON.
   */
  public function searchGroupsAjax(Request $request)
  {
    $groups = $this->app['auth.model.group']->searchGroups($request->get('q'));
    $result = array();
    foreach ($groups as $_group)
      $result[] = $_group->getTitle();
    return new Response(json_encode($result), 200, array('Content-Type' => 'application/json'));
  }

  /**
   * AJAX action to show lock user dialog/actually lock user.
   *
   * @param Request $request
   * @return string
   * @throws NotFoundHttpException
   */
  public function lockDialogAjax(Request $request)
  {
    $this->app['auth']->checkPolicies('auth.admin.rw');

    if ($request->getMethod() == 'POST')
    {
      $user = $this->app['auth']->findUser($request->get('id'));
      if (!$user) throw new NotFoundHttpException('Unknown user id.');
      $user->setLocked($request->get('lock-reason'));
      $user->save();
      return 'OK';
    }
    else
      return $this->app['core.view']->render('Anthem/Auth:dialog/lock_user.php', array('id' => $request->get('id')));
  }

  /**
   * AJAX action to unlock user.
   *
   * @param Request $request
   * @return string
   * @throws NotFoundHttpException
   */
  public function unlockUserAjax(Request $request)
  {
    $this->app['auth']->checkPolicies('auth.admin.rw');
    $user = $this->app['auth']->findUser($request->get('id'));
    if (!$user) throw new NotFoundHttpException('Unknown user id.');
    $user->setLocked(null);
    $user->save();
    return 'OK';
  }
}
