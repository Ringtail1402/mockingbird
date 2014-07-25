<?php

namespace Anthem\Auth\Admin;

use Anthem\Admin\Admin\TableAdminPage;

/**
 * Group admin page.
 */
class GroupAdmin extends TableAdminPage
{
  public function getTitle()
  {
    return _t('Auth.GROUP_ADMIN_TITLE');
  }

  public function getSubtitle()
  {
    return _t('Auth.GROUP_ADMIN_SUBTITLE');
  }

  protected function getOptions()
  {
    $app = $this->app;
    $self = $this;

    return array(
      'form'          => 'Anthem\\Auth\\Form\\GroupForm',
      'table_columns' => array(
        'title'         => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\StringColumn',
                                 'title' => _t('Auth.TITLE'), 'width' => 'auto', 'sort' => true, 'filter' => true, 'link_form' => true),
        'num_users'     => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\NumberColumn',
                                 'title' => _t('Auth.NUM_USERS'), 'width' => '130px', 'sort' => true, 'is_virtual' => true),
      ),
      'action_column_width' => '350px',
      'default_sort'  => array('title', 'asc'),
      'extra_links' => array(
        'view_users' => array(
          'title'      => '<i class="icon-arrow-right"></i> ' . _t('Auth.TO_USERS'),
          'url'        => function($object) use ($app, $self) {
            return $app['url_generator']->generate('admin.page', array('page' => 'auth.admin.users')) .
                   '#filter.groups=' . str_replace('+', '%20', urlencode($object->getTitle()));
          },
        ),
      ),
      'extra_css'     => array(
        'Anthem/Auth:admin.css',
      ),
    );
  }

  protected function getQuery()
  {
    return parent::getQuery()
                 ->leftJoinRefGroup()
                 ->withColumn('COUNT(ref_users_groups.USER_ID)', 'num_users')
                 ->groupById();
  }
}
