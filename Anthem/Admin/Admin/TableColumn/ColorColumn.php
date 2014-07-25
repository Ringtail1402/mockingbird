<?php

namespace Anthem\Admin\Admin\TableColumn;

use Anthem\Admin\Admin\TableColumn\PropelColumn;

/**
 * A color column.  Just shows color, cannot be edited or sorted.
 */
class ColorColumn extends PropelColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'color';
  }

  /**
   * Returns default templates to use.
   *
   * @param  none
   * @return array
   */
  protected function getDefaultTemplates()
  {
    return array(
      'value'   => 'Anthem/Admin:table/columns/color.php',
      'filter'  => false,
    );
  }
}