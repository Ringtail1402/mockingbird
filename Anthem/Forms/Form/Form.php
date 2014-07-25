<?php

namespace Anthem\Forms\Form;

use Silex\Application;
use Anthem\Forms\Input\GroupInput;
use Anthem\Forms\Input\VirtualInputInterface;

/**
 * Base form class.  A form is basically an input group which
 * is bound to properties of an object.
 */
abstract class Form extends GroupInput
{
  /**
   * @var object Underlying object.
   */
  protected $object;

 /**
  * The constructor.
  *
  * @param \Silex\Application $app
  * @param array|object       $object
  * @param array              $options
  */
  public function __construct(Application $app, $object, array $options = array())
  {
    parent::__construct($app, $options);

    if (!$object)
      throw new \LogicException('A form must be bound to a valid object.');
    $this->load($object);
  }

  /**
   * Loads object properties into form fields.
   *
   * @param  array|object $object
   * @return void
   */
  public function load($object)
  {
    $virtual_fields = array();

    // Iterate over known fields
    foreach ($this->options['fields'] as $name => $field)
    {
      // Ignore
      if ($field->getOption('is_virtual')) continue;

      // Try virtual fields
      if ($field instanceof VirtualInputInterface)
      {
        $virtual_fields[] = $field;
        continue;
      }

      // Try loading from array
      if (is_array($object))
      {
        if (isset($object[$name]))
          $field->setValue($object[$name]);
        continue;
      }

      // Try loading from property
      if (isset($object->$name))
      {
        $field->setValue($object->$name);
        continue;
      }

      // Try loading from getter, then.  PHP being case-insensitive helps a bit
      $getter_name = 'get' . str_replace('_', '', $name);
      $field->setValue(call_user_func(array($object, $getter_name)));
    }

    // Load virtual fields last
    foreach ($virtual_fields as $field) $field->load($object);

    $this->object = $object;
  }

  /**
   * Saves form fields into object properties.
   *
   * @return array|object
   */
  public function save()
  {
    $virtual_fields = array();

    // Iterate over known fields
    foreach ($this->options['fields'] as $name => $field)
    {
      // Ignore
      if ($field->getOption('is_virtual')) continue;

      // Skip readonly fields
      if ($field->getOption('readonly') && !$field->getOption('save_even_when_readonly')) continue;

      // Try virtual fields
      if ($field instanceof VirtualInputInterface)
      {
        $virtual_fields[] = $field;
        continue;
      }

      // Try saving to array
      if (is_array($this->object))
      {
        $this->object[$name] = $field->getValue();
        continue;
      }

      // Try saving to property
      if (isset($this->object->$name))
      {
        $this->object->$name = $field->getValue();
        continue;
      }

      // Try saving to setter, then.  PHP being case-insensitive helps a bit
      $setter_name = 'set' . str_replace('_', '', $name);
      call_user_func(array($this->object, $setter_name), $field->getValue());
    }

    // Save virtual fields last
    foreach ($virtual_fields as $field) $field->save($this->object);

    return $this->object;
  }

  /**
   * Returns underlying object
   *
   * @return object
   */
  public function getObject()
  {
    return $this->object;
  }
}