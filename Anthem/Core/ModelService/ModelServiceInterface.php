<?php

namespace Anthem\Core\ModelService;

/**
 * Base interface for model services.
 * A model service represents a repository for a single type of objects, pretty much a database table.
 * All model access should be performed through a model service.
 * It makes the following assumption about underlying repository:
 * - the repository stores all objects of the same type;
 * - objects can be uniquely referenced by a scalar primary key;
 * - objects themselves can be of arbitrary class;
 * - there is an associated opaque query class, which may be used by service for storing query criteria.
 */
interface ModelServiceInterface
{
  /**
   * Creates an empty, unsaved object.
   *
   * @abstract
   * @param  none
   * @return object
   */
  public function create();

  /**
   * Returns unique scalar primary key of the object.
   *
   * @abstract
   * @param  object $object
   * @return mixed
   */
  public function id($object);

  /**
   * Instances a query object for the model class.
   *
   * @abstract
   * @param  none
   * @return mixed
   */
  public function createQuery();

  /**
   * Finds an object by ID.
   *
   * @abstract
   * @param  mixed $id
   * @return object
   */
  public function find($id);


  /**
   * Sets up pagination parameters for query.
   *
   * @param mixed $query
   * @param integer $limit
   * @param integer $offset
   * @return void
   */
  public function paginate($query, $limit, $offset);

  /**
   * Performs a query, returning an array of objects.
   *
   * @abstract
   * @param  mixed $query
   * @return object[]
   */
  public function query($query);

  /**
   * Performs a query, returning a single object.
   *
   * @abstract
   * @param  mixed $query
   * @return object
   */
  public function querySingle($query);

  /**
   * Performs a query, deleting all matching objects.
   *
   * @abstract
   * @param  mixed $query
   * @return void
   */
  public function queryDelete($query);

  /**
   * Performs a query, returning number of records.
   *
   * @abstract
   * @param  mixed $query
   * @return integer
   */
  public function count($query);

  /**
   * Saves an object.
   *
   * @abstract
   * @param  object $object
   * @return void
   */
  public function save($object);

  /**
   * Deletes an object.
   *
   * @abstract
   * @param  object $object
   * @return void
   */
  public function delete($object);

  /**
   * Deletes all objects.  Be careful.
   *
   * @abstract
   * @return void
   */
  public function truncate();

  /**
   * Begins a transaction.  Might not necessarily be supported.
   *
   * @abstract
   * @return void
   */
  public function begin();

  /**
   * Commits a transaction.  Might not necessarily be supported.
   *
   * @abstract
   * @return void
   */
  public function commit();

  /**
   * Rolls back a transaction.  Might not necessarily be supported.
   *
   * @abstract
   * @return void
   */
  public function rollback();

  /**
   * Clears possible caches.  Might not necessarily do anything.
   *
   * @abstract
   * @return void
   */
  public function flush();
}
