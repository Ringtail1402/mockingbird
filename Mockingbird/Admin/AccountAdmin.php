<?php

namespace Mockingbird\Admin;

use Silex\Application;
use Anthem\Admin\Admin\TableAdminPage;
use Anthem\Core\ModelService\ModelServiceInterface;
use AnthemCM\Tour\View\TourHelpers;
use Mockingbird\Model\Account;
use Mockingbird\Model\AccountQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Account object admin page.
 */
class AccountAdmin extends TableAdminPage
{
  /**
   * @var \Mockingbird\ModelService\AccountService $model
   */
  protected $model;

  /**
   * The constructor.
   *
   * @param \Anthem\Core\ModelService\ModelServiceInterface $model
   * @param \Silex\Application $app
   */
  public function __construct(ModelServiceInterface $model, Application $app)
  {
    // mockingbird.alldata.ro -- user can view data of any users but cannot change it
    // default -- user can view only his own data and can change it
    if ($app['auth']->hasPolicies('mockingbird.alldata.ro')) $this->ro = true;

    parent::__construct($model, $app);
  }

  public function getTitle()
  {
    return _t('MENU_ACCOUNTS');
  }

  public function getSubtitle()
  {
    return _t('ACCOUNTS_SUBTITLE');
  }

  protected function getOptions()
  {
    $app = $this->app;
    $self = $this;

    $columns = array(
      'title'      => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\StringColumn',
        'title' => _t('TITLE'), 'width' => 'auto', 'sort' => true, 'filter' => true, 'link_form' => true),
      'color'      => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\ColorColumn',
        'title' => '', 'width' => '15px'),
      'type'       => array('class' => 'Mockingbird\\Admin\\TableColumn\\AccountTypeColumn',
        'title' => _t('IS_DEBT'), 'width' => '75px', 'sort' => true, 'filter' => true, 'is_virtual' => true),
      'isclosed'   => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\BooleanColumn',
        'title' => _t('IS_ACTIVE'), 'width' => '64px', 'sort' => true, 'filter' => true,
        'options' => array('false_empty' => true, 'invert_value' => true)),
      'currency_id' => array('class' => 'Mockingbird\\Admin\\TableColumn\\CurrencyDenominationColumn',
        'title' => _t('CURRENCY'), 'width' => '64px', 'sort' => true, 'filter' => true),
      // TODO: filter fails, condition should go in HAVING
      'balance'    => array('class' => 'Mockingbird\\Admin\\TableColumn\\CurrencyColumn',
        'title' => _t('BALANCE'), 'width' => '120px', 'sort' => true, 'filter' => false, 'is_virtual' => true,
        'options' => array('currency_alias' => 'c')),
      /*'created_at' => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\DateTimeColumn',
                            'title' => _t('ACCOUNT_CREATED'), 'width' => '10%', 'sort' => true, 'filter' => true),*/
      // TODO: filter fails, condition should go in HAVING
      'last_transaction' => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\DateTimeColumn',
        'title' => _t('ACCOUNT_UPDATED'), 'width' => '110px', 'sort' => true, 'filter' => false, 'is_virtual' => true),
      'num_transactions' => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\NumberColumn',
        'title' => _t('NUM_TRANSACTIONS'), 'width' => '75px', 'sort' => true, 'filter' => true, 'is_virtual' => true,
        'options' => array('template_filter' => 'Mockingbird:columns/num_transactions_filter.php')),
      'sum_transactions' => array('class' => 'Mockingbird\\Admin\\TableColumn\\SumTransactionsColumn',
        'title' => _t('SUM_TRANSACTIONS'), 'width' => '120px', 'sort' => true, 'filter' => true, 'is_virtual' => true,
        'options' => array('currency_alias' => 'c', 'transaction_alias' => 't')),
    );
    if ($app['auth']->hasPolicies('mockingbird.alldata.ro'))
      $columns['user'] = array('class' => 'Anthem\\Auth\\Admin\\TableColumn\\UserColumn',
                                  'title' => _t('USER'), 'width' => '100px', 'sort' => true, 'filter' => true);

    return array(
      'form'          => 'Mockingbird\\Form\\AccountForm',
      'no_results_message' => _t('ACCOUNT_TABLE_EMPTY'),
      'table_columns' => $columns,
      'action_column_width' => '17%',
      'default_sort'  => array('last_transaction', 'desc'),
      'can_edit'      => false,
      'can_purge'     => false,
      'extra_links' => array(
        'view_transactions' => array(
          'title'      => '<i class="icon-arrow-right"></i> ' . _t('TO_TRANSACTIONS'),
          'url'        => function($object) use ($app, $self) {
            $url = $app['url_generator']->generate('transactions') .
                   '#filter.account_id=' . $object->getId();
            $filters = $self->getFilters();
            if (!empty($filters['sum_transactions']['from']))
              $url .= '&filter.created_at.from=' . $filters['sum_transactions']['from'];
            if (!empty($filters['sum_transactions']['to']))
              $url .= '&filter.created_at.to='   . $filters['sum_transactions']['to'];
            return $url;
          },
        ),
      ),
      'extra_css'     => array(
        'Anthem/Forms:lib/bootstrap-datepicker.css',
        'Anthem/Forms:lib/bootstrap-colorpicker.css',
      ),
      'extra_js'      => array(
        'Anthem/Forms:lib/bootstrap-datepicker.js',
        'Anthem/Forms:lib/bootstrap-colorpicker.js',
        'Anthem/Core:lib/jquery.format.js',
        'Mockingbird:accounts.js',
        'Mockingbird:util.js',
      )
    );
  }

  /**
   * Returns a new query object.
   *
   * @param  none
   * @return \ModelCriteria
   */
  protected function getQuery()
  {
    return AccountQuery::create('a')
                       ->_if(!$this->app['auth']->hasPolicies('mockingbird.alldata.ro'))
                         ->filterByUser($this->app['auth']->getUser())
                       ->_endif()
                       ->innerJoinCurrency('c')
                       ->leftJoinTransactions('t')
                       ->withColumn('(SELECT tlt.CREATED_AT
                                      FROM transactions tlt
                                      WHERE tlt.ACCOUNT_ID = accounts.ID
                                      ORDER BY tlt.CREATED_AT DESC
                                      LIMIT 1)', 'last_transaction')
                       ->withColumn('COUNT(t.Id)', 'num_transactions')
                       ->withColumn('SUM(t.Amount)', 'sum_transactions')
                       // XXX Probably should not spell out entire subquery as plain SQL
                       ->withColumn('(SELECT ab.INITIAL_AMOUNT + COALESCE(SUM(tb.AMOUNT), 0)
                                      FROM accounts ab
                                      LEFT JOIN transactions tb
                                             ON (ab.ID = tb.ACCOUNT_ID)
                                      WHERE ab.ID = accounts.ID)', 'balance')
                       ->groupBy('a.Id');
  }

  /**
   * Checks if an object can be deleted.
   *
   * @param  Account $object
   * @return bool
   */
  public function testDelete($object)
  {
    // Do not allow if any transactions exist.
    // TODO: this is an extra query per every object.
    return !$object->isNew() && $object->countTransactionss() == 0;
  }

  /**
   * Checks if several objects can be deleted.
   *
   * @param  array $ids
   * @return boolean
   */
  public function testDeleteMass(array $ids)
  {
    // Do not allow if any transactions exist.
    $naccounts = AccountQuery::create('a')
                             ->filterByPrimaryKeys($ids)
                             ->leftJoinTransactions('t')
                             ->withColumn('COUNT(t.Id)', 'num_transactions')
                             ->having('num_transactions = 0')
                             ->groupBy('a.Id')
                             ->count();
    return ($naccounts > 0);
  }

  /**
   * Deletes several objects.
   *
   * @param  array $ids
   * @return boolean
   */
  public function deleteMass(array $ids)
  {
    // Do not allow if any transactions exist.
    $accounts = AccountQuery::create('a')
                            ->filterByPrimaryKeys($ids)
                            ->leftJoinTransactions('t')
                            ->withColumn('COUNT(t.Id)', 'num_transactions')
                            ->having('num_transactions = 0')
                            ->groupBy('a.Id')
                            ->find();
    $ids = array();
    foreach ($accounts as $account) $ids[] = $account->getId();
    AccountQuery::create('a')
                ->filterByPrimaryKeys($ids)
                ->delete();

    return true;
  }

  /**
   * Returns extra HTML.  Support tours.
   *
   * @return string
   */
  public function getExtraHtml()
  {
    $tour = new TourHelpers($this->app);
    return $tour->init('mockingbird');
  }

  /**
   * Renders a form.  Checks for permissions to create an account.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function formAjax(Request $request)
  {
    if (!$request->get('id') && !$this->app['auth']->hasPolicies('mockingbird.user.unlimited_accounts'))
    {
      $naccounts = $this->model->countAccounts(false, true);
      $limit = $this->app['Mockingbird']['limit.accounts'];
      if ($naccounts >= $limit)
      {
        return new Response(json_encode(array('error' => _t('ERROR_LIMIT_ACCOUNTS', $limit))), 200, array('Content-Type' => 'application/json'));
      }
    }

    return parent::formAjax($request);
  }
}
