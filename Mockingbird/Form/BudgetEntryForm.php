<?php

namespace Mockingbird\Form;

use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\SelectInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\NumberValidator;
use Anthem\Forms\Validator\PrimaryKeyValidator;
use Mockingbird\Input\BudgetEntryTypeInput;
use Mockingbird\Input\CurrencyInput;
use Mockingbird\Model\BudgetEntry;

/**
 * Budget entry form.  A subform of BudgetForm.
 */
class BudgetEntryForm extends Form
{
  /**
   * @var array
   */
  static protected $category_values = null;

  /**
   * Creates a form.
   *
   * @param \Silex\Application $app
   * @param BudgetEntry        $object
   */
  public function __construct($app, $object, array $options = array())
  {
    // Values for category select -- cache them
    if (self::$category_values == null)
    {
      $categories = $app['mockingbird.model.category']->getAll();
      self::$category_values = array();
      foreach ($categories as $category)
        self::$category_values[$category->getId()] = $category->getTitle();
    }

    // Values for when select
    $when_values = $options['when_values'];
    $when_values[''] = _t('BUDGET_WITHINPERIOD');

    // Convert amount from underlying currency
    $object->setAmount($object->getAmount() / $app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary()
                                            * $options['currency']->getRateToPrimary());

    return parent::__construct($app, $object, array_merge($options, array(
      'label'  => function () use ($object) {
        if ($object->isNew()) return _t('BUDGET_ENTRY_NEW');
        return ($object->getAmount() > 0 ? _t('BUDGET_INCOME') : _t('BUDGET_SPENDING')) . '&nbsp;&mdash; ' .
               htmlspecialchars($object->getCategory()->getTitle()) . '&nbsp;&mdash; ' .
               ($object->getWhen()
                 ? (($object->getBudget()->getMonth()
                       ? strftime('%x', mktime(0, 0, 0, $object->getBudget()->getMonth(), $object->getWhen(), $object->getBudget()->getYear()))
                       : strftime('%B %Y', mktime(0, 0, 0, $object->getWhen(), 1, $object->getBudget()->getYear()))))
                 : _t('BUDGET_WITHINPERIOD'));
      },
      'no_top_label' => true,
      'fields' => array(
        'type' => new BudgetEntryTypeInput($app, array(
          'label'        => _t('TYPE'),
        )),
        'category_id' => new SelectInput($app, array(
          'label'        => _t('CATEGORY'),
          'validator'    => array(new RequiredValidator(),
                                  new PrimaryKeyValidator(array('model' => 'Mockingbird\\Model\\TransactionCategory'))),
          'values'       => self::$category_values,
        )),
        'amount'    => new CurrencyInput($app, array(
          'label'        => _t('AMOUNT'),
          'validator'    => array(new RequiredValidator(), new NumberValidator()),
          'currency'     => $app['mockingbird.model.currency']->getDefaultCurrency(),
          'absolute'     => true,
        )),
        'when' => new SelectInput($app, array(
          'label'        => $options['when_label'],
          'values'       => $when_values
        )),
        'description' => new StringInput($app, array(
          'label'        => _t('DESCRIPTION'),
        )),
      ),
    )));
  }

  public function save()
  {
    $object = parent::save();

    // Convert amount to underlying currency
    $object->setAmount($object->getAmount() / $this->options['currency']->getRateToPrimary()
                                            * $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary());

    return $object;
  }
}
