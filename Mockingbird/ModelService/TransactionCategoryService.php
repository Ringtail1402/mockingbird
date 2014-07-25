<?php

namespace Mockingbird\ModelService;

use Silex\Application;
use Anthem\Propel\ModelService\PropelModelService;
use Mockingbird\Model\TransactionCategory;

/**
 * Model service for TransactionCategory model.
 */
class TransactionCategoryService extends PropelModelService
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
   * Returns all categories.
   *
   * @return TransactionCategory[]
   */
  public function getAll($user = null)
  {
    return $this->createQuery()
                ->filterByUser($user ? $user : $this->app['auth']->getUser())
                ->orderByTitle()
                ->find();
  }


  /**
   * Searches by category title.
   *
   * @param string $q
   * @return TransactionCategory
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
   * Counts categories.
   *
   * @return integer
   */
  public function countCategories()
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
    return 'Mockingbird\\Model\\TransactionCategory';
  }
}
