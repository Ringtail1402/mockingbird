<?php

namespace Anthem\Admin\Admin;

use Anthem\Admin\Admin\TableAdminPage;

/**
 * An extension of TableAdminPage which allows presenting records in arbitrary form, rather than as table.
 */
abstract class ListAdminPage extends TableAdminPage
{
  /**
   * Returns default templates.
   *
   * @param  string $template
   * @return string
   */
  public function getTemplate($template)
  {
    $templates = array(
      'frame'        => 'Anthem/Admin:list/frame.php',
      'filters'      => 'Anthem/Admin:list/filters.php',
      'table'        => $this->options['list_template'],        // List templates are always custom.
      'table_empty'  => isset($this->options['empty_list_template']) ? $this->options['empty_list_template'] : 'Anthem/Admin:list/list_empty.php',
      'frame_print'  => 'Anthem/Admin:list/frame_print.php',
    );
    if (isset($templates[$template])) return $templates[$template];

    return parent::getTemplate($template);
  }

  /**
   * Sets default options.  Checks that ListAdminPage-specific options are set.
   *
   * @return void
   * @throws \InvalidArgumentException
   */
  protected function setDefaultOptions()
  {
    if (empty($this->options['list_template'])) throw new \InvalidArgumentException('Option list_template must be set.');

    parent::setDefaultOptions();
  }
}