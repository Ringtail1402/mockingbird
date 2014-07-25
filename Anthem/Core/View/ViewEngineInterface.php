<?php

namespace Anthem\Core\View;

/**
 * A trivial interface for view engines.
 */
interface ViewEngineInterface
{
 /**
  * Renders a template.
  *
  * @abstract
  * @param  string $template
  * @param  array  $params
  * @return string
  */
  function render($template, $params = array());
}