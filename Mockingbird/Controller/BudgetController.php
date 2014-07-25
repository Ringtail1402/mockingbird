<?php

namespace Mockingbird\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Mockingbird\Model\Budget;
use Mockingbird\Form\BudgetForm;

/**
 * Mockingbird budgets controller.
 */
class BudgetController
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
    return $this->app['core.view']->render('Mockingbird:budgets.php', array(
      'first_year' => date('Y', strtotime($this->app['mockingbird.model.transaction']->getFirstTransactionDate())),
      'last_year'  => date('Y') + 5,
      'print'      => $request->get('print')
    ));
  }

  /**
   * Returns data for budget visualization.
   *
   * @param  Request $request
   * @return string
   */
  public function dataAction(Request $request)
  {
    $vars = $this->getViewVars($request);

    return new Response(json_encode(array(
      'budget'    => $this->app['core.view']->render('Mockingbird:budget.php', $vars),
      'editable'  => $vars['editable'],
      'printable' => $vars['budget'] != null,
    )), 200, array('Content-Type' => 'application/json'));
  }

  /**
   * Returns data for budget chart visualization.
   *
   * @param  Request $request
   * @return string
   */
  public function chartDataAction(Request $request)
  {
    $vars = $this->getViewVars($request);
    if (!$vars['budget'])
    {
      return new Response(json_encode(array(
        'message'   => $this->app['core.view']->render('Mockingbird:budget.php', $vars),
        'editable'  => $vars['editable'],
        'printable' => $vars['budget'] != null,
      )), 200, array('Content-Type' => 'application/json'));
    }

    $result = array();

    $currency = $this->app['mockingbird.model.currency']->getDefaultCurrency();
    $format = str_replace('#', '%.02f', $currency->getFormat());

    foreach (array('incomes', 'expenses') as $type)
    {
      $source_data = $vars[$type];

      // Prepare DataTable array for Google Charts
      $data = array(
        'cols' => array(
          array('id' => 'category',          'label' => 'Category',                     'type' => 'string'),
          array('id' => 'estimated_total',   'label' => _t('BUDGET_ESTIMATED_TOTAL'),   'type' => 'number'),
          array('id' => 'estimated_current', 'label' => _t('BUDGET_ESTIMATED_CURRENT'), 'type' => 'number'),
          array('id' => 'actual',            'label' => _t('BUDGET_ACTUAL'),            'type' => 'number'),
        ),
        'rows' => array(),
      );

      // Fill in rows
      foreach ($source_data as $title => $category)
      {
        // Put totals in title
        if ($title == '*')
        {
          $data['title'] = _t('BUDGET_' . strtoupper($type)) . ' ' .
                           _t('BUDGET_ESTIMATED_TOTAL')   . ' ' . sprintf($format, $category['estimated_total']) . '; ' .
                           _t('BUDGET_ESTIMATED_CURRENT') . ' ' . sprintf($format, $category['estimated_current']) . '; ' .
                           _t('BUDGET_ACTUAL')            . ' ' . sprintf($format, $category['actual']);
          continue;
        }
        if (!$title) $title = _t('BUDGET_LEFTOVER');

        $data['rows'][]   = array('c' => array(
          array('v' => $title),
          array('v' => $category['estimated_total'],   'f' => sprintf($format, $category['estimated_total'])),
          array('v' => $category['estimated_current'], 'f' => sprintf($format, $category['estimated_current'])),
          array('v' => $category['actual'],            'f' => sprintf($format, $category['actual'])),
        ));
      }

      $result[$type] = $data;
    }

    $result['editable']  = $vars['editable'];
    $result['printable'] = $vars['budget'] != null;

    return new Response(json_encode($result), 200, array('Content-Type' => 'application/json'));
  }

  /**
   * Common data for budget table/chart view.
   *
   * @param  Request $request
   * @return array
   */
  protected function getViewVars(Request $request)
  {
    $year  = (int)$request->get('year');
    $month = $request->get('month') == 'all' ? null : (int)$request->get('month');
    $budget =   $this->app['mockingbird.model.budget']->findOneByDate($year, $month);
    $editable = $this->app['mockingbird.model.budget']->isEditable($year, $month);

    if ($budget)
    {
      $incomes  = $this->app['mockingbird.model.budget']->calculate($budget, true);
      $expenses = $this->app['mockingbird.model.budget']->calculate($budget, false);
    }
    else
    {
      $incomes  = null;
      $expenses = null;
    }

    return array(
      'year'     => $year,
      'month'    => $month,
      'budget'   => $budget,
      'editable' => $editable,
      'incomes'  => $incomes,
      'expenses' => $expenses,
    );
  }

  /**
   * Returns and/or posts budget edit form.
   *
   * @param  Request $request
   * @return string
   */
  public function editAction(Request $request)
  {
    $year  = (int)$request->get('year');
    $month = $request->get('month') == 'all' ? null : (int)$request->get('month');
    $editable = $this->app['mockingbird.model.budget']->isEditable($year, $month);
    if (!$editable)
      throw new NotFoundHttpException('Cannot edit an old budget.');

    $budget = $this->app['mockingbird.model.budget']->findOneByDate($year, $month);
    if (!$budget)
    {
      $budget = new Budget();
      $budget->setUser($this->app['auth']->getUser());
      $budget->setYear($year);
      $budget->setMonth($month);
    }

    $form = new BudgetForm($this->app, $budget);
    $valid = true;

    // Handle save
    if ($request->getMethod() == 'POST')
    {
      $form->setValue($request->request->all());
      if ($form->validate())
      {
        $form->save();
        $this->app['mockingbird.model.budget']->save($budget);
        $form = new BudgetForm($this->app, $budget);
      }
      else
        $valid = false;
    }

    return new Response(json_encode(array(
      'form'  => $form->render(),
      'valid' => $valid,
    )), 200, array('Content-Type' => 'application/json'));
  }
}