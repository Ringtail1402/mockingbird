<?php

namespace Mockingbird\Form;

use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\StringInput;
use Anthem\Auth\Input\UserInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\UniqueValidator;
use Mockingbird\Model\CounterParty;
use Mockingbird\Model\CounterPartyQuery;

/**
 * Counterparty form.
 */
class CounterPartyForm extends Form
{
  /**
   * Creates a form.
   *
   * @param \Silex\Application $app
   * @param CounterParty       $object
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
      'label'  => function () use ($object) { return ($object->getTitle() ? htmlspecialchars($object->getTitle()) : _t('COUNTERPARTY_NEW')); },
      'fields' => array(
        'title'     => new StringInput($app, array(
          'label'        => _t('TITLE'),
          'help'         => _t('COUNTERPARTY_TITLE_HELP'),
          'validator'    => array(new RequiredValidator(), new UniqueValidator(array('query' => function($title) use ($object) {
            return CounterPartyQuery::create()
                                    ->filterByTitle($title)
                                    ->_if(!$object->isNew())
                                      ->filterById($object->getId(), \Criteria::NOT_EQUAL)
                                    ->_endif();
          }))),
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
