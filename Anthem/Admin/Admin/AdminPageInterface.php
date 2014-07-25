<?php

namespace Anthem\Admin\Admin;

use Symfony\Component\HttpFoundation\Request;

/**
 * Base interface for admin page services.
 */
interface AdminPageInterface
{
 /**
  * Returns page title.
  *
  * @abstract
  * @return string
  */
  function getTitle();

 /**
  * Returns page subtitle.
  *
  * @abstract
  * @return string
  */
  function getSubtitle();

 /**
  * Renders a page.
  *
  * @abstract
  * @param  Request $request
  * @return string
  */
  function render(Request $request);

 /**
  * Returns a template for the specified part of page.
  *
  * @abstract
  * @param  string $template
  * @return string
  */
  function getTemplate($template);
}