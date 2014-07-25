<?php

namespace Anthem\Auth\ModelService;

use Anthem\Propel\ModelService\PropelModelService;
use Anthem\Auth\Model\GroupQuery;

/**
 * Group model service.
 */
class GroupService extends PropelModelService
{
  /**
   * Searches by group title.
   *
   * @param string $q
   * @return \Anthem\Auth\Model\Group
   */
  public function searchGroups($q)
  {
    return GroupQuery::create()
                     ->filterByTitle('%' . $q . '%')
                     ->orderByTitle()
                     ->find();
  }

  /**
   * Counts available groups.
   *
   * @return integer
   */
  public function countGroups()
  {
    return GroupQuery::create()
                     ->count();
  }

  /**
   * Returns full name of the class managed by this service.
   *
   * @return string
   */
  public function getModelClass()
  {
    return 'Anthem\\Auth\\Model\\Group';
  }
}