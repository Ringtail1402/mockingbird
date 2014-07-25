<?php

namespace Anthem\Propel\ModelService;

use \Propel;
use \BaseObject;
use \ModelCriteria;
use Anthem\Core\ModelService\ModelServiceInterface;

/**
 * This is a base model service class for Propel models.
 */
abstract class PropelModelService implements ModelServiceInterface
{
  /**
   * Returns full name of the class managed by this service.
   *
   * @abstract
   * @param  none
   * @return string
   */
  abstract public function getModelClass();

  /**
   * Creates an empty, unsaved object.
   *
   * @abstract
   * @param  none
   * @return \BaseObject
   */
  public function create()
  {
    $class = $this->getModelClass();
    return new $class();
  }

  /**
   * Returns unique scalar primary key of the object.
   *
   * @param  \BaseObject $object
   * @return mixed
   */
  public function id($object)
  {
    return $object->getPrimaryKey();
  }

  /**
   * Instances a query object for the model class.
   *
   * @return ModelCriteria
   */
  public function createQuery()
  {
    $query_class = $this->getModelClass() . 'Query';
    return call_user_func(array($query_class, 'create'));
  }

 /**
  * Finds an object by ID.
  *
  * @param  integer $id
  * @return BaseObject
  */
  public function find($id)
  {
    return $this->createQuery()
                ->findPk($id);
  }

  /**
   * Sets up pagination parameters for query.
   *
   * @param \ModelCriteria $query
   * @param integer $limit
   * @param integer $offset
   * @return void
   */
  public function paginate($query, $limit, $offset)
  {
    $query->offset($offset)
          ->limit($limit);
  }

  /**
   * Performs a query, returning an array of objects.
   *
   * @param  \ModelCriteria $query
   * @return \BaseObject[]
   */
  public function query($query)
  {
    return $query->find();
  }

  /**
   * Performs a query, returning a single object.
   *
   * @param  \ModelCriteria $query
   * @return \BaseObject
   */
  public function querySingle($query)
  {
    return $query->findOne();
  }

  /**
   * Performs a query, deleting all matching objects.
   *
   * @param  \ModelCriteria $query
   * @return void
   */
  public function queryDelete($query)
  {
    // Propel doesn't let us do a delete() with no criteria set
    if (count($query->getMap()))
      $query->delete();
    else
      $query->deleteAll();
  }

  /**
   * Performs a query, returning number of records.
   *
   * @abstract
   * @param  \ModelCriteria $query
   * @return integer
   */
  public function count($query)
  {
    return $query->count();
  }

 /**
  * Saves an object.
  *
  * @param  \BaseObject $object
  * @return void
  */
  public function save($object)
  {
    if (!is_a($object, $this->getModelClass()))
      throw new \LogicException('Object must be an instance of ' . $this->getModelClass() . '.');
    $object->save();
  }

 /**
  * Deletes an object.
  *
  * @param  \BaseObject $object
  * @return void
  */
  public function delete($object)
  {
    if (!is_a($object, $this->getModelClass()))
      throw new \LogicException('Object must be an instance of ' . $this->getModelClass() . '.');
    $object->delete();
  }

 /**
  * Deletes all objects.  Be careful.
  *
  * @return void
  */
  public function truncate()
  {
    call_user_func(array($this->getModelClass() . 'Peer', 'doDeleteAll'));
  }

 /**
  * Begins a transaction.
  *
  * @return void
  */
  public function begin()
  {
    Propel::getConnection()->beginTransaction();
  }

 /**
  * Commits a transaction.
  *
  * @return void
  */
  public function commit()
  {
    Propel::getConnection()->commit();
  }

 /**
  * Rolls back a transaction.
  *
  * @return void
  */
  public function rollback()
  {
    Propel::getConnection()->rollBack();
  }

  /**
   * Clears possible caches.
   *
   * @return void
   */
  public function flush()
  {
    call_user_func(array($this->getModelClass() . 'Peer', 'clearInstancePool'));
  }
}

