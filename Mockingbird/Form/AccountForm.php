<?php

namespace Mockingbird\Form;

use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\CheckboxInput;
use Anthem\Forms\Input\ColorInput;
use Anthem\Auth\Input\UserInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\NumberValidator;
use Anthem\Forms\Validator\PrimaryKeyValidator;
use Mockingbird\Input\CurrencyDenominationInput;
use Mockingbird\Input\CurrencyInput;
use Mockingbird\Input\AccountTypeInput;
use Mockingbird\Model\Account;

/**
 * Account form.
 */
class AccountForm extends Form
{
  /**
   * @var boolean  Is the form in semi-read-only mode due to some transaction already existing in account.
   */
  protected $is_old;

  /**
   * Creates a form.
   *
   * @param \Silex\Application $app
   * @param Account            $object
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

    $this->is_old = $is_old = !$object->isNew() && $object->countTransactionss();
    $object->setVirtualColumn('balance', $app['mockingbird.model.account']->balance($object));

    $options = array(
      'label'  => function () use ($object, $is_old, $app) { return ($object->getTitle() ? htmlspecialchars($object->getTitle()) : _t('ACCOUNT_NEW')) .
        ($is_old ? $app['core.view']->render('Mockingbird:form_alert.php', array('alert' => _t('WARNING_OLD_ACCOUNT'))) : '');; },
      'fields' => array(
        'title'     => new StringInput($app, array(
          'label'        => _t('TITLE'),
          'help'         => _t('ACCOUNT_TITLE_HELP'),
          'validator'    => new RequiredValidator(),
        )),
        'color'     => new ColorInput($app, array(
          'label'        => _t('COLOR'),
          'help'         => _t('ACCOUNT_COLOR_HELP'),
        )),
        'type'      => new AccountTypeInput($app, array(
          'label'        => _t('TYPE'),
          'help'         => _t('ACCOUNT_TYPE_HELP'),
          'validator'    => new RequiredValidator(),
          'readonly'     => $this->is_old,
        )),
        'currency_id' => new CurrencyDenominationInput($app, array(
          'label'        => _t('CURRENCY'),
          'help'         => _t('ACCOUNT_CURRENCY_HELP'),
          'validator'    => new PrimaryKeyValidator(array('model' => 'Mockingbird\\Model\\Currency')),
          'readonly'     => $this->is_old,
        )),
        'initial_amount' => new CurrencyInput($app, array(
          'label'        => _t('BALANCE'),
          'help'         => _t('ACCOUNT_INITIAL_AMOUNT_HELP'),
          'validator'    => array(new NumberValidator()),
          'currency'     => $object->getCurrency() ? $object->getCurrency() :
              $app['mockingbird.model.currency']->getDefaultCurrency(),
          'readonly'     => $this->is_old,
          'absolute'     => $object->getIsdebt(),
          'allow_zero'   => true,
        )),
        'balance' => new CurrencyInput($app, array(
          'label'        => _t('BALANCE'),
          'help'         => _t('ACCOUNT_BALANCE_HELP'),
          'currency'     => $object->getCurrency() ? $object->getCurrency() :
              $app['mockingbird.model.currency']->getDefaultCurrency(),
          'readonly'     => true,
        )),
        'isclosed' => new CheckboxInput($app, array(
          'label'        => _t('IS_CLOSED'),
          'help'         => _t('ACCOUNT_ISCLOSED_HELP'),
          'readonly'     => !$object->isNew() && $object->getVirtualColumn('balance') != 0,
        ))
      ),
    );
    if ($object->isNew())
    {
      unset($options['fields']['balance']);
      unset($options['fields']['isclosed']);
    }
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

  /**
   * Saves form.
   *
   * @return Account
   */
  public function save()
  {
    /** @var \Mockingbird\Model\Account $object */
    $object = parent::save();

    // Normalize amount sign
    if ($object->getIsdebt())
    {
      if ($object->getIscredit())
        $object->setInitialAmount(sprintf('%.2F', -abs($object->getInitialAmount())));
      else
        $object->setInitialAmount(sprintf('%.2F', abs($object->getInitialAmount())));
    }

    return $object;
  }
}
