<?php

namespace Anthem\Auth\Input;

use Silex\Application;
use Anthem\Forms\Input\SelectInput;
use Anthem\Auth\Model\GroupQuery;

class GroupInput extends SelectInput
{
  public function __construct(Application $app, array $options = array())
  {
    $options['values'] = array(null => '');

    foreach (GroupQuery::create()
                       ->orderByTitle()
                       ->find() as $group)
      $options['values'][$group->getId()] = $group->getTitle();

    parent::__construct($app, $options);
  }

  public function getValue()
  {
    $value = parent::getValue();
    if (!$value) return null;
    return $value;
  }
}