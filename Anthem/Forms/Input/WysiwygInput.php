<?php

namespace Anthem\Forms\Input;

use Silex\Application;
use Anthem\Forms\Input\TextareaInput;

/**
 * <textarea> input with integrated TinyMCE.
 *
 * Options:
 * - editor (array): TinyMCE configuration.
 */
class WysiwygInput extends TextareaInput
{
  /**
   * The constructor.  Sets up input options.
   *
   * @param \Silex\Application $app
   * @param array              $options
   */
  public function __construct(Application $app, array $options = array())
  {
    parent::__construct($app, $options);
    if (!isset($this->options['editor'])) $this->options['editor'] = array();
    $this->options['editor']['mode'] = 'specific_textareas';
  }

  /**
   * Returns HTML for input.
   *
   * @return string
   */
  public function render()
  {
    $this->options['editor']['editor_selector'] = 'wysiwyg-' . $this->getFullId();
    if (isset($this->options['readonly']))
      $this->options['editor']['readonly'] = $this->options['readonly'];
    return parent::render();
  }

  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:wysiwyg.php';
  }
}
