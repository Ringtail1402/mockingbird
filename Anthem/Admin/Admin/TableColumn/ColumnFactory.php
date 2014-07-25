<?php

namespace Anthem\Admin\Admin\TableColumn;

use Silex\Application;

/**
 * Columns instantiation.
 */
class ColumnFactory
{
 /**
  * @var \Silex\Application
  */
  protected $app;

 /**
  * @var string[string] Registered column types.
  */
  protected $column_types = array();

 /**
  * @var string[string] Javascripts which need to be included.
  */
  protected $javascripts = array();

 /**
  * The constructor.  Collects column types from all modules.
  *
  * @param \Silex\Application $app
  */
  public function __construct(Application $app)
  {
    $this->app = $app;
  }

 /**
  * Сonstructs a column from options.  $options['class'] must be set.
  *
  * @param  string $name
  * @param  array  $options
  * @return \Anthem\Admin\Admin\TableColumn\BaseColumn
  * @throws \InvalidArgumentException
  */
  public function createColumn($name, $options)
  {
    if (!isset($options['class']))
      throw new \InvalidArgumentException('Column type not set for column \'' . $name . '\'.');
    $class = $options['class'];
    if (!isset($options['options'])) $options['options'] = array();
    if (!empty($options['is_virtual']))
      $options['options']['is_virtual'] = $options['is_virtual'];

    $column = new $class($this->app, $name, $options['options']);
    $js = $column->getJS();
    if ($js) $this->javascripts[$js] = $js;
    return $column;
  }

 /**
  * Returns all registered column javascripts.
  *
  * @param  none
  * @return string[string]
  */
  public function getJavascripts()
  {
    return $this->javascripts;
  }
}
