<?php

namespace AnthemCM\Pages\ModelService;

use Anthem\Propel\ModelService\PropelModelService;

/**
 * Model service for Page model.
 */
class PageService extends PropelModelService
{
  public function getModelClass()
  {
    return 'AnthemCM\\Pages\\Model\\Page';
  }

  /**
   * Finds a page by its URL.
   *
   * @param  string  $url
   * @param  boolean $include_inactive
   * @return \AnthemCM\Pages\Model\Page
   */
  public function findOneByUrl($url, $include_inactive = false)
  {
    return $this->createQuery()
                ->_if(!$include_inactive)
                  ->filterByIsActive(true)
                ->_endif()
                ->findOneByUrl($url);
  }

  /**
   * Counts existing pages.
   *
   * @param  boolean $include_inactive
   * @return integer
   */
  public function countPages($include_inactive = false)
  {
    return $this->createQuery()
                ->_if(!$include_inactive)
                  ->filterByIsActive(true)
                ->_endif()
                ->count();
  }
}
