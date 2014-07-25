<?php

namespace Anthem\Forms\Input;

/**
 * This interface should be used for custom fields which do not have a direct
 * mapping to object properties.  These fields will handle saving/loading their
 * values from object themselves.
 */
interface VirtualInputInterface
{
  /**
   * Loads a value from object.
   *
   * @param  $object
   * @return void
   */
  function load($object);

  /**
   * Saves a value into object.
   *
   * @param  $object
   * @return void
   */
  function save($object);
}