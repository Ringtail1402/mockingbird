<?php

namespace Anthem\Forms\Input;

use Silex\Application;
use Anthem\Forms\Input\BaseInput;

/**
 * This is a group of several inputs.  This is a base class for forms, but also tab pages etc.
 *
 * Options:
 * - fields (array, required): Array of child inputs.
 * - add_prefix (boolean): Append this group name to child input names.  The difference between:
 *     child1, child2, child3 and group[child1], group[child2], group[child3].  Default is true.
 */
abstract class GroupInput extends BaseInput
{
 /**
  * The constructor.  Sets child inputs name and prefix options.
  *
  * @param \Silex\Application $app
  * @param array $options
  */
  public function __construct(Application $app, array $options = array())
  {
    parent::__construct($app, $options);
    if (!isset($this->options['fields']))
      throw new \LogicException('Fields option must be set.');
    if (!isset($this->options['name']))
      $this->options['name'] = null;  // Allowed for this type
    if (!isset($this->options['add_prefix']))
      $this->options['add_prefix'] = true;
    foreach ($this->options['fields'] as $name => $input)
    {
      $input->setOption('name', $name);
      $input->setOption('prefix', $this->options['add_prefix'] && $this->options['name']
                                  ? $this->getFullname()
                                  : (isset($this->options['prefix']) ? $this->options['prefix'] : ''));
    }
  }

 /**
  * Sets an option.  Handles name and prefix option changes,
  * propagating them to child inputs.
  *
  * @param  string $name
  * @param  string $value
  * @return void
  */
  public function setOption($name, $value)
  {
    parent::setOption($name, $value);
    if ($name == 'name' || $name == 'prefix')
    {
      foreach ($this->options['fields'] as $name => $input)
      {
        $input->setOption('prefix', $this->options['add_prefix'] && $this->options['name']
                                    ? $this->getFullname()
                                    : (isset($this->options['prefix']) ? $this->options['prefix'] : ''));
      }
    }
  }

  /**
   * Sets readonly option for this input and all subinputs.
   *
   * @param boolean $ro
   * @return void
   */
  public function setReadOnly($ro)
  {
    parent::setReadOnly($ro);
    foreach ($this->options['fields'] as $input) $input->setReadOnly($ro);
  }

 /**
  * Returns values of sub-input in an array.
  *
  * @param  none
  * @return array
  */
  public function getValue()
  {
    $result = array();
    foreach ($this->options['fields'] as $name => $input)
      $result[$name] = $input->getValue();
    return $result;
  }

 /**
  * Sets values of sub-inputs from an array.
  *
  * @param  $value
  * @return void
  */
  public function setValue($value)
  {
    foreach ($this->options['fields'] as $name => $input)
    {
      $name = str_replace('.', '_', $name);
      if (isset($value[$name]))
        $input->setValue($value[$name]);
      else
        $input->setValue(null);
    }
  }

 /**
  * Returns default template.
  *
  * @param  none
  * @return string
  */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:group.php';
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
    $complete_valid = parent::validate();

    foreach ($this->options['fields'] as $name => $input)
    {
      // Skip readonly fields
      if ($input->getOption('readonly')) continue;

      $valid = $input->validate();
      $children_valid = $children_valid && $valid;
      $complete_valid = $complete_valid && $valid;
    }

    if (!$children_valid)
      $this->errors['default'] = _t('Invalid.');

    return $complete_valid;
  }
}