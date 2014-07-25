<?php

namespace Mockingbird\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mockingbird charts controller.
 */
class ChartController
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
   * Shows main chart page.
   *
   * @param  Request $request
   * @return string
   */
  public function indexAction(Request $request)
  {
    return $this->app['core.view']->render('Mockingbird:charts.php', array(
      'first_year' => date('Y', strtotime($this->app['mockingbird.model.transaction']->getFirstTransactionDate())),
      'last_year'  => date('Y'),
      'print'      => $request->get('print')
    ));
  }

  /**
   * Returns data for Google Chart visualization.
   *
   * @param  Request $request
   * @return string
   */
  public function dataAction(Request $request)
  {
    $type  = $request->get('type');
    $for   = $request->get('for');
    $by    = $request->get('by');
    $in    = $request->get('in');
    $year  = (int)$request->get('year');
    $month = (int)$request->get('month');
    $data  = array();

    switch ($type)
    {
      case 'pie':
        $data = $this->pieChartData($for, $by, $in, $year, $month);
        break;

      case 'time':
        $data = $this->timeChartData($for, $by, $in, $year, $month);
        break;
    }

    return new Response(json_encode($data), 200, array('Content-Type' => 'application/json'));
  }

  /**
   * Converts $in/$year/$month parameters to two Y-m-d H:i:s strings.
   *
   * @param string       $in
   * @param integer|null $year
   * @param integer|null $month
   * @return array
   */
  protected function getIntervalBoundaries($in, $year, $month)
  {
    $from = null;
    $to = null;
    if ($in == 'all')
    {
      $date = $this->app['mockingbird.model.transaction']->getFirstTransactionDate();
      $from = date('Y', strtotime($date)) . '-01-01 00:00:00';
      $to   = date('Y') . '-12-31 23:59:59';
    }
    if ($in == 'year')
    {
      $from = $year . '-01-01 00:00:00';
      $to   = $year . '-12-31 23:59:59';
    }
    if ($in == 'month')
    {
      $date = new \DateTime($year . '-' . sprintf('%02d', $month) . '-01 00:00:00');
      $from = $date->format('Y-m-d H:i:s');
      $date->add(new \DateInterval('P1M'));
      $date->sub(new \DateInterval('PT1S'));
      $to = $date->format('Y-m-d H:i:s');
    }

    return array($from, $to);
  }

  /**
   * Prepares data for a pie chart.
   *
   * @param string  $for income/expense
   * @param string  $by  account/category
   * @param string  $in  all/year/month
   * @param integer $year
   * @param integer $month
   * @return array
   */
  protected function pieChartData($for, $by, $in, $year, $month)
  {
    // Determine time interval boundaries
    list($from, $to) = $this->getIntervalBoundaries($in, $year, $month);

    // Handle balance separately
    if ($for == 'balance') return $this->pieChartBalanceData($to);

    // Calculate
    $result = $this->app['mockingbird.model.transaction']->getGroupedSumsByPeriod($for == 'income', $by, $from, $to);

    // Prepare DataTable array for Google Charts
    $data = array(
      'cols' => array(
        array('id' => 'row', 'label' => 'row', 'type' => 'string'),
        array('id' => 'sum', 'label' => 'sum', 'type' => 'number'),
      ),
      'rows' => array(),
      'colors' => array(),  // Values other than rows and cols will be used as options argument to chart.draw()
    );

    // Build DataTable array for Google Charts
    $currency = $this->app['mockingbird.model.currency']->getDefaultCurrency();
    $format = str_replace('#', '%.02f', $currency->getFormat());
    $total = 0;
    foreach ($result as $row => $values)
    {
      $data['rows'][] = array('c' => array(array('v' => $row), array('v' => (float)$values['sum'], 'f' => sprintf($format, $values['sum']))));
      $total += $values['sum'];
      $data['colors'][] = $values['color'];
    }

    // Extra row for deficit/proficit, if any
    $remainder = (float)$this->app['mockingbird.model.transaction']->getSumByPeriod($for != 'income', $from, $to);
    if ($remainder > $total)
    {
      $data['rows'][] = array('c' => array(array('v' => _t($for == 'income' ? 'CHART_DEFICIT' : 'CHART_PROFICIT')),
                                           array('v' => $remainder - $total, 'f' => sprintf($format, $remainder - $total))));
      $data['colors'][] = '#e0e0e0';
    }

    // Total amount is used as a title of chart
    $data['title'] = _t('TOTAL') . ' ' . sprintf($format, $total);

    return $data;
  }

  /**
   * Prepares data for a pie chart specifically with balances.
   *
   * @param  string|integer|DateTime $datetime
   * @return array
   */
  protected function pieChartBalanceData($datetime)
  {
    // Get accounts and total balance
    $accounts = $this->app['mockingbird.model.account']->getNormalAccounts($datetime);
    $sum      = $this->app['mockingbird.model.account']->balance($accounts, $datetime);
    foreach ($accounts as $_account)
      $balances[$_account->getId()] = $this->app['mockingbird.model.account']->balance($_account, $datetime);

    // Prepare DataTable array for Google Charts
    $currency = $this->app['mockingbird.model.currency']->getDefaultCurrency();
    $format = str_replace('#', '%.02f', $currency->getFormat());
    $data = array(
      'cols' => array(
        array('id' => 'row', 'label' => 'row', 'type' => 'string'),
        array('id' => 'sum', 'label' => 'sum', 'type' => 'number'),
      ),
      'rows' => array(),
      'colors' => array(),  // Values other than rows and cols will be used as options argument to chart.draw()
      'title' => _t('TOTAL') . ' ' . sprintf($format, $sum)
    );

    // Build DataTable array for Google Charts
    $debt = 0;
    foreach ($accounts as $_account)
    {
      $balance = $this->app['mockingbird.model.account']->balance($_account, $datetime);
      if ($balance < 0)
      {
        $debt += abs($balance * $_account->getCurrency()->getRateToPrimary()
                              / $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary());
        continue;
      }
      $_format = str_replace('#', '%.02f', $_account->getCurrency()->getFormat());
      $data['rows'][] = array('c' => array(array('v' => $_account->getTitle()),
                                           array('v' => $balance * $_account->getCurrency()->getRateToPrimary()
                                                                 / $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary(),
                                                 'f' => sprintf($_format, $balance))));
      $data['colors'][] = $_account->getColor();
    }

    // Extra row for debt, if any
    if ($debt)
    {
      $data['rows'][] = array('c' => array(array('v' => _t('CHART_DEBTS')),
                                           array('v' => $debt, 'f' => sprintf($format, $debt))));
      $data['colors'][] = '#e0e0e0';
    }

    return $data;
  }

  /**
   * Prepares data for a time chart.
   *
   * @param string  $for income/expense
   * @param string  $by  account/category
   * @param string  $in  all/year/month
   * @param integer $year
   * @param integer $month
   * @return array
   */
  protected function timeChartData($for, $by, $in, $year, $month)
  {
    // Handle balance separately
    if ($for == 'balance') return $this->timeChartBalanceData($in, $year, $month);

    // Determine time interval boundaries
    list($from, $to) = $this->getIntervalBoundaries($in, $year, $month);

    // Prepare DataTable array for Google Charts
    $data = array(
      'cols' => array(
        array('id' => 'time', 'label' => 'Time', 'type' => 'string'),
      ),
      'rows' => array(),
      'colors' => array(),  // Values other than rows and cols will be used as options argument to chart.draw()
    );

    // Fill in columns
    $columns = $this->app['mockingbird.model.transaction']->getGroupColumnValues($for == 'income', $by, $from, $to);
    foreach ($columns as $id => $column)
    {
      $data['cols'][] = array('id' => 'col' . $id, 'label' => $column['title'], 'type' => 'number');
      $data['colors'][] = $column['color'];
    }

    // Iterate over time period
    $total = 0;
    $currency = $this->app['mockingbird.model.currency']->getDefaultCurrency();
    $format = str_replace('#', '%.02f', $currency->getFormat());
    $_from = strtotime($from);
    while ($_from < strtotime($to))
    {
      // Calculate subperiod boundary
      $_to = $_from;
      $date = new \DateTime();
      $date->setTimestamp($_from);
      switch ($in)
      {
        case 'month':
          $row = strftime('%x', $_from);
          $date->add(new \DateInterval('P1D'));
          break;

        case 'year':
          $row = strftime('%B %Y', $_from);
          $date->add(new \DateInterval('P1M'));
          break;

        default:
          $row = date('Y', $_from);
          $date->add(new \DateInterval('P1Y'));
      }
      $date->sub(new \DateInterval('PT1S'));
      $_to = $date->getTimestamp();

      // Calculate
      $subtotal = 0;
      $result = $this->app['mockingbird.model.transaction']->getGroupedSumsByPeriod($for == 'income', $by, $_from, $_to);
      $values = array(array('v' => $row));
      foreach ($columns as $id => $column)
      {
        if (isset($result[$column['title']]))
          $sum = (float)$result[$column['title']]['sum'];
        else
        {
          $values[] = array('v' => null);
          continue;
        }

        $values[] = array('v' => $sum, 'f' => sprintf($format, $sum));
        $total += $sum;
        $subtotal += $sum;
      }

      $data['rows'][] = array('c' => $values);

      // Shift to next period
      $_from = $_to + 1;
    }

    // Total amount is used as a title of chart
    $data['title'] = _t('TOTAL') . ' ' . sprintf($format, $total);

    return $data;
  }

  /**
   * Prepares data for a time chart specifically with balances.
   *
   * @param string  $in  all/year/month
   * @param integer $year
   * @param integer $month
   * @return array
   */
  protected function timeChartBalanceData($in, $year, $month)
  {
    // Determine time interval boundaries
    list($from, $to) = $this->getIntervalBoundaries($in, $year, $month);

    // Prepare DataTable array for Google Charts
    $data = array(
      'cols' => array(
        array('id' => 'time', 'label' => 'Time', 'type' => 'string'),
      ),
      'rows' => array(),
      'colors' => array(),  // Values other than rows and cols will be used as options argument to chart.draw()
    );

    // Fill in columns
    $accounts = $this->app['mockingbird.model.account']->getActiveAccountsInPeriod($from, $to);
    foreach ($accounts as $account)
    {
      $data['cols'][] = array('id' => 'acc' . $account->getId(), 'label' => $account->getTitle(), 'type' => 'number');
      $data['colors'][] = $account->getColor();
    }

    // Iterate over time period
    $_from = strtotime($from);
    while ($_from < strtotime($to))
    {
      // Calculate subperiod boundary
      $_to = $_from;
      $date = new \DateTime();
      $date->setTimestamp($_from);
      switch ($in)
      {
        case 'month':
          $row = strftime('%x', $_from);
          $date->add(new \DateInterval('P1D'));
          break;

        case 'year':
          $row = strftime('%B %Y', $_from);
          $date->add(new \DateInterval('P1M'));
          break;

        default:
          $row = date('Y', $_from);
          $date->add(new \DateInterval('P1Y'));
      }
      $date->sub(new \DateInterval('PT1S'));
      $_to = $date->getTimestamp();

      // Calculate
      $subtotal = 0;
      $result = $this->app['mockingbird.model.account']->balanceSeveral($accounts, $_to);
      $values = array(array('v' => $row));
      foreach ($accounts as $account)
      {
        $sum = $result[$account->getId()];
        if (!$sum || $_from > time())  // show no data for dates in the future
        {
          $values[] = array('v' => null);
          continue;
        }

        $format = str_replace('#', '%.02f', $account->getCurrency()->getFormat());
        $values[] = array('v' => $sum * $account->getCurrency()->getRateToPrimary()
                                      / $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary(),
                          'f' => sprintf($format, $sum));
      }

      $data['rows'][] = array('c' => $values);

      // Shift to next period
      $_from = $_to + 1;
    }

    return $data;
  }
}