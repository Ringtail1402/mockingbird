<?php

namespace Anthem\Forms\Input;

use Silex\Application;
use Anthem\Forms\Input\VirtualInputInterface;
use Anthem\Forms\Input\GroupInput;

/**
 * A complex input which allows editing a number of sub-objects within a single form.
 * Options are:
 * - model (string, required): Model class.
 * - form (string, required): Sub-form class.
 * - form_options (array): Options for sub-form constructors.
 * - master_object (object, required): Master object.
 * - query (function, required): A function which retrieves sub-objects, given a master object.
 * - set_subobjects_method (string, required): Method of model class to set master object.
 */
class PropelSubformsInput extends GroupInput
                          implements VirtualInputInterface
{
  /**
   * @var \BaseObject $master Master object.
   */
  protected $master;

  /**
   * @var \BaseObject[] $objects Objects displayed in a form.
   */
  protected $objects;

  /**
   * The constructor.  Checks that options are set, and initializes sub-forms.
   *
   * @param \Silex\Application $app
   * @param array $options
   * @throws \LogicException
   */
  public function __construct(Application $app, array $options = array())
  {
    foreach (array('model', 'form', 'query', 'master_object', 'set_subobjects_method') as $option)
      if (empty($options[$option]))
        throw new \LogicException($option . ' option must be set.');

    $this->master = $options['master_object'];

    $forms = array();
    if (!isset($options['form_options'])) $options['form_options'] = array();
    $this->objects = $options['query']($this->master, $this);
    foreach ($this->objects as $object)
      $forms[$object->getPrimaryKey()] = new $options['form']($app, $object, $options['form_options']);
    $forms['__NEW'] = new $options['form']($app, new $options['model'](), $options['form_options']);
    $options['fields'] = $forms;
    $options['skip_labels'] = true;
    $options['save_even_when_readonly'] = true;

    parent::__construct($app, $options);
  }

  /**
   * Loads a value from object.
   *
   * @param  $object
   * @return void
   */
  function load($object)
  {
    // Sub-forms are loaded upon their construction, do nothing here.
  }

  /**
   * Sets values of sub-forms from an array.
   *
   * @param  $value
   * @return void
   */
  public function setValue($value)
  {
    // Get original form ids
    $ids = array_combine(array_keys($this->options['fields']), array_keys($this->options['fields']));
    unset($ids['__NEW']);  // Skip template form

    // Use an array of sub-form ids as our own value
    $this->value = array();

    if (is_array($value)) foreach ($value as $id => $subvalue)
    {
      // Create a form for new object if necessary
      if (!isset($this->options['fields'][$id]))
      {
        $subform = new $this->options['form']($this->app, new $this->options['model'](), $this->options['form_options']);
        $subform->setOption('name', $id);
        $subform->setOption('prefix', $this->getFullname());
        $this->options['fields'][$id] = $subform;
      }

      $this->options['fields'][$id]->setValue($subvalue);

      unset($ids[$id]);
      $this->value[] = $id;
    }

    // Remove forms with no values (deleted by user).
    foreach ($ids as $id) unset($this->options['fields'][$id]);
  }

  /**
   * Saves a value into object.
   *
   * @param  $object
   * @return void
   */
  function save($object)
  {
    // Get original object ids
    $ids = array();
    $objects = new \PropelCollection();
    foreach ($this->objects as $object) $ids[$object->getPrimaryKey()] = $object->getPrimaryKey();

    // Save all subforms into objects
    foreach ($this->options['fields'] as $id => $subform)
    {
      if ($id == '__NEW') continue;  // Skip template form
      $subform->save();
      $objects[] = $subform->getObject();
    }

    // Save all objects
    foreach ($objects as $object)
      unset($ids[$object->getPrimaryKey()]);
    call_user_func(array($this->master, $this->options['set_subobjects_method']), $objects);

    // Delete all leftover objects
    if (count($ids))
      call_user_func(array($this->options['model'] . 'Query', 'create'))
        ->filterByPrimaryKeys($ids)
        ->delete();
    $this->objects = $objects;
  }

  /**
   * Validates input group.  This means both validating all children
   * and executing some or all validators on this sub-input group itself.
   *
   * @param  none
   * @return bool
   */
  public function validate()
  {
    $children_valid = true;

    // Validate this input, but hide __NEW field temporarily
    $__new = $this->options['fields']['__NEW'];
    unset($this->options['fields']['__NEW']);
    $complete_valid = BaseInput::validate();
    $this->options['fields']['__NEW'] = $__new;

    foreach ($this->options['fields'] as $name => $input)
    {
      // Skip template form
      if ($name == '__NEW') continue;

      // Skip readonly forms
      if ($input->getOption('readonly')) continue;

      $valid = $input->validate();
      $children_valid = $children_valid && $valid;
      $complete_valid = $complete_valid && $valid;
    }

    if (!$children_valid)
      $this->errors['default'] = true;  // No error message

    return $complete_valid;
  }

  /**
   * Returns default template.
   *
   * @param  none
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:subforms.php';
  }
}
