<?php

namespace Mockingbird\Form;

use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\HiddenInput;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\NumberValidator;
use Anthem\Forms\Validator\EqualityValidator;
use Mockingbird\Input\CurrencyInput;
use Mockingbird\Input\TransactionCategoryInput;
use Mockingbird\Input\TransactionTagsInput;
use Mockingbird\Model\Transaction;

/**
 * Sub-transaction form.  Pretty much a TransactionForm with only four inputs.
 */
class SubTransactionForm extends Form
{
  /**
   * @var boolean  Is the form in semi-read-only mode due to transaction being too old.
   */
  protected $is_old;

  /**
   * Creates a form.
   *
   * @param \Silex\Application $app
   * @param Transaction        $object
   */
  public function __construct($app, $object)
  {
    $this->is_old = $is_old = !$object->isNew() && !$object->getIsprojected() &&
                              time() - $object->getParentTransaction()->getUpdatedAt('U') > 86400 * $app['settings']->get('mockingbird.max_transaction_editable_age');

    return parent::__construct($app, $object, array(
      'label'  => function () use ($object) { return ($object->getTitle() ? htmlspecialchars($object->getTitle()) : _t('TRANSACTION_NEW')); },
      'no_top_label' => true,
      'fields' => array(
        'title'     => new StringInput($app, array(
          'label'        => _t('TITLE'),
          'validator'    => new RequiredValidator(),
        )),
        'amount'    => new CurrencyInput($app, array(
          'label'        => _t('AMOUNT'),
          'validator'    => array(new RequiredValidator(), new NumberValidator()),
          'currency'     => $object->getAccount() ? $object->getAccount()->getCurrency() :
              $app['mockingbird.model.currency']->getDefaultCurrency(),
          'absolute'     => true,
          'readonly'     => $this->is_old,
        )),
        'category_id'   => new TransactionCategoryInput($app, array(
          'label'        => _t('CATEGORY'),
        )),
        'tags'          => new TransactionTagsInput($app, array(
          'label'        => _t('TAGS'),
        )),
      ),
    ));
  }

  /**
   * Saves form.  Normalizes amount.
   *
   * @return Transaction
   */
  public function save()
  {
    $object = parent::save();
    $object->setAmount(sprintf('%.2F', -abs($object->getAmount())));
    return $object;
  }
}
