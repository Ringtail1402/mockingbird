<?php

namespace Anthem\Auth\Admin\TableColumn;

use Silex\Application;
use Criteria;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\StringColumn;

/**
 * User column for use in admin pages.
 */
class UserColumn extends StringColumn
{
  public function __construct(Application $app, $field, array $options = array())
  {
    $options['auto_wbr'] = true;
    parent::__construct($app, $field, $options);
  }

  /**
   * Returns column type name.
   *
   * @return string
   */
  public function getTypeName()
  {
    return 'user';
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
    $templates['value'] = 'Anthem/Auth:columns/user.php';
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
    $query->useUserQuery()
            ->orderByEmail($dir == 'asc' ? \Criteria::ASC : \Criteria::DESC)
          ->endUse();
    return $query;
  }

  /**
   * Adds filtering criteria for this field to the query.
   * XXX Assumes that useUserQuery() is used for user model.
   *
   * @param  mixed $query
   * @param  mixed $filter
   * @return mixed
   */
  public function addFilter($query, &$filter)
  {
    if ($filter)
    {
      $query->useUserQuery()
              ->filterByEmail('%' . $filter . '%', \Criteria::LIKE)
            ->endUse();
    }
    return $query;
  }
}