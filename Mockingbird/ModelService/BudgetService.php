<?php

namespace Mockingbird\ModelService;

use Silex\Application;
use Anthem\Propel\ModelService\PropelModelService;
use Mockingbird\Model\Budget;
use Mockingbird\Model\BudgetQuery;
use Mockingbird\Model\BudgetEntry;
use Mockingbird\Model\BudgetEntryQuery;

/**
 * Model service for Budget model.
 */
class BudgetService extends PropelModelService
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
   * Looks up a budget by date.
   *
   * @param integer $year
   * @param integer|null $month
   * @return Budget
   */
  public function findOneByDate($year, $month)
  {
    return BudgetQuery::create()
                      ->filterByUser($this->app['auth']->getUser())
                      ->filterByYear($year)
                      ->filterByMonth($month)
                      ->findOne();
  }

  /**
   * Returns all budgets.
   *
   * @return Budget[]
   */
  public function getAll()
  {
    return $this->createQuery()
                ->filterByUser($this->app['auth']->getUser())
                ->orderByYear()
                ->orderByMonth()
                ->find();
  }


  /**
   * Whether budget for this period can be edited/created.
   *
   * @param integer $year
   * @param integer|null $month
   * @return boolean
   */
  public function isEditable($year, $month)
  {
    return ($month && sprintf('%d-%02d', $year, $month) >= date('Y-m')) ||
           (!$month && $year >= date('Y'));
  }

  /**
   * Returns budget entries in appropriate order.
   *
   * @param Budget $budget
   * @return BudgetEntry[]
   */
  public function getBudgetEntries($budget)
  {
    return BudgetEntryQuery::create()
                           ->filterByBudget($budget)
                           ->addDescendingOrderByColumn('(SIGN(amount))')
                           ->useCategoryQuery()
                             ->orderByTitle()
                           ->endUse()
                           ->orderByWhen()
                           ->with('Category')
                           ->find();
  }

  /**
   * Calculates a budget.
   * Returns an array with estimated and actual incomes or expenses by categories.
   *
   * @param Budget  $budget
   * @param boolean $income  true = incomes, false = expenses
   * @return array
   */
  public function calculate($budget, $income)
  {
    // Budget entries
    $entries = $this->getBudgetEntries($budget);

    $ratio = $budget->getCurrency()->getRateToPrimary() / $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary();

    // Dates for budget
    if ($budget->getMonth())
    {
      $from_date = new \DateTime($budget->getYear() . '-' . $budget->getMonth() . '-01 00:00:00');
      $to_date   = clone $from_date;
      $to_date->add(new \DateInterval('P1M'))->sub(new \DateInterval('PT1S'));
      $total_periods = $to_date->format('d');
      $current_period = date('d');
    }
    else
    {
      $from_date = new \DateTime($budget->getYear() . '-01-01 00:00:00');
      $to_date   = clone $from_date;
      $to_date->add(new \DateInterval('P1Y'))->sub(new \DateInterval('PT1S'));
      $total_periods = 12;
      $current_period = date('m');
    }

    // If second date is in the future, calculate only up to current date
    if ($to_date->getTimestamp() > time())
    {
      $to_date = new \DateTime();
      $is_past = false;
    }
    else
      $is_past = true;

    // If FIRST date is in the future, there is nothing to calculate yet.
    if ($from_date->getTimestamp() > time())
      $sums = array();
    // Otherwise, calculate incomes/expenses by category
    else
      $sums = $this->app['mockingbird.model.transaction']->getGroupedSumsByPeriod($income, 'category', $from_date, $to_date);

    $result = array();
    foreach ($entries as $entry)
    {
      if (($entry->getAmount() > 0 && !$income) ||
          ($entry->getAmount() < 0 && $income)) continue;  // type doesn't match

      // Create a row for category as required
      $category = $entry->getCategory()->getTitle();
      if (!isset($result[$category]))
      {
        $result[$category] = array(
          'color' => $entry->getCategory()->getColor(),
          'when' => null,
          'description' => null,
          'estimated_total'   => 0,
          'estimated_current' => 0,
          'actual'            => 0,
          'percent'           => 0.0,
          'entries' => array()
        );
      }

      // Create an entry for this category;
      $_entry = array();
      $_entry['when'] = $entry->getWhen();
      $_entry['description'] = $entry->getDescription();
      $_entry['estimated_total'] = abs($entry->getAmount()) * $ratio;
      // Calculate estimated spending/expense by the current period of time
      if ($from_date->getTimestamp() > time())  // If entire period is in the future, this is zero
        $_entry['estimated_current'] = 0;
      elseif ($is_past)  // If entire period is in the past, this equals total sum
        $_entry['estimated_current'] = $_entry['estimated_total'];
      else
      {
        // If specific day/month is set, the estimate is zero before
        // and full sum after it comes
        if ($entry->getWhen())
        {
          if ($current_period > $entry->getWhen())
            $_entry['estimated_current'] = $_entry['estimated_total'];
          else
            $_entry['estimated_current'] = 0;
        }
        else  // Otherwise, a proportion
          $_entry['estimated_current'] = $_entry['estimated_total'] * (($current_period - 1) / $total_periods);
      }
      $result[$category]['entries'][] = $_entry;
    }

    // Iterate through result array, calculating all totals
    $total_estimated = 0;
    $total_estimated_current = 0;
    $total_actual = 0;
    foreach ($result as $category => $values)
    {
      foreach ($values['entries'] as $entry_values)
      {
        $values['estimated_total']   += $entry_values['estimated_total'];
        $values['estimated_current'] += $entry_values['estimated_current'];
      }
      $total_estimated         += $values['estimated_total'];
      $total_estimated_current += $values['estimated_current'];
      if (isset($sums[$category]))
      {
        $values['actual'] = $sums[$category]['sum'];
        $total_actual    += $sums[$category]['sum'];
        unset($sums[$category]);
      }
      if ($values['estimated_current'])
        $values['percent'] = $values['actual'] / $values['estimated_current'] * 100 - 100;


      // If there is only a single entry in the category, put its data into category and remove it
      if (count($values['entries']) == 1)
      {
        $values['when']        = $values['entries'][0]['when'];
        $values['description'] = $values['entries'][0]['description'];
        $values['entries']     = array();
      }

      $result[$category] = $values;
    }

    // Extra row for totals
    if ($total_estimated || $total_actual)
    {
      $result['*'] = array(
        'color'             => '#000000',
        'when'              => null,
        'description'       => null,
        'estimated_total'   => $total_estimated,
        'estimated_current' => $total_estimated_current,
        'actual'            => $total_actual,
        'percent'           => $total_estimated_current ? $total_actual / $total_estimated_current * 100 - 100 : null,
        'entries'           => array()
      );
    }

    // Extra row for leftover categories
    if (count($sums))
    {
      $unknown = 0;
      foreach ($sums as $sum) $unknown += $sum['sum'];

      $result[null] = array(
        'color'             => '#000000',
        'when'              => null,
        'description'       => null,
        'estimated_total'   => 0,
        'estimated_current' => 0,
        'actual'            => $unknown,
        'percent'           => null,
        'entries'           => array()
      );
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
    return 'Mockingbird\\Model\\Budget';
  }
}
