<?php

namespace Mockingbird\ModelService;

use Silex\Application;
use Anthem\Propel\ModelService\PropelModelService;

/**
 * Model service for CounterParty model.
 */
class CounterPartyService extends PropelModelService
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
   * Looks up counter-party by exact title.
   *
   * @param string $q
   * @return CounterParty
   */
  public function findOneByTitle($q)
  {
    return $this->createQuery()
                ->filterByUser($this->app['auth']->getUser())
                ->findOneByTitle($q);
  }

  /**
   * Searches by counter-party title.
   *
   * @param string $q
   * @return CounterParty
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
   * Counts counter-parties.
   *
   * @return integer
   */
  public function countCounterParties()
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
    return 'Mockingbird\\Model\\CounterParty';
  }
}
