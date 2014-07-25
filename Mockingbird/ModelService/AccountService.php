<?php

namespace Mockingbird\ModelService;

use Silex\Application;
use Anthem\Propel\ModelService\PropelModelService;
use Mockingbird\Model\Account;
use Mockingbird\Model\TransactionQuery;
use Mockingbird\Model\TransactionPeer;

/**
 * Model service for Account model.
 */
class AccountService extends PropelModelService
{
  /**
   * @var Application
   */
  protected $app;

  public function __construct(Application $app)
  {
    $this->app = $app;
  }

  /**
   * Returns all normal (non-debt) accounts.
   *
   * @param string|integer  $date
   * @return Account[]
   */
  public function getNormalAccounts($date)
  {
    return $this->getAccounts($date, false, false);
  }

  /**
   * Returns all credit accounts.
   *
   * @param string|integer  $date
   * @return Account[]
   */
  public function getCreditAccounts($date)
  {
    return $this->getAccounts($date, false, true, true);
  }

  /**
   * Returns all active debit accounts.
   *
   * @param string|integer  $date
   * @return Account[]
   */
  public function getDebitAccounts($date)
  {
    return $this->getAccounts($date, false, true, false);
  }

  /**
   * Returns all non-closed accounts.
   *
   * @param string|integer  $date
   * @return Account[]
   */
  public function getAllAccounts($date = null, $user = null)
  {
    return $this->getAccounts($date, false, null, null, $user);
  }

  /**
   * Returns accounts.
   *
   * @param string|integer  $date
   * @param boolean $isclosed
   * @param boolean $isdebt
   * @param boolean $iscredit
   * @return Account[]
   */
  protected function getAccounts($date, $isclosed = false, $isdebt = null, $iscredit = null, $user = null)
  {
    if ($date)
    {
      if (is_string($date)) $date = strtotime($date);
      $date = date('Y-m-d', $date) . ' 23:59:59';
    }

    return $this->createQuery()
                ->filterByUser($user ? $user : $this->app['auth']->getUser())
                ->_if($date)
                  ->filterByCreatedAt($date, \Criteria::LESS_EQUAL)
                ->_endif()
                ->filterByIsclosed($isclosed)
                ->_if($isdebt !== null)
                  ->filterByIsdebt($isdebt)
                ->_endif()
                ->_if($iscredit !== null)
                  ->filterByIscredit($iscredit)
                ->_endif()
                ->orderByIsdebt()
                ->orderByIscredit()
                ->firstCreatedFirst()
                ->joinCurrency()
                ->find();
  }

  /**
   * Counts accounts.
   *
   * @param boolean $withclosed
   * @param boolean $withdebt
   * @return integer
   */
  public function countAccounts($withclosed = false, $withdebt = true)
  {
    return $this->createQuery()
                ->_if(!$this->app['auth']->hasPolicies('mockingbird.alldata.ro'))
                  ->filterByUser($this->app['auth']->getUser())
                ->_endif()
                ->_if(!$withclosed)
                  ->filterByIsclosed(false)
                ->_endif()
                ->_if(!$withdebt)
                  ->filterByIsdebt(false)
                ->_endif()
                ->count();
  }

  /**
   * Gets accounts which have been active in the specified period.  This is determined as:
   * - either balance of the account at the beginning of the period being non-zero;
   * - or any transactions existing within this period.
   *
   * @param integer $from
   * @param integer $to
   * @return Account[]
   */
  public function getActiveAccountsInPeriod($from, $to)
  {
    $accounts1 = $this->createQuery()
                      ->filterByUser($this->app['auth']->getUser())
                      ->filterByIsdebt(false)
                      ->leftJoinTransactions()
                      ->addJoinCondition('Transactions', 'Transactions.CreatedAt < ?', $from)
                      ->withColumn('accounts.INITIAL_AMOUNT + COALESCE(SUM(transactions.AMOUNT), 0)', 'sum')
                      ->having('sum <> 0')
                      ->groupById()
                      ->find();
    $ids1 = array();
    foreach ($accounts1 as $account) $ids1[] = $account->getId();

    $accounts2 = $this->createQuery()
                      ->filterByUser($this->app['auth']->getUser())
                      ->filterByIsdebt(false)
                      ->useTransactionsQuery(null, \Criteria::LEFT_JOIN)
                        ->filterById(null, \Criteria::ISNOTNULL)
                      ->endUse()
                      ->addJoinCondition('Transactions', 'Transactions.CreatedAt >= ?', $from)
                      ->addJoinCondition('Transactions', 'Transactions.CreatedAt <= ?', $to)
                      ->groupById()
                      ->find();

    $ids2 = array();
    foreach ($accounts2 as $account) $ids2[] = $account->getId();

    return $this->createQuery()
                ->filterByPrimaryKeys($ids1)
                ->_or()
                ->filterByPrimaryKeys($ids2)
                ->firstCreatedFirst()
                ->innerJoinCurrency()
                ->with('Currency')
                ->find();
  }

  /**
   * Returns current balance of an account, or sum of balances of all accounts,
   * for the specified point of time.  The sum is always returned in primary
   * currency.
   *
   * @param  Account|Account[] $account
   * @param  integer           $time
   * @return float
   */
  public function balance($account, $time = null)
  {
    if (!$time) $time = time();
    if (is_string($time)) $time = strtotime($time);
    $time = date('Y-m-d', $time) . ' 23:59:59';

    if ($account instanceof \PropelObjectCollection)
    {
      $sum = 0;
      foreach ($account as $_account)
      {
        $balance = $this->balance($_account, $time);
        $currency = $_account->getCurrency();
        $balance *= $currency->getRateToPrimary();
        $balance /= $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary();
        $sum += $balance;
      }
      return $sum;
    }

    // Account balance is null, before account is created
    if ($time < $account->getCreatedAt('U')) return null;

    $balance = TransactionQuery::create()
                               ->filterByAccount($account)
                               ->filterByIsprojected(0)
                               ->filterByCreatedAt($time, \Criteria::LESS_EQUAL)
                               ->withColumn(sprintf('SUM(%s)', TransactionPeer::AMOUNT), 'sum')
                               ->select(array('sum'))
                               ->findOne();
    return $account->getInitialAmount() + $balance;
  }

  /**
   * Returns balances of several accounts as an array.
   *
   * @param  Account[] $account
   * @param  integer   $time
   * @return float
   */
  public function balanceSeveral($accounts, $time = null)
  {
    if (!$time) $time = time();
    if (is_string($time)) $time = strtotime($time);

    // Sum transaction amounts by specified time
    $data = TransactionQuery::create()
                            ->filterByAccount(new \PropelObjectCollection($accounts))
                            ->filterByIsprojected(0)
                            ->filterByCreatedAt($time, \Criteria::LESS_EQUAL)
                            ->innerJoinAccount()
                            ->withColumn('accounts.INITIAL_AMOUNT + COALESCE(SUM(transactions.AMOUNT), 0)', 'sum')
                            ->groupByAccountId()
                            ->select(array('AccountId', 'sum'))
                            ->find();
    $result = array();
    foreach ($data as $row) $result[$row['AccountId']] = $row['sum'];

    // If the query does not return a row for some account, there are no transactions on it.
    // Default either to initial amount, or to null if $time is earlier than account creation timestamp
    foreach ($accounts as $account)
    {
      if (!isset($result[$account->getId()]))
      {
        if ($account->getCreatedAt('U') > $time)
          $result[$account->getId()] = null;
        else
          $result[$account->getId()] = $account->getInitialAmount();
      }
    }

    return $result;
  }

  /**
   * Returns underlying model class.
   *
   * @return string
   */
  public function getModelClass()
  {
    return 'Mockingbird\\Model\\Account';
  }
}
