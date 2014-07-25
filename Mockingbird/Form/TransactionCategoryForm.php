<?php

namespace Mockingbird\Form;

use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\ColorInput;
use Anthem\Auth\Input\UserInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\UniqueValidator;
use Anthem\Forms\Validator\PrimaryKeyValidator;
use Mockingbird\Model\TransactionCategory;
use Mockingbird\Model\TransactionCategoryQuery;

/**
 * Category form.
 */
class TransactionCategoryForm extends Form
{
  /**
   * Creates a form.
   *
   * @param \Silex\Application  $app
   * @param TransactionCategory $object
   */
  public function __construct($app, $object)
  {
    if ($object->getUser())
      $user = $object->getUser();
    else
    {
      $user = $app['auth']->getUser();
      $object->setUser($user);
    }

    $options = array(
      'label'  => function () use ($object) { return ($object->getTitle() ? htmlspecialchars($object->getTitle()) : _t('CATEGORY_NEW')); },
      'fields' => array(
        'title'     => new StringInput($app, array(
          'label'        => _t('TITLE'),
          'help'         => _t('CATEGORY_TITLE_HELP'),
          'validator'    => array(new RequiredValidator(), new UniqueValidator(array('query' => function($title) use ($user, $object) {
            return TransactionCategoryQuery::create()
                                           ->filterByUser($user)
                                           ->filterByTitle($title)
                                           ->_if(!$object->isNew())
                                             ->filterById($object->getId(), \Criteria::NOT_EQUAL)
                                           ->_endif();
          }))),
        )),
        'color'     => new ColorInput($app, array(
          'label'        => _t('COLOR'),
          'help'         => _t('CATEGORY_COLOR_HELP'),
        )),
      ),
    );

    if ($app['auth']->hasPolicies('mockingbird.alldata.ro'))
    {
      $options['fields']['user_id'] = new UserInput($app, array(
        'label'     => _t('USER'),
        'readonly'  => true,
      ));
    }

    parent::__construct($app, $object, $options);

    if ($app['auth']->hasPolicies('mockingbird.alldata.ro')) $this->setReadOnly(true);
  }
}
