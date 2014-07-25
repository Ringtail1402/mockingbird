<?php

namespace Anthem\Forms\Input;

use Silex\Application;
use Anthem\Forms\Input\VirtualInputInterface;
use Anthem\Forms\Input\BaseInput;

/**
 * An input which allows associating an object to any object of another model.
 * Options are:
 * - ref_model (string, required): Ref model class.
 * - query_target_objects (function, required): A function which retrieves possible target models.
 * - query_ref_objects (function(object), required): A function which retrieves existing ref objects.
 * - set_ref_master_method (string, required): Method of ref model class to set master object.
 * - get_ref_target_id_method (string, required): Method of ref model class to get target object id.
 * - set_ref_target_id_method (string, required): Method of ref model class to set target object id.
 * - empty_message (string): Message to show if no target objects exist.
 */
class PropelManyToManyInput extends BaseInput
                            implements VirtualInputInterface
{
  /**
   * @var \BaseObject $master Master object.
   */
  protected $master;

  /**
   * @var \BaseObject[] $refs Ref objects.
   */
  protected $refs = array();

  /**
   * The constructor.  Checks that options are set, and initializes sub-forms.
   *
   * @param \Silex\Application $app
   * @param array $options
   * @throws \LogicException
   */
  public function __construct(Application $app, array $options = array())
  {
    foreach (array('ref_model', 'query_target_objects', 'query_ref_objects',
                   'set_ref_master_method', 'get_ref_target_id_method', 'set_ref_target_id_method') as $option)
      if (empty($options[$option]))
        throw new \LogicException($option . ' option must be set.');

    $options['target_objects'] = $options['query_target_objects']();
    $options['show_own_help'] = true;

    parent::__construct($app, $options);
  }

  /**
   * Loads a value from object.
   *
   * @param  $object
   * @return void
   */
  public function load($object)
  {
    $refs = $this->options['query_ref_objects']($object);
    foreach ($refs as $ref) $this->refs[call_user_func(array($ref, $this->options['get_ref_target_id_method']))] = $ref;

    $this->value = array();
    foreach ($this->refs as $target_id => $ref)
      $this->value[$target_id] = true;
  }

  /**
   * Saves a value into object.
   *
   * @param  $object
   * @return void
   */
  public function save($object)
  {
    if (is_array($this->value))
    {
      foreach ($this->value as $target_id => $enable)
      {
        if ($enable)
        {
          // Ref object already exists
          if (isset($this->refs[$target_id]))
          {
            unset($this->refs[$target_id]);
            continue;
          }

          // Create ref object
          $ref = new $this->options['ref_model']();
          call_user_func(array($ref, $this->options['set_ref_master_method']), $object);
          call_user_func(array($ref, $this->options['set_ref_target_id_method']), $target_id);
        }
      }
    }

    // Delete all leftover refs
    foreach ($this->refs as $ref) $ref->delete();
  }

  /**
   * Returns default template.
   *
   * @param  none
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:many_to_many.php';
  }
}
