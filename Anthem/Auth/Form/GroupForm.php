<?php

namespace Anthem\Auth\Form;

use Silex\Application;
use Anthem\Forms\Form\Form;
use Anthem\Auth\Model\Group;
use Anthem\Auth\Model\GroupQuery;
use Anthem\Forms\Input\StringInput;
use Anthem\Auth\Input\PoliciesInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\UniqueValidator;

/**
 * Group editing form.
 */
class GroupForm extends Form
{
  public function __construct(Application $app, $group)
  {
    parent::__construct($app, $group, array(
      'label'  => function () use ($group) { return ($group->getTitle() ? htmlspecialchars($group->getTitle()) : _t('Auth.GROUP_NEW')); },
      'fields' => array(
        'title'    => new StringInput($app, array(
          'label'     => _t('Auth.TITLE'),
          'validator' => array(
            new RequiredValidator(),
            new UniqueValidator(array(
              'query' => function ($title) use ($group) {
                return GroupQuery::create()
                                 ->_if($group->getId())
                                   ->filterById($group->getId(), \Criteria::NOT_EQUAL)
                                 ->_endif()
                                 ->filterByTitle($title);
              },
              'message' => _t('Auth.UNIQUE_GROUP_TITLE_VALIDATOR_MESSAGE'),
            ))
          ),
        )),
        'policies' => new PoliciesInput($app, array(
          'label'     => _t('Auth.POLICIES'),
          'mode'      => 'group',
        )),
      ),
    ));
  }
}
