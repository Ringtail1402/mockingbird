<?php

namespace Mockingbird\Form;

use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\HiddenInput;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\DateTimeInput;
use Anthem\Forms\Input\PropelSubformsInput;
use Anthem\Auth\Input\UserInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\NumberValidator;
use Anthem\Forms\Validator\PrimaryKeyValidator;
use Anthem\Forms\Validator\EqualityValidator;
use Anthem\Forms\Validator\ClosureValidator;
use Mockingbird\Input\AccountInput;
use Mockingbird\Input\CurrencyInput;
use Mockingbird\Input\TransactionTypeInput;
use Mockingbird\Input\TransactionTargetAmountInput;
use Mockingbird\Input\TransactionCounterPartyInput;
use Mockingbird\Input\TransactionCategoryInput;
use Mockingbird\Input\TransactionTagsInput;
use Mockingbird\Model\Transaction;

/**
 * Transaction form.  Intended only for master transactions.
 */
class TransactionForm extends Form
{
  /**
   * @var boolean  Is the form in semi-read-only mode due to transaction being too old.
   */
  protected $is_old;

  /**
   * @var \Anthem\Auth\Model\User
   */
  protected $user;

  /**
   * Creates a form.
   *
   * @param \Silex\Application $app
   * @param Transaction        $object
   */
  public function __construct($app, $object)
  {
    $this->is_old = $is_old = !$object->isNew() && !$object->getIsprojected() &&
                              time() - $object->getUpdatedAt('U') > 86400 * $app['settings']->get('mockingbird.max_transaction_editable_age');
    if ($object->getUser())
      $user = $object->getUser();
    else
    {
      $user = $app['auth']->getUser();
      $object->setUser($user);
    }
    $this->user = $user;

    $options = array(
      'label'  => function () use ($app, $object, $is_old) {
                    return ($object->getTitle() ? htmlspecialchars($object->getTitle()) : _t('TRANSACTION_NEW')) .
                           ($is_old ? $app['core.view']->render('Mockingbird:form_alert.php', array(
                             'alert' => _t('WARNING_OLD_TRANSACTION', $app['settings']->get('mockingbird.max_transaction_editable_age'))
                           )) : '');
                  },
      'fields' => array(
        'title'     => new StringInput($app, array(
          'label'        => _t('TITLE'),
          'help'         => _t('TRANSACTION_TITLE_HELP'),
          'validator'    => new RequiredValidator(),
          'js_validator' => 'required',
        )),
        'created_at' => new DateTimeInput($app, array(
          'label'        => _t('CREATED_AT'),
          'help'         => _t('TRANSACTION_CREATED_AT_HELP'),
          'readonly'     => $this->is_old,
        )),
        'type'      => new TransactionTypeInput($app, array(
          'label'        => _t('TYPE'),
          'validator'    => new RequiredValidator(),
          'readonly'     => $this->is_old,
        )),
        'account_id' => new AccountInput($app, array(
          'label'        => _t('ACCOUNT'),
          'help'         => _t('TRANSACTION_ACCOUNT_HELP'),
          'validator'    => array(
            new PrimaryKeyValidator(array('model' => 'Mockingbird\\Model\\Account',
                                          'message' => _t('ERROR_ACCOUNT_NEEDED')))),
            new ClosureValidator(array(
              'closure' => function ($value) use ($user) {
                $account = \Mockingbird\Model\AccountQuery::create()->findPk($value);
                return $account && $account->getUserId() == $user->getId();
              },
              'message' => _t('ERROR_INVALID_USER'),
          )),
          'add_empty'    => true,
          'readonly'     => $this->is_old,
          'user'         => $user,
        )),
        'amount'    => new CurrencyInput($app, array(
          'label'        => _t('AMOUNT'),
          'help'         => _t('TRANSACTION_AMOUNT_HELP'),
          'currency'     => $object->getAccount() ? $object->getAccount()->getCurrency() :
                            $app['mockingbird.model.currency']->getDefaultCurrency(),
          'absolute'     => true,
          'readonly'     => $this->is_old,
        )),
        'counter_transaction_id' => new HiddenInput($app),
        'target_account_id' => new AccountInput($app, array(
          'label'        => _t('TARGET_ACCOUNT'),
          'help'         => _t('TRANSACTION_TARGET_ACCOUNT_HELP'),
          'add_empty'    => true,
          'readonly'     => $this->is_old,
          'user'         => $user,
          //'no_debit_accounts' => true,
        )),
        'target_amount' => new TransactionTargetAmountInput($app, array(
          'label'        => _t('TARGET_AMOUNT'),
          'help'         => _t('TRANSACTION_TARGET_AMOUNT_HELP'),
          'currency'     => $object->getTargetAccount() ? $object->getTargetAccount()->getCurrency() :
                            $app['mockingbird.model.currency']->getDefaultCurrency(),
          'absolute'     => true,
          'readonly'     => $this->is_old,
        )),
        'counter_party' => new TransactionCounterPartyInput($app, array(
          'label'        => _t('COUNTER_PARTY'),
          'help'         => _t('TRANSACTION_COUNTER_PARTY_HELP'),
          'readonly'     => $this->is_old,
          'user'         => $user,
        )),
        'category_id'   => new TransactionCategoryInput($app, array(
          'label'        => _t('CATEGORY'),
          'help'         => _t('TRANSACTION_CATEGORY_HELP'),
          'user'         => $user,
        )),
        'tags'          => new TransactionTagsInput($app, array(
          'label'        => _t('TAGS'),
          'help'         => _t('TRANSACTION_TAGS_HELP'),
          'user'         => $user,
        )),
        'subtransactions' => new PropelSubformsInput($app, array(
          'label'        => _t('SUBTRANSACTIONS'),
          'master_object' => $object,
          'model'        => 'Mockingbird\\Model\\Transaction',
          'form'         => 'Mockingbird\\Form\\SubTransactionForm',
          'query'        => function ($object, $field) { return $object->getSubTransactionss(); },
          'set_subobjects_method' => 'setSubTransactionss',
          'readonly'     => $this->is_old,
        )),
      ),
    );

    if ($app['auth']->hasPolicies('mockingbird.alldata.ro'))
    {
      $options['fields']['user_id'] = new UserInput($app, array(
        'label'     => _t('USER'),
        'readonly'  => true,  // Otherwise we would have to make <select> tags contents dynamic
      ));
    }

    parent::__construct($app, $object, $options);

    if ($app['auth']->hasPolicies('mockingbird.alldata.ro')) $this->setReadOnly(true);

  }

  /**
   * Validates form.  Sets up some field validators on the fly.
   *
   * @return bool
   */
  public function validate()
  {
    $user = $this->user;

    $type       = $this->options['fields']['type']->getValue();
    $account_id = $this->options['fields']['account_id']->getValue();

    // Validate fields for transfer
    if ($type == 'transfer')
    {
      $this->options['fields']['target_account_id']->addValidator(new PrimaryKeyValidator(array('model' => 'Mockingbird\\Model\\Account',
                                                                                                'message' => _t('ERROR_ACCOUNT_NEEDED'))));
      $this->options['fields']['target_account_id']->addValidator(new EqualityValidator(array('value' => function () use ($account_id) { return $account_id; },
                                                                                              'not_equal' => true,
                                                                                              'message' => _t('ERROR_DIFFERENT_ACCOUNT_NEEDED'))));
      $this->options['fields']['target_account_id']->addValidator(new ClosureValidator(array(
        'closure' => function ($value) use ($user) {
          $account = \Mockingbird\Model\AccountQuery::create()->findPk($value);
          return $account && $account->getUserId() == $user->getId();
        },
        'message' => _t('ERROR_INVALID_USER'),
      )));

      $this->options['fields']['target_amount']->addValidator(new RequiredValidator());
      $this->options['fields']['target_amount']->addValidator(new NumberValidator());
    }
    else
      $this->options['fields']['counter_party']->addValidator(new RequiredValidator());

    // Amount is required for all types but master
    if ($type != 'master')
    {
      $this->options['fields']['amount']->addValidator(new RequiredValidator());
      $this->options['fields']['amount']->addValidator(new NumberValidator());
    }
    else
    {
      $this->options['fields']['subtransactions']->addValidator(new RequiredValidator(array('message' => _t('ERROR_SUBTRANSACTIONS_NEEDED'))));
    }

    return parent::validate();
  }

  /**
   * Saves form.
   *
   * @return Transaction
   */
  public function save()
  {
    // Do not update updated_at field after transaction is already deemed old.
    if ($this->is_old)
      $this->object->keepUpdateDateUnchanged();

    $object = parent::save();

    // Mark transaction as future if it is, well, in the future.
    // Do not mark subtransactions or counter transaction.  The user gets notified about overdue future transactions.
    if ($this->object->getCreatedAt('U') > time())
      $this->object->setIsProjected(true);
    else
      $this->object->setIsProjected(false);

    // Sync some subtransactions fields
    foreach ($object->getSubTransactionss() as $subtransaction)
    {
      $subtransaction->setCreatedAt($object->getCreatedAt());
      $subtransaction->setAccountId($object->getAccountId());
      $subtransaction->setCounterPartyId($object->getCounterPartyId());
      $subtransaction->setUser($object->getUser());
    }

    // TransactionTypeInput::save() also does this, but will not be called if that field is readonly
    if ($object->getCounterTransaction())
    {
      $object->getCounterTransaction()->setTitle($object->getTitle());
      $object->getCounterTransaction()->keepUpdateDateUnchanged();
    }
    return $object;
  }
}
