<?php

namespace Mockingbird\Form;

use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\HiddenInput;
use Anthem\Forms\Input\PropelSubformsInput;
use Anthem\Forms\Validator\RequiredValidator;
use Mockingbird\Model\Budget;
use Mockingbird\Input\BudgetCopyInput;

/**
 * Budget form.  Pretty much a container for BudgetEntry forms.
 */
class BudgetForm extends Form
{
  /**
   * @var boolean  Is the form in semi-read-only mode due to budget being too old.
   */
  protected $is_old;

  /**
   * Creates a form.
   *
   * @param \Silex\Application $app
   * @param Budget             $object
   */
  public function __construct($app, $object)
  {
    $when_values = array();
    if ($object->getMonth())
    {
      $date = new \DateTime($object->getYear() . '-' . $object->getMonth() . '-01 00:00:00');
      $date->add(new \DateInterval('P1M'))->sub(new \DateInterval('PT1S'));
      $max_day = $date->format('d');
      for ($i = 1; $i <= $max_day; $i++)
        $when_values[$i] = strftime('%x', mktime(0, 0, 0, $object->getMonth(), $i, $object->getYear()));
    }
    else
    {
      for ($i = 1; $i <= 12; $i++)
        $when_values[$i] = strftime('%B', mktime(0, 0, 0, $i, 1, $object->getYear()));
    }

    if (!$object->getCurrency())
      $object->setCurrency($app['mockingbird.model.currency']->getDefaultCurrency());

    return parent::__construct($app, $object, array(
      'label'  => function () use ($app, $object) {
                    return _t('BUDGET_TITLE', $object->getMonth()
                                                ? strftime('%B %Y', mktime(0, 0, 0, $object->getMonth(), 1, $object->getYear()))
                                                : $object->getYear());
                  },
      'fields' => array(
        'year' => new HiddenInput($app),
        'month' => new HiddenInput($app),
        'entries' => new PropelSubformsInput($app, array(
          'label'        => _t('ENTRIES'),
          'master_object' => $object,
          'model'        => 'Mockingbird\\Model\\BudgetEntry',
          'form'         => 'Mockingbird\\Form\\BudgetEntryForm',
          'form_options' => array(
            'when_label'  => $object->getMonth() ? _t('BUDGET_DAY') : _t('BUDGET_MONTH'),
            'when_values' => $when_values,
            'currency'    => $object->getCurrency(),
          ),
          'query'        => function ($object, $field) use ($app) { return $app['mockingbird.model.budget']->getBudgetEntries($object); },
          'set_subobjects_method' => 'setEntrys',
        )),
        'copy' => new BudgetCopyInput($app, array(
          'label'        => _t('COPY_FROM'),
          'except'       => $object->getId(),
          'user'         => $object->getUser(),
        )),
      ),
    ));
  }

  public function validate()
  {
    if (!$this->options['fields']['copy']->getValue())
      $this->options['fields']['entries']->addValidator(new RequiredValidator(array('message' => _t('ERROR_BUDGET_ENTRIES_NEEDED'))));

    return parent::validate();
  }
}
