<?php

namespace Anthem\Forms\Validator;

/**
 * Base validator class.
 */
abstract class BaseValidator
{
 /**
  * @var array Validator options.
  */
  protected $options;

 /**
  * The constructor.
  *
  * @param array $options
  */
  public function __construct(array $options = array())
  {
    $this->options = $options;
  }

 /**
  * Function that will do actual work.
  *
  * @abstract
  * @param  mixed $value
  * @return boolean|string
  */
  abstract public function validate($value);
}