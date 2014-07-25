<?php

namespace AnthemCM\Feedback\Admin;

use Anthem\Admin\Admin\TableAdminPage;

/**
 * Feedback object admin page.
 */
class FeedbackAdmin extends TableAdminPage
{
  public function getTitle()
  {
    return _t('Feedback.ADMIN_TITLE');
  }

  public function getSubtitle()
  {
    return _t('Feedback.ADMIN_SUBTITLE');
  }

  protected function getOptions()
  {
    $app = $this->app;

    return array(
      'form'          => 'AnthemCM\\Feedback\\Form\\FeedbackForm',
      'table_columns' => array(
        'created_at' => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\DateTimeColumn',
                              'title' => _t('Feedback.CREATED_AT'), 'width' => '10%', 'sort' => true, 'filter' => true, 'link_form' => true),
        'user'       => array('class' => 'Anthem\\Auth\\Admin\\TableColumn\\UserColumn',
                              'title' => _t('Feedback.USER'), 'width' => '25%', 'sort' => true, 'filter' => true),
        'email'      => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\StringColumn',
                              'title' => _t('Feedback.EMAIL'),  'width' => '25%', 'sort' => true, 'filter' => true),
      ),
      'can_create' => false,
      'can_edit' => false,
      'can_edit_ro' => true,
      'can_save' => false,
      'can_save_create' => false,
      'default_sort'  => array('created_at', 'desc'),
      'extra_css' => array(
        'Anthem/Forms:lib/bootstrap-datepicker.css',
      ),
      'extra_js'      => array(
        'Anthem/Forms:lib/bootstrap-datepicker.js',
        'Anthem/Admin:columns/datetime.js'
      ),
    );
  }
}
