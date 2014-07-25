<?php

namespace AnthemCM\Pages\Admin;

use Anthem\Admin\Admin\TableAdminPage;

/**
 * Page object admin page.
 */
class PageAdmin extends TableAdminPage
{
  public function getTitle()
  {
    return _t('Pages.ADMIN_TITLE');
  }

  public function getSubtitle()
  {
    return _t('Pages.ADMIN_SUBTITLE');
  }

  protected function getOptions()
  {
    $app = $this->app;

    return array(
      'form'          => 'AnthemCM\\Pages\\Form\\PageForm',
      'url'           => function($object) use ($app) {
        return $object->getIsActive() ? $app['request']->getBaseUrl() . '/' . $object->getUrl() : '';
      },
      'preview'       => function($object) use ($app) {
        return $app['pages.controller']->pageAction($object);
      },
      'table_columns' => array(
        'title'      => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\StringColumn',
                              'title' => _t('Pages.TITLE'),  'width' => '25%', 'sort' => true, 'filter' => true, 'link_form' => true),
        'url'        => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\StringColumn',
                              'title' => _t('Pages.URL'),    'width' => '25%', 'sort' => true, 'filter' => true),
        'is_active'  => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\BooleanColumn',
                              'title' => _t('Pages.ACTIVE'), 'width' => '9%', 'sort' => true, 'filter' => true),
        'updated_at' => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\DateTimeColumn',
                              'title' => _t('Pages.UPDATED_AT'), 'width' => '10%', 'sort' => true, 'filter' => true)
      ),
      'default_sort'  => array('url', 'asc'),
      'extra_css' => array(
        'Anthem/Forms:lib/bootstrap-datepicker.css',
      ),
      'extra_js'      => array(
        'Anthem/Forms:lib/bootstrap-datepicker.js',
        'Anthem/Forms:lib/tinymce/tiny_mce.js',
        'Anthem/Admin:columns/datetime.js'
      ),
    );
  }
}
