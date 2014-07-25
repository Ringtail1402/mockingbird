<?php

namespace AnthemCM\Pages\Form;

use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\CheckboxInput;
use Anthem\Forms\Input\WysiwygInput;
use Anthem\Forms\Validator\RequiredValidator;

class PageForm extends Form
{
  public function __construct($app, $object)
  {
    return parent::__construct($app, $object, array(
      'label'  => function () use ($object) { return $object->getTitle() ? $object->getTitle() : _t('Pages.NEW'); },
      'fields' => array(
        'title'     => new StringInput($app, array(
          'label'        => _t('Pages.TITLE'),
          'validator'    => new RequiredValidator(),
        )),
        'is_active' => new CheckboxInput($app, array(
          'label'        => _t('Pages.ACTIVE'),
        )),
        'url'       => new StringInput($app, array(
          'label'        => _t('Pages.URL'),
          'help'         => _t('Pages.URL_HELP'),
        )),
        'content'   => new WysiwygInput($app, array(
          'label'        => _t('Pages.CONTENT'),
          'help'         => _t('Pages.CONTENT_HELP'),
          'editor'       => array(
            'theme'        => 'advanced',
            'height'       => 400,
          ),
        )),
      ),
    ));
  }
}
