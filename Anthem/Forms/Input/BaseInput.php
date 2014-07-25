<?php

namespace Anthem\Forms\Input;

use Silex\Application;

/**
 * Base class for form inputs.
 */
abstract class BaseInput
{
  /**
   * @var \Silex\Application
   */
  protected $app;

  /**
   * @var string Input name.
   */
  protected $name;

  /**
   * @var array Input options.
   */
  protected $options;

  /**
   * @var mixed Input value.
   */
  protected $value = null;

  /**
   * @var string[] Validation errors.
   */
  protected $errors = array();

  /**
   * The constructor.  Sets up input options.
   * Options are:
   * - name (string) Name.  Might be unset for forms but is required for actual inputs.
   * - value (mixed) Initial value.
   * - is_virtual (boolean) Input will be excluded from default form load/save, value must be set and retrieved
   *           manually.  An input may also implement VirtualInputInterface for custom load/save logic.
   * - label (string) Label text.
   * - help (string) Small label text.
   * - prefix (string) Prefix used for <input name="..."> attribute (e.g. form, form[subform])
   * - class (string) Extra classes, if any.
   * - readonly (boolean) Is the field read only.
   * - template (string) Custom input template.
   * - validator (array) A validator or an array of validators, if any.
   *           This is for server-side validation, managed by Forms\Validator\BaseValidator-based classes
   * - js_validator (array) JS validation options, if any.  (validator_name => options).
   *           This is for client-side validation.  This is completely independent from server-side
   *           validation.  Client-side validation is managed by Mootools-More Form.Validator component.
   *
   * @param \Silex\Application $app
   * @param array              $options
   */
  public function __construct(Application $app, array $options = array())
  {
    $this->app     = $app;
    $this->options = $options;

    if (!empty($options['value'])) $this->value = $options['value'];
  }

  /**
   * Returns input name.
   *
   * @return string
   * @throws \LogicException
   */
  public function getName()
  {
    if (!isset($this->options['name']))
      throw new \LogicException('Name not set for input of type \'' . get_class($this) . '\'.');
    return $this->options['name'];
  }

  /**
   * Returns an input option.
   *
   * @param  string $name
   * @return mixed
   */
  public function getOption($name)
  {
    if (isset($this->options[$name]))
      return $this->options[$name];
    else
      return null;
  }

  /**
   * Sets an input option.
   *
   * @param  string $name
   * @param  mixed  $value
   * @return void
   */
  public function setOption($name, $value)
  {
    $this->options[$name] = $value;
  }

  /**
   * Sets readonly option.
   *
   * @param boolean $ro
   * @return void
   */
  public function setReadOnly($ro)
  {
    $this->setOption('readonly', $ro);
  }

  /**
   * Returns input id (with possible prefix).
   *
   * @return string
   */
  public function getFullId()
  {
    $id = $this->getFullname();
    $id = str_replace('[', '_', $id);
    $id = str_replace(']', '', $id);
    return $id;
  }

  /**
   * Returns input full name (with possible prefix).
   *
   * @return string
   */
  public function getFullname()
  {
    if (!isset($this->options['name'])) return '';
    if (isset($this->options['prefix']) && $this->options['prefix'])
      return sprintf('%s[%s]', $this->options['prefix'], $this->getName());
    return $this->options['name'];
  }

  /**
   * Returns input class atribute.
   *
   * @return string
   */
  public function getFullclass()
  {
    return isset($this->options['class']) ? $this->options['class'] : '';
  }

  /**
   * Returns input data-validator-properties="..." attributes.
   *
   * @return string
   */
  public function getFullJSValidationOptions()
  {
    if (!isset($this->options['js_validator'])) return '';

    return 'data-validators="' . htmlspecialchars($this->options['js_validator']) . '" ';
  }

  /**
   * Sets input value.  Normalizes it if necessary.
   *
   * @param mixed $value
   * @throws \LogicException
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

  /**
   * Adds an error manually.
   *
   * @param  string $message
   * @return void
   */
  public function addError($message)
  {
    $this->errors[] = $message;
  }

  /**
   * Returns input value.  The value might not necessarily be valid.
   *
   * @return mixed
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Returns validation errors (if any).
   *
   * @return string[]
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * Returns visible validation errors (if any).
   * Invisible errors have a true value instead of message.
   * They aren't displayed in the form template, but invalidate the form nonetheless.
   *
   * @return string[]
   */
  public function getVisibleErrors()
  {
    $errors = $this->errors;
    foreach ($errors as $key => $error)
      if ($error && !is_string($error)) unset($errors[$key]);
    return $errors;
  }


  /**
   * Returns default input template.
   *
   * @abstract
   * @return string
   */
  abstract protected function getDefaultTemplate();

  /**
   * If this returns false, labels etc. are never rendered.
   *
   * @return boolean
   */
  public function isInvisible()
  {
    return false;
  }

  /**
   * Returns HTML for input.
   *
   * @return string
   */
  public function render()
  {
    $template = $this->getDefaultTemplate();
    if (isset($this->options['template']))
      $template = $this->options['template'];

    return $this->app['core.view']->render($template, array(
      'id'       => $this->getFullId(),
      'name'     => $this->getFullName(),
      'class'    => $this->getFullClass(),
      'js_validation_options' => $this->getFullJSValidationOptions(),
      'value'    => $this->value,
      'options'  => $this->options,
      'valid'    => !count($this->errors),
      'errors'   => $this->getVisibleErrors(),
    ));
  }

  /**
   * Adds a validator to this input.
   *
   * @param  \Anthem\Forms\Validator\BaseValidator $validator
   * @return void
   */
  public function addValidator($validator)
  {
    if (!isset($this->options['validator'])) $this->options['validator'] = array();
    $this->options['validator'][] = $validator;
  }

  /**
   * Validates value according to 'validator' option.
   * Fills in $error array.
   *
   * @return bool
   */
  public function validate()
  {
    // Always valid if no validators are set
    if (!isset($this->options['validator'])) return true;

    // Validators array
    $validators = $this->options['validator'];
    if (!is_array($validators)) $validators = array($validators);

    // Cycle through validators, storing error messages (if any)
    $valid = true;
    $this->errors = array();
    foreach ($validators as $validator)
    {
      $result = $validator->validate($this->getValue());
      if (is_string($result) && $result)
      {
        $valid = false;
        $this->errors[$result] = $result;
        break;
      }
      elseif (!$result)
      {
        $valid = false;
        $this->errors['default'] = _t('Forms.INVALID');
        break;
      }
    }

    return $valid;
  }
}