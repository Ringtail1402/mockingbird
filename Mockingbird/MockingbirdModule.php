<?php

namespace Mockingbird;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Anthem\Propel\Fixtures\FixtureProviderInterface;
use Anthem\Core\Module;
use Anthem\Core\View\HelperProviderInterface;
use Anthem\Cron\CronProviderInterface;
use Anthem\Settings\SettingsInterface;
use Anthem\Settings\SettingsProviderInterface;
use Mockingbird\ModelService\AccountService;
use Mockingbird\ModelService\CurrencyService;
use Mockingbird\ModelService\TransactionService;
use Mockingbird\ModelService\CounterPartyService;
use Mockingbird\ModelService\TransactionCategoryService;
use Mockingbird\ModelService\TransactionTagService;
use Mockingbird\ModelService\BudgetService;
use Mockingbird\Controller\DashboardController;
use Mockingbird\Controller\ChartController;
use Mockingbird\Controller\BudgetController;
use Mockingbird\View\MockingbirdHelpers;
use Mockingbird\Admin\AccountAdmin;
use Mockingbird\Admin\TransactionAdmin;
use Mockingbird\Admin\CounterPartyAdmin;
use Mockingbird\Admin\TransactionCategoryAdmin;
use Mockingbird\Admin\TransactionTagAdmin;
use Mockingbird\Admin\CurrencyAdmin;
use Mockingbird\Util\SQLiteFunctions;
use Mockingbird\MockingbirdSettings;

/**
 * Mockingbird module, a simple personal accounting web app.
 */
class MockingbirdModule extends Module implements ServiceProviderInterface,
                                                  ControllerProviderInterface,
                                                  FixtureProviderInterface,
                                                  HelperProviderInterface,
                                                  CronProviderInterface,
                                                  SettingsProviderInterface
{
  /**
   * The constructor.
   *
   * @param \Silex\Application $app
   */
  public function __construct(Application $app)
  {
    parent::__construct($app);

    // Register extra SQLite functions, if applicable.
    if (php_sapi_name() != 'cli')
    {
      $con = \Propel::getConnection();
      if ($con->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'sqlite')
        SQLiteFunctions::registerExtraSQLiteFunctions($con);
    }
  }

  /**
   * Registers Mockingbird services.
   *
   * @param  Application $app
   * @return void
   */
  public function register(Application $app)
  {
    $app['mockingbird.dashboard.controller'] = $app->share(function() use ($app) { return new DashboardController($app); });
    $app['mockingbird.chart.controller']     = $app->share(function() use ($app) { return new ChartController($app); });
    $app['mockingbird.budget.controller']    = $app->share(function() use ($app) { return new BudgetController($app); });

    $app['mockingbird.model.account']        = $app->share(function() use ($app) { return new AccountService($app); });
    $app['mockingbird.model.currency']       = $app->share(function() use ($app) { return new CurrencyService($app); });
    $app['mockingbird.model.transaction']    = $app->share(function() use ($app) { return new TransactionService($app); });
    $app['mockingbird.model.counterparty']   = $app->share(function() use ($app) { return new CounterPartyService($app); });
    $app['mockingbird.model.category']       = $app->share(function() use ($app) { return new TransactionCategoryService($app); });
    $app['mockingbird.model.tag']            = $app->share(function() use ($app) { return new TransactionTagService($app); });
    $app['mockingbird.model.budget']         = $app->share(function() use ($app) { return new BudgetService($app); });

    $app['mockingbird.accounts.admin']       = $app->share(function() use ($app) { return new AccountAdmin($app['mockingbird.model.account'], $app); });
    $app['mockingbird.transactions.admin']   = $app->share(function() use ($app) { return new TransactionAdmin($app['mockingbird.model.transaction'], $app); });
    $app['mockingbird.counterparties.admin'] = $app->share(function() use ($app) { return new CounterPartyAdmin($app['mockingbird.model.counterparty'], $app); });
    $app['mockingbird.categories.admin']     = $app->share(function() use ($app) { return new TransactionCategoryAdmin($app['mockingbird.model.category'], $app); });
    $app['mockingbird.tags.admin']           = $app->share(function() use ($app) { return new TransactionTagAdmin($app['mockingbird.model.tag'], $app); });
    $app['mockingbird.currencies.admin']     = $app->share(function() use ($app) { return new CurrencyAdmin($app['mockingbird.model.currency'], $app); });
  }


  /**
   * Returns routes to connect to the given application.
   *
   * @param  Application $app
   * @return ControllerCollection
   */
  public function connect(Application $app)
  {
    $controllers = new ControllerCollection();
    $self = $this;

    // Dashboard
    $controllers->get('/dashboard',
      function(Request $request) use($app) { return $app['mockingbird.dashboard.controller']->indexAction($request); }
    )->bind('dashboard');
    $controllers->get('/ajax/dashboard_accounts/{datetime}',
      function(Request $request, $datetime = null) use($app) { return $app['mockingbird.dashboard.controller']->accountsAction($request, $datetime); }
    )->bind('dashboard_accounts');
    $controllers->get('/ajax/dashboard_calendar/month/{year}/{month}',
      function(Request $request, $year, $month) use($app) { return $app['mockingbird.dashboard.controller']->monthCalendarAction($request, $year, $month); }
    )->bind('dashboard_calendar_month');
    $controllers->get('/ajax/dashboard_calendar/year/{year}',
      function(Request $request, $year) use($app) { return $app['mockingbird.dashboard.controller']->yearCalendarAction($request, $year); }
    )->bind('dashboard_calendar_year');
    $controllers->get('/ajax/dashboard_calendar/all',
      function(Request $request) use($app) { return $app['mockingbird.dashboard.controller']->allCalendarAction($request); }
    )->bind('dashboard_calendar_all');

    // Charts
    $controllers->get('/charts',
      function(Request $request) use($app) { return $app['mockingbird.chart.controller']->indexAction($request); }
    )->bind('charts');
    $controllers->get('/ajax/chart_data',
      function(Request $request) use($app) { return $app['mockingbird.chart.controller']->dataAction($request); }
    )->bind('chart_data');

    // Budgets
    $controllers->get('/budget',
      function(Request $request) use($app) { return $app['mockingbird.budget.controller']->indexAction($request); }
    )->bind('budgets');
    $controllers->get('/ajax/budget_data',
      function(Request $request) use($app) { return $app['mockingbird.budget.controller']->dataAction($request); }
    )->bind('budget_data');
    $controllers->get('/ajax/budget_chart_data',
      function(Request $request) use($app) { return $app['mockingbird.budget.controller']->chartDataAction($request); }
    )->bind('budget_chart_data');
    $controllers->match('/ajax/budget_edit',
      function(Request $request) use($app) { return $app['mockingbird.budget.controller']->editAction($request); }
    )->bind('budget_edit');

    // Admin pages
    $controllers->get('/accounts',
      function(Request $request) use($app) { return $app['admin.controller']->indexAction($request, 'mockingbird.accounts.admin'); }
    )->bind('accounts');
    $controllers->get('/transactions',
      function(Request $request) use($app) { return $app['admin.controller']->indexAction($request, 'mockingbird.transactions.admin'); }
    )->bind('transactions');
    $controllers->get('/counterparties',
      function(Request $request) use($app) { return $app['admin.controller']->indexAction($request, 'mockingbird.counterparties.admin'); }
    )->bind('counterparties');
    $controllers->get('/categories',
      function(Request $request) use($app) { return $app['admin.controller']->indexAction($request, 'mockingbird.categories.admin'); }
    )->bind('categories');
    $controllers->get('/tags',
      function(Request $request) use($app) { return $app['admin.controller']->indexAction($request, 'mockingbird.tags.admin'); }
    )->bind('tags');
    $controllers->get('/currencies',
      function(Request $request) use($app) { return $app['admin.controller']->indexAction($request, 'mockingbird.currencies.admin'); }
    )->bind('currencies');

    return $controllers;
  }

  /**
   * Registers Mockingbird fixtures.
   *
   * @param  none
   * @return string[]
   */
  public function getFixtureClasses()
  {
    return array('Mockingbird\\Fixtures\\CurrencyFixtures');
  }

  /**
   * Returns Mockingbird helpers.
   *
   * @param  \Silex\Application $app
   * @return object[string]
   */
  public function getHelpers(Application $app)
  {
    return array(
      'm'  => new MockingbirdHelpers($app),
    );
  }

  /**
   * Returns Mockingbird cron tasks.
   *
   * @return array
   */
  public function getCronTasks(Application $app)
  {
    if (!$app['settings']->get('mockingbird.auto_update_rates')) return array();

    return array(
      'hourly' => array(
        'mockingbird:notice-overdue-transactions' => function () use ($app) {
          $app['mockingbird.model.transaction']->updateAllOverdueTransactionsNotice();
        }
      ),
      'daily' => array(
        'mockingbird:update-currency-rates' => function () use ($app) {
          $app['mockingbird.model.currency']->loadRate();
        }
      )
    );
  }

  /**
   * Returns Mockingbird settings.
   *
   * @return SettingsInterface
   */
  public function getSettings()
  {
    return new MockingbirdSettings();
  }
}
