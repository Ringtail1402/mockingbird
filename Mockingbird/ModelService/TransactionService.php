<?php

namespace Mockingbird\ModelService;

use Silex\Application;
use Anthem\Propel\ModelService\PropelModelService;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\TransactionQuery;

/**
 * Model service for Transaction model.
 */
class TransactionService extends PropelModelService
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
   * Searches transaction titles.
   *
   * @param string $q
   * @return string[]
   */
  public function searchTitles($q)
  {
    return $this->createQuery()
                ->_if(!$this->app['auth']->hasPolicies('mockingbird.alldata.ro'))
                  ->filterByUser($this->app['auth']->getUser())
                ->_endif()
                ->filterByTitle('%' . $q . '%')
                ->groupByTitle()
                ->select('Title')
                ->find()
                ->toArray();
  }


  /**
   * Calculates spendings and incomes over some units of time.
   *
   * @param \DateTime $year
   * @param \DateTime $month
   * @param string    $unit Y, m or d.
   * @return array
   */
  public function getTransactionsOverUnitsOfTime($from_date, $to_date, $unit)
  {
    // Query for transactions within the specified period, excluding:
    // * transactions on debt accounts
    // * transactions between account, if the target account is not a debt account
    $transactions = $this->createQuery()
                         ->filterByUser($this->app['auth']->getUser())
                         ->filterByCreatedAt(array('min' => $from_date, 'max' => $to_date))
                         ->useAccountQuery()
                           ->filterByIsdebt(false)
                         ->innerJoinCurrency()
                         ->endUse()
                         ->filterByTargetAccountId(null)  // Exclude transfers, except ones to debt accounts
                         ->_or()
                         ->useTargetAccountQuery('ta')
                           ->filterByIsdebt(true)
                         ->endUse()
                         ->find();

    // Sum transactions over days
    $units = array();
    foreach ($transactions as $transaction)
    {
      $_unit = (int)$transaction->getCreatedAt($unit);
      if (!isset($units[$_unit])) $units[$_unit] = array('+' => 0.0, '-' => 0.0);

      $amount = $transaction->getAmount();
      $currency = $transaction->getAccount()->getCurrency();
      $amount *= $currency->getRateToPrimary();
      $amount /= $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary();

      if ($amount > 0)
        $units[$_unit]['+'] += $amount;
      else
        $units[$_unit]['-'] -= $amount;
    }

    return $units;
  }
  /**
   * Calculates spendings and incomes over each day within a month.
   *
   * @param integer $year
   * @param integer $month
   * @return array
   */
  public function getTransactionsOverDaysInMonth($year, $month)
  {
    $from_date = new \DateTime("{$year}-{$month}-01 00:00:00");
    $to_date = clone $from_date;
    $to_date->add(new \DateInterval('P1M'))->sub(new \DateInterval('PT1S'));

    return $this->getTransactionsOverUnitsOfTime($from_date, $to_date, 'd');
  }

  /**
   * Calculates spendings and incomes over each month within a year.
   *
   * @param integer $year
   * @return array
   */
  public function getTransactionsOverMonthsInYear($year)
  {
    $from_date = new \DateTime("{$year}-01-01 00:00:00");
    $to_date = clone $from_date;
    $to_date->add(new \DateInterval('P1Y'))->sub(new \DateInterval('PT1S'));

    return $this->getTransactionsOverUnitsOfTime($from_date, $to_date, 'm');
  }

  /**
   * Calculates spendings and incomes over each year.
   *
   * @return array
   */
  public function getTransactionsOverYears()
  {
    $first_date = strtotime($this->getFirstTransactionDate());
    $last_date  = strtotime($this->getLastTransactionDate());
    $from_date  = new \DateTime(date('Y', $first_date) . '-01-01 00:00:00');
    $to_date    = new \DateTime(date('Y', $last_date)  . '-12-31 23:59:59');

    return $this->getTransactionsOverUnitsOfTime($from_date, $to_date, 'Y');
  }

  /**
   * Calculates spendings and incomes over some units of time.
   *
   * @param \DateTime $year
   * @param \DateTime $month
   * @param string    $unit               Y, m or d.
   * @param integer   $limit_big_amounts  Calculate averages only for transactions below this amount.
   * @return array
   */
  public function getTotalsOverUnitsOfTime($from_date, $to_date, $unit, $limit_big_amounts = null)
  {
    // Query for transactions within the specified period, excluding:
    // * transactions on debt accounts
    // * transactions between account, if the target account is not a debt account
    $transactions = $this->createQuery()
                         ->filterByUser($this->app['auth']->getUser())
                         ->filterByCreatedAt(array('min' => $from_date, 'max' => $to_date))
                         ->useAccountQuery()
                         ->filterByIsdebt(false)
                           ->innerJoinCurrency()
                         ->endUse()
                         ->filterByTargetAccountId(null)  // Exclude transfers, except ones to debt accounts
                         ->_or()
                         ->useTargetAccountQuery('ta')
                           ->filterByIsdebt(true)
                         ->endUse()
                         ->find();

    $income = 0;
    $spending = 0;
    $units = array();
    // Prepare units array
    $firsttrans = strtotime(date('Y-m-d', strtotime($this->getFirstTransactionDate())));
    switch ($unit)
    {
      case 'd':
        $year  = $from_date->format('Y');
        $month = $from_date->format('m');
        for ($i = 1; $i <= 31; $i++)
        {
          $time = mktime(0, 0, 0, $month, $i, $year);
          if (date('m', $time) != $month) continue;  // overflows into next month
          if ($time < $firsttrans) continue;  // before first transaction
          if ($time > time()) continue;  // in future
          $units[$i] = 0;
        }
        break;

      case 'm':
        $year = $from_date->format('Y');
        for ($i = 1; $i <= 12; $i++)
        {
          $time = mktime(0, 0, 0, $i, 1, $year);
          if ($time < $firsttrans) continue;  // before first transaction
          if ($time > time()) continue;  // in future
          $units[$i] = 0;
        }
        break;

      case 'Y':
        $from_year = $from_date->format('Y');
        $to_year = $to_date->format('Y');
        for ($i = $from_year; $i <= $to_year; $i++) $units[$i] = 0;
        break;

      default:
        throw new \InvalidArgumentException('Unknown unit of time: ' . $unit);
    }

    // Sum transactions over units
    foreach ($transactions as $transaction)
    {
      $amount = $transaction->getAmount();
      $currency = $transaction->getAccount()->getCurrency();
      $amount *= $currency->getRateToPrimary();
      $amount /= $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary();

      if ($amount > 0)
        $income += $amount;
      else
        $spending += $amount;

      // Averages
      $_unit = (int)$transaction->getCreatedAt($unit);
      if (isset($units[$_unit]) && (!$limit_big_amounts || abs($amount) < $limit_big_amounts))
        $units[$_unit] += $amount;
    }
    if (count($units))
      $avg = array_sum($units) / count($units);
    else
      $avg = 0;

    return array('income' => $income, 'spending' => $spending, 'avg' => $avg);
  }

  /**
   * Calculates total income, spending and average spending per day over days in month.
   *
   * @param integer $year
   * @param integer $month
   * @param integer $limit_big_amounts
   * @return array
   */
  public function getTotalsOverDaysInMonth($year, $month, $limit_big_amounts = null)
  {
    $from_date = new \DateTime("{$year}-{$month}-01 00:00:00");
    $to_date = clone $from_date;
    $to_date->add(new \DateInterval('P1M'))->sub(new \DateInterval('PT1S'));

    return $this->getTotalsOverUnitsOfTime($from_date, $to_date, 'd', $limit_big_amounts);
  }


  /**
   * Calculates total income, spending and average spending per month over months in year.
   *
   * @param integer $year
   * @return array
   */
  public function getTotalsOverMonthsInYear($year)
  {
    $from_date = new \DateTime("{$year}-01-01 00:00:00");
    $to_date = clone $from_date;
    $to_date->add(new \DateInterval('P1Y'))->sub(new \DateInterval('PT1S'));

    return $this->getTotalsOverUnitsOfTime($from_date, $to_date, 'm');
  }

  /**
   * Calculates total income, spending and average spending per year over years.
   *
   * @return array
   */
  public function getTotalsOverYears()
  {
    $first_date = strtotime($this->getFirstTransactionDate());
    $last_date  = strtotime($this->getLastTransactionDate());
    $from_date  = new \DateTime(date('Y', $first_date) . '-01-01 00:00:00');
    $to_date    = new \DateTime(date('Y', $last_date)  . '-12-31 23:59:59');

    return $this->getTotalsOverUnitsOfTime($from_date, $to_date, 'Y');
  }

  /**
   * Calculates sum of incomes or expenses over a period of time.
   *
   * @param boolean $income      true = incomes, false = expenses
   * @param integer $from        Timestamp or null.
   * @param integer $to          Timestamp or null.
   * @return float
   */
  public function getSumByPeriod($income, $from, $to)
  {
    $sum = $this->createQuery()
                ->filterByUser($this->app['auth']->getUser())
                ->_if($income)
                  ->filterByAmount(0, \Criteria::GREATER_THAN)
                ->_else()
                  ->filterByAmount(0, \Criteria::LESS_THAN)
                ->_endif()
                ->_if($from && $to)
                  ->filterByCreatedAt(array('min' => $from, 'max' => $to))
                ->_endif()
                ->useAccountQuery()
                  ->innerJoinCurrency()
                ->endUse()
                ->filterByTargetAccountId(null)  // Exclude transfers, except ones to debt accounts
                ->_or()
                ->useTargetAccountQuery('ta')
                  ->filterByIsdebt(true)
                ->endUse()
                ->withColumn('ABS(SUM(transactions.AMOUNT * currencies.RATE_TO_PRIMARY))', 'sum')
                ->select('sum')
                ->findOne();
    return $sum / $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary();
  }

  /**
   * Calculates sum by accounts or categories over a period of time.
   *
   * @param boolean $income      true = incomes, false = expenses
   * @param string  $groupcolumn Column to group by (category, account).
   * @param integer $from        Timestamp or null.
   * @param integer $to          Timestamp or null.
   * @return array               Category/account title => sum.
   */
  public function getGroupedSumsByPeriod($income, $groupcolumn, $from, $to)
  {
    $query = TransactionQuery::create('t')
                  ->filterByUser($this->app['auth']->getUser())
                  ->_if($income)
                    ->filterByAmount(0, \Criteria::GREATER_THAN)
                  ->_else()
                    ->filterByAmount(0, \Criteria::LESS_THAN)
                  ->_endif()
                  ->_if($from && $to)
                    ->filterByCreatedAt(array('min' => $from, 'max' => $to))
                  ->_endif()
                  ->useAccountQuery()
                    ->innerJoinCurrency()
                  ->endUse()
                  ->filterByTargetAccountId(null)  // Exclude transfers, except ones to debt accounts
                  ->_or()
                  ->useTargetAccountQuery('ta')
                    ->filterByIsdebt(true)
                  ->endUse()
                  ->withColumn('ABS(SUM(transactions.AMOUNT * currencies.RATE_TO_PRIMARY))', 'sum')
                  ->orderBy('sum', \Criteria::DESC);

    switch ($groupcolumn)
    {
      case 'category':
        $query->leftJoinCategory()
              ->groupByCategoryId()
              ->select(array('Category.Title', 'sum', 'Category.Color'));
        break;

      case 'account':
        $query->groupByAccountId()
              ->select(array('Account.Title', 'sum', 'Account.Color'));
        break;

      default:
        throw new \LogicException('Unknown group by column: ' . $groupcolumn . '.');
    }

    $result = array();
    foreach ($query->find() as $row)
    {
      $row = array_values($row);
      $name = $row[1];
      $sum = $row[0] / $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary();
      if (!$name && $groupcolumn == 'category') $name = _t('CHART_NO_CATEGORY');
      $result[$name] = array('sum' => $sum, 'color' => $row[2] ? $row[2] : '#000000');
    }

    return $result;
  }

  /**
   * Returns all existing values for $groupcolumn within a period.
   *
   * @param boolean $income      true = incomes, false = expenses
   * @param string  $groupcolumn Column to group by (category, account).
   * @param integer $from        Timestamp or null.
   * @param integer $to          Timestamp or null.
   * @return array               id => array(title, color)
   */
  public function getGroupColumnValues($income, $groupcolumn, $from, $to)
  {
    $query = TransactionQuery::create('t')
                  ->filterByUser($this->app['auth']->getUser())
                  ->_if($income)
                    ->filterByAmount(0, \Criteria::GREATER_THAN)
                  ->_else()
                    ->filterByAmount(0, \Criteria::LESS_THAN)
                  ->_endif()
                  ->_if($from && $to)
                    ->filterByCreatedAt(array('min' => $from, 'max' => $to))
                  ->_endif()
                  ->filterByTargetAccountId(null)  // Exclude transfers, except ones to debt accounts
                  ->_or()
                  ->useTargetAccountQuery('ta')
                    ->filterByIsdebt(true)
                  ->endUse();

    switch ($groupcolumn)
    {
      case 'category':
        $query->useCategoryQuery('c', \Criteria::LEFT_JOIN)
                ->orderByTitle(\Criteria::DESC)
              ->endUse()
              ->groupByCategoryId()
              ->select(array('c.Id', 'c.Title', 'c.Color'));
        break;

      case 'account':
        $query->useAccountQuery()
                ->orderByTitle(\Criteria::DESC)
              ->endUse()
              ->groupByAccountId()
              ->select(array('Account.Id', 'Account.Title', 'Account.Color'));
        break;

      default:
        throw new \LogicException('Unknown group by column: ' . $groupcolumn . '.');
    }

    $result = array();
    foreach ($query->find() as $row)
    {
      $row = array_values($row);
      $name = $row[1];
      if (!$name && $groupcolumn == 'category') $name = _t('CHART_NO_CATEGORY');
      $result[$row[0]] = array('title' => $name, 'color' => $row[2] ? $row[2] : '#000000');
    }

    return $result;
  }

  /**
   * Returns date/time of the very first transaction registered.
   *
   * @return string
   */
  public function getFirstTransactionDate()
  {
    return $this->createQuery()
                ->filterByUser($this->app['auth']->getUser())
                ->firstCreatedFirst()
                ->select('CreatedAt')
                ->findOne();
  }

  /**
   * Returns date/time of the very last transaction registered.
   *
   * @return string
   */
  public function getLastTransactionDate()
  {
    return $this->createQuery()
                ->filterByUser($this->app['auth']->getUser())
                ->lastCreatedFirst()
                ->select('CreatedAt')
                ->findOne();
  }

  /**
   * Counts transactions.
   *
   * @return integer
   */
  public function countTransactions()
  {
    return $this->createQuery()
                ->_if(!$this->app['auth']->hasPolicies('mockingbird.alldata.ro'))
                  ->filterByUser($this->app['auth']->getUser())
                ->_endif()
                ->count();
  }

  /**
   * Counts transactions in current month, for current user, excluding subtransactions.
   *
   * @return integer
   */
  public function countTransactionsInMonth()
  {
    return $this->createQuery()
                ->filterByUser($this->app['auth']->getUser())
                ->filterByParentTransactionId(null)
                ->add('YEAR(transactions.CREATED_AT) = ?', date('Y'), \Criteria::RAW)
                ->add('MONTH(transactions.CREATED_AT) = ?', date('m'), \Criteria::RAW)
                ->count();
  }

  /**
   * Checks that there are any overdue transactions.  Creates a notification if there are.
   * Removes it if there aren't.  Runs for all users.
   *
   * @return void
   */
  public function updateAllOverdueTransactionsNotice()
  {
    // SELECT COUNT(user_id) AS num, user_id FROM transactions WHERE isprojected = 1 AND created_at < NOW() GROUP BY user_id
    $overdue = TransactionQuery::create()
                               ->joinUser()
                               ->filterByIsprojected(true)
                               ->filterByCreatedAt(time(), \Criteria::LESS_EQUAL)
                               ->groupByUserId()
                               ->with('User')
                               ->withColumn('COUNT(user_id)', 'num')
                               ->find();
    foreach ($overdue as $_overdue)
    {
      $this->app['notify']->addPersistent(_t('TRANSACTION_OVERDUE_MESSAGE', $this->app['Core']['web_root'], date('Y-m-d%20H:i:s'), $_overdue->getVirtualColumn('num')),
        '', 'mockingbird.overdue_transactions', false, $_overdue->getUser());
    }
  }

  /**
   * Saves a transaction.  Sets created_at field manually.  For a newly-created transaction this can be done
   * by timestampable behavior, but if user manually edits an already-saved transaction and clears created_at field,
   * it could end up blank.
   *
   * @param \Mockingbird\Model\Transaction $object
   * @return void
   */
  public function save($object)
  {
    if (!$object->getCreatedAt()) $object->setCreatedAt(time());
    parent::save($object);
  }

  /**
   * Returns underlying model class.
   *
   * @return string
   */
  public function getModelClass()
  {
    return 'Mockingbird\\Model\\Transaction';
  }
}
