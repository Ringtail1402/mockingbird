<?php

namespace Anthem\Auth\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * User lock reason column.
 */
class LockedColumn extends StringColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'locked';
  }

  /**
   * Returns default templates to use.
   *
   * @param  none
   * @return array
   */
  protected function getDefaultTemplates()
  {
    $templates = parent::getDefaultTemplates();
    $templates['value'] = 'Anthem/Auth:columns/locked.php';
    return $templates;
  }
}