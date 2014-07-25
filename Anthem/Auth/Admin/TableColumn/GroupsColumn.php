<?php

namespace Anthem\Auth\Admin\TableColumn;

use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * User groups column.
 */
class GroupsColumn extends StringColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'groups';
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
    $templates['value'] = 'Anthem/Auth:columns/groups.php';
    return $templates;
  }

  /**
   * Adds filtering criteria for this field to the query.
   *
   * @param  mixed $query
   * @param  mixed $filter
   * @return mixed
   */
  public function addFilter($query, &$filter)
  {
    if ($filter)
    {
      $query->useRefGroupQuery()
              ->useGroupQuery()
                ->filterByTitle($filter)
              ->endUse()
            ->endUse();
    }
    return $query;
  }
}