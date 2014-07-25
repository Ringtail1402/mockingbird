<?php

namespace Anthem\Core\View;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Basic view helpers (slots, view extension etc.)
 */
class ViewHelpers
{
 /**
  * @var \Silex\Application
  */
  protected $app;

 /**
  * @var string[string] Slot contents.
  */
  protected $slots = array();

 /**
  * @var string[] Active slot stack.
  */
  protected $slot_stack = array();

 /**
  * The constructor.
  *
  * @param \Silex\Application $app
  */
  public function __construct(Application $app)
  {
    $this->app = $app;
  }

 /**
  * Directly sets slot content.
  *
  * @param  string $name
  * @param  string $value
  * @return void
  */
  public function setSlot($name, $value)
  {
    $this->slots[$name] = $value;
  }

 /**
  * Starts slot content.
  *
  * @param  $name
  * @return void
  */
  public function beginSlot($name)
  {
    array_push($this->slot_stack, $name);
    ob_start();
  }

 /**
  * Ends last opened slot content.
  *
  * @param  none
  * @return void
  */
  public function endSlot()
  {
    $name = array_pop($this->slot_stack);
    $this->slots[$name] = ob_get_clean();
  }

 /**
  * Ends last opened slot content, appends it to old content.
  *
  * @param  none
  * @return void
  */
  public function endSlotAppend()
  {
    $name = array_pop($this->slot_stack);
    if (!$this->isSlotSet($name))
      $this->slots[$name] = ob_get_clean();
    else
      $this->slots[$name] .= ob_get_clean();
  }

 /**
  * Checks if the slot exists and is non-empty.
  *
  * @param  string $name
  * @return boolean
  */
  public function isSlotSet($name)
  {
    return isset($this->slots[$name]) && !empty($this->slots[$name]);
  }

 /**
  * Returns slot content.
  *
  * @param  string $name
  * @return string
  */
  public function getSlot($name)
  {
    if (!$this->isSlotSet($name)) return '';
    return $this->slots[$name];
  }

 /**
  * Begins a block of code where all whitespace is squashed.
  *
  * @param  none
  * @return void
  */
  public function squash()
  {
    ob_start();
  }

 /**
  * Ends a block of code where all whitespace is squashed.
  *
  * @param  none
  * @return void
  */
  public function endSquash()
  {
    echo trim(preg_replace('/[ \r\n\t]+/', ' ', ob_get_clean()));
  }

  /**
   * Expands a string, calling _t() on it.  If the argument is a function, calls it and returns result.
   *
   * @param  string|function $string
   * @return string
   */
  public function str($string)
  {
    if (is_callable($string))
      $string = $string($this->app);
    return _t($string);
  }

 /**
  * Includes another template.
  *
  * @param  string $template
  * @param  array  $params
  * @return string
  */
  public function sub($template, $params = array())
  {
    return $this->app['core.view']->render($template, $params);
  }

 /**
  * Sets a master template.  The entire content of current template will be passed
  * to master template as $content parameter.  All slot contents will be preserved.
  *
  * @param  string $template
  * @return void
  */
  public function extend($template = null)
  {
    if (!$template)
    {
      if (!isset($this->app['Core']['extend_default']))
        throw new \InvalidArgumentException('Default template to extend not found.');
      $template = $this->app['Core']['extend_default'];
    }

    $app = $this->app;
    $this->app->after(function(Request $request, Response $response) use ($app, $template) {
      $response->setContent($app['core.view']->render($template, array(
        'content' => $response->getContent()
      )));
    });
  }
}