<?php

namespace Mockingbird\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mockingbird dashboard controller.
 */
class DashboardController
{
  /**
   * @var \Silex\Application
   */
  protected $app;

  /**
   * The constructor.
   *
   * @param \Silex\Application $app
   */
  public function __construct(Application $app)
  {
    $this->app = $app;

    // Require authorization
    if (!empty($app['Auth']['enable'])) $app['auth']->checkAuthorization();
  }

  /**
   * Shows dashboard page.
   *
   * @param  Request $request
   * @return string
   */
  public function indexAction(Request $request)
  {
    // Show admin dashboard to admins
    if ($this->app['auth']->hasPolicies('mockingbird.alldata.ro'))
      return $this->app['core.view']->render('Mockingbird:admin_dashboard.php');

    return $this->app['core.view']->render('Mockingbird:dashboard.php');
  }

  /**
   * Returns accounts and sums information for the specified moment in time.
   *
   * @param  Request $request
   * @param  string  $datetime
   * @return string
   */
  public function accountsAction(Request $request, $datetime)
  {
    if (!$datetime || $datetime == 'today')
      $datetime = time();
    else
      $datetime = strtotime($datetime);
    $normal_accounts = $this->app['mockingbird.model.account']->getNormalAccounts($datetime);
    $credit_accounts = $this->app['mockingbird.model.account']->getCreditAccounts($datetime);
    $debit_accounts  = $this->app['mockingbird.model.account']->getDebitAccounts($datetime);
    $sum             = $this->app['mockingbird.model.account']->balance($normal_accounts, $datetime);
    $sum_credits     = $this->app['mockingbird.model.account']->balance($credit_accounts, $datetime);
    $sum_debits      = $this->app['mockingbird.model.account']->balance($debit_accounts, $datetime);
    $sum_total       = $sum + $sum_credits + $sum_debits;
    $balances        = array();
    foreach (array($normal_accounts, $credit_accounts, $debit_accounts) as $_accounts)
      foreach ($_accounts as $_account)
        $balances[$_account->getId()] = $this->app['mockingbird.model.account']->balance($_account, $datetime);

    return $this->app['core.view']->render('Mockingbird:dashboard_accounts.php', array(
      'date'            => strftime('%x', $datetime),
      'normal_accounts' => $normal_accounts,
      'credit_accounts' => $credit_accounts,
      'debit_accounts'  => $debit_accounts,
      'balances'        => $balances,
      'sum'             => $sum,
      'sum_credits'     => $sum_credits,
      'sum_debits'      => $sum_debits,
      'sum_total'       => $sum_total,
    ));
  }

  /**
   * Returns a calendar with spendings and incomes for each day of specified month.
   *
   * @param Request $request
   * @param integer $year
   * @param integer $month
   * @return string
   */
  public function monthCalendarAction(Request $request, $year, $month)
  {
    $day_sums = $this->app['mockingbird.model.transaction']->getTransactionsOverDaysInMonth($year, $month);
    $totals = $this->app['mockingbird.model.transaction']->getTotalsOverDaysInMonth($year, $month, $this->app['settings']->get('mockingbird.day_average_limit'));
    $days = array();
    for ($day = 1; $day <= 31; $day++)
    {
      $time = mktime(0, 0, 0, $month, $day, $year);
      if (date('Y-m-d', $time) == date('Y-m-d'))
        $class = 'today';
      elseif ($time < time())
        $class = 'past';
      else
        $class = 'future';
      $days[$day] = array(
        'content' => $this->app['core.view']->render('Mockingbird:dashboard_calendar_month_day.php', array(
          'year'     => $year,
          'month'    => $month,
          'day'      => $day,
          'income'   => empty($day_sums[$day]['+']) ? 0 : $day_sums[$day]['+'],
          'spending' => empty($day_sums[$day]['-']) ? 0 : $day_sums[$day]['-'],
        )),
        'class' => $class,
      );
    }

    return $this->app['core.view']->render('Mockingbird:dashboard_calendar_month.php', array(
      'year' => $year,
      'month' => $month,
      'days' => $days,
      'totals' => $totals,
    ));
  }

  /**
   * Returns a calendar with spendings and incomes for each month of specified year.
   *
   * @param Request $request
   * @param integer $year
   * @return string
   */
  public function yearCalendarAction(Request $request, $year)
  {
    $month_sums = $this->app['mockingbird.model.transaction']->getTransactionsOverMonthsInYear($year);
    $totals = $this->app['mockingbird.model.transaction']->getTotalsOverMonthsInYear($year);
    $months = array();
    for ($month = 1; $month <= 12; $month++)
    {
      $time = mktime(0, 0, 0, $month, 1, $year);
      if (date('Y-m', $time) == date('Y-m'))
        $class = 'today';
      elseif ($time < time())
        $class = 'past';
      else
        $class = 'future';
      $months[$month] = array(
        'content' => $this->app['core.view']->render('Mockingbird:dashboard_calendar_year_month.php', array(
          'year'     => $year,
          'month'    => $month,
          'income'   => empty($month_sums[$month]['+']) ? 0 : $month_sums[$month]['+'],
          'spending' => empty($month_sums[$month]['-']) ? 0 : $month_sums[$month]['-'],
        )),
        'class' => $class,
      );
    }

    return $this->app['core.view']->render('Mockingbird:dashboard_calendar_year.php', array(
      'year' => $year,
      'months' => $months,
      'totals' => $totals,
    ));
  }

  /**
   * Returns a calendar with spendings and incomes for each year.
   *
   * @param Request $request
   * @return string
   */
  public function allCalendarAction(Request $request)
  {
    $year_sums = $this->app['mockingbird.model.transaction']->getTransactionsOverYears();
    $totals = $this->app['mockingbird.model.transaction']->getTotalsOverYears();
    $years = array();
    $first_date = strtotime($this->app['mockingbird.model.transaction']->getFirstTransactionDate());
    $last_date  = strtotime($this->app['mockingbird.model.transaction']->getLastTransactionDate());
    $from_year  = date('Y', $first_date);
    $to_year    = date('Y', $last_date);
    for ($year = $from_year; $year <= $to_year; $year++)
    {
      $time = mktime(0, 0, 0, 1, 1, $year);
      if (date('Y', $time) == date('Y'))
        $class = 'today';
      elseif ($time < time())
        $class = 'past';
      else
        $class = 'future';
      $years[$year] = array(
        'content' => $this->app['core.view']->render('Mockingbird:dashboard_calendar_all_year.php', array(
          'year'     => $year,
          'income'   => empty($year_sums[$year]['+']) ? 0 : $year_sums[$year]['+'],
          'spending' => empty($year_sums[$year]['-']) ? 0 : $year_sums[$year]['-'],
        )),
        'class' => $class,
      );
    }

    return $this->app['core.view']->render('Mockingbird:dashboard_calendar_all.php', array(
      'years' => $years,
      'totals' => $totals,
    ));
  }
}