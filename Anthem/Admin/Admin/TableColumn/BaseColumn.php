<?php

namespace Anthem\Admin\Admin\TableColumn;

use Silex\Application;

/**
 * Base class for table fields.
 */
abstract class BaseColumn
{
 /**
  * @var \Silex\Application;
  */
  protected $app;

 /**
  * @var string Type name.
  */
  protected $typename;

 /**
  * @var string Field name.
  */
  protected $field;

 /**
  * @var array Field options.
  */
  protected $options;

 /**
  * @var array Templates
  */
  protected $templates;

 /**
  * The constructor.
  *
  * @param \Silex\Application $app
  * @param string $field
  * @param array  $options
  */
  public function __construct(Application $app, $field, array $options = array())
  {
    $this->app      = $app;
    $this->field    = $field;
    $this->options  = $options;

    $this->options['field'] = $field;
    $this->templates = $this->getDefaultTemplates();
    if (isset($options['template']))
      $this->templates['value'] = $options['template'];
    if (isset($options['template_filter']))
      $this->templates['filter'] = $options['template_filter'];
  }

 /**
  * Returns column type name.
  *
  * @abstract
  * @return string
  */
  abstract public function getTypeName();

 /**
  * Adds sorting criteria for this field to the query.
  *
  * @param  mixed  $query
  * @param  string $dir
  * @return mixed
  */
  abstract public function addSortCriteria($query, $dir);

 /**
  * Adds filtering criteria for this field to the query.
  *
  * @param  mixed $query
  * @param  mixed $filter
  * @return mixed
  */
  abstract public function addFilter($query, &$filter);

 /**
  * Returns templates which this column should use.  Must return
  * an array with two entries, 'value' and 'filter'.
  *
  * @abstract
  * @return array
  */
  abstract protected function getDefaultTemplates();

 /**
  * Renders a value of this field.  If $link parameter is not null,
  * it holds HTML attributes for the link which will pop up edit form.
  *
  * @param  mixed  $value
  * @param  object $object
  * @param  string $link
  * @return string
  */
  public function renderField($value, $object, $link = null)
  {
    return $this->app['core.view']->render($this->templates['value'], array(
      'value'   => $value,
      'object'  => $object,
      'url'     => $link,
      'options' => $this->options,
    ));
  }

 /**
  * Renders filter control(s) for this field, filled with data.
  *
  * @param  mixed $filter
  * @param  string|null $variant
  * @return string
  */
  public function renderFilter($filter, $variant = null)
  {
    $template = $this->templates['filter'];
    if ($variant && isset($this->templates['filter.' . $variant]))
      $template = $this->templates['filter.' . $variant];

    return $this->app['core.view']->render($template, array(
      'field'   => $this->field,
      'filter'  => $filter,
      'options' => $this->options,
    ));
  }

  /**
   * Returns JS which needs to be included once if at least one column of this type is present.
   * This JS may include handlers for "tableupdated" event.
   *
   * @return string|null
   */
  public function getJS()
  {
    return null;
  }
}