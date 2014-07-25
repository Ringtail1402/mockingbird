<?php

namespace Anthem\Auth\Admin\TableColumn;

use Silex\Application;
use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * Group column for use in admin pages.
 */
class GroupColumn extends StringColumn
{
  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'group';
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
    $templates['value'] = 'Anthem/Auth:columns/group.php';
    return $templates;
  }

  /**
   * Adds sorting criteria.
   * XXX Assumes that useUserQuery() is used for user model.
   *
   * @param mixed  $query
   * @param string $dir
   * @return mixed
   */
  public function addSortCriteria($query, $dir)
  {
    $query->useGroupQuery()
            ->orderByTitle($dir == 'asc' ? \Criteria::ASC : \Criteria::DESC)
          ->endUse();
    return $query;
  }

  /**
   * Adds filtering criteria for this field to the query.
   * XXX Assumes that useGroupQuery() is used for group model.
   *
   * @param  mixed $query
   * @param  mixed $filter
   * @return mixed
   */
  public function addFilter($query, &$filter)
  {
    if ($filter)
    {
      $query->useGroupQuery()
              ->filterByTitle('%' . $filter . '%', \Criteria::LIKE)
            ->endUse();
    }
    return $query;
  }
}