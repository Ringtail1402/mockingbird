<?php

namespace Mockingbird\ModelService;

use Silex\Application;
use Anthem\Propel\ModelService\PropelModelService;

/**
 * Model service for TransactionTag model.
 */
class TransactionTagService extends PropelModelService
{
  /**
   * @var Application
   */
  protected $app;

  public function __construct(Application $app)
  {
    $this->app = $app;
  }

  /**
   * Looks up tag by exact title.
   *
   * @param string $q
   * @return TransactionTag
   */
  public function findOneByTitle($q)
  {
    return $this->createQuery()
        ->filterByUser($this->app['auth']->getUser())
        ->findOneByTitle($q);
  }

  /**
   * Searches by tag title.
   *
   * @param string $q
   * @return TransactionTag
   */
  public function search($q)
  {
    return $this->createQuery()
                ->filterByUser($this->app['auth']->getUser())
                ->filterByTitle('%' . $q . '%')
                ->orderByTitle()
                ->find();
  }

  /**
   * Counts tags.
   *
   * @return integer
   */
  public function countTags()
  {
    return $this->createQuery()
                ->_if(!$this->app['auth']->hasPolicies('mockingbird.alldata.ro'))
                  ->filterByUser($this->app['auth']->getUser())
                ->_endif()
                ->count();
  }

  /**
   * Returns underlying model class.
   *
   * @return string
   */
  public function getModelClass()
  {
    return 'Mockingbird\\Model\\TransactionTag';
  }
}
