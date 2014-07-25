<?php

namespace Mockingbird\Input;

use Anthem\Forms\Input\SelectInput;
use Silex\Application;
use Mockingbird\Model\Account;

/**
 * An input field for account selection.
 */
class AccountInput extends SelectInput
{
  /**
   * The constructor.
   *
   * @param Application $app
   * @param array       $options
   */
  public function __construct(Application $app, array $options = array())
  {
    $options['values'] = array();
    $options['option_attrs'] = array();
    $credits = array();
    $debits = array();
    $accounts = $app['mockingbird.model.account']->getAllAccounts(null, $options['user']);
    /** @var Account[] $accounts */
    foreach ($accounts as $account)
    {
      if ($account->getIsdebt() && $account->getIsCredit())
      {
        if (!empty($options['no_credit_accounts'])) continue;
        $credits[$account->getId()] = $account->getTitle();
      }
      elseif ($account->getIsdebt() && !$account->getIsCredit())
      {
        if (!empty($options['no_debit_accounts'])) continue;
        $debits[$account->getId()] = $account->getTitle();
      }
      else
        $options['values'][$account->getId()] = $account->getTitle();
      $format = explode('#', $account->getCurrency()->getFormat());
      $options['option_attrs'][$account->getId()] =
          'data-currency="' . htmlspecialchars($account->getCurrency()->getTitle()) . '" ' .
          'data-rate="' . $account->getCurrency()->getRateToPrimary() . '" ' .
          'data-format-pre="' . htmlspecialchars($format[0]) . '" ' .
          'data-format-post="' . htmlspecialchars($format[1]) . '"';
    }
    if (count($credits))
      $options['values'][_t('CREDIT_ACCOUNTS')] = $credits;
    if (count($debits))
      $options['values'][_t('DEBIT_ACCOUNTS')] = $debits;

    parent::__construct($app, $options);
  }
}