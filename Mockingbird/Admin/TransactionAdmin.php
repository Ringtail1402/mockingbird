<?php

namespace Mockingbird\Admin;

use Silex\Application;
use Anthem\Admin\Admin\ListAdminPage;
use Anthem\Core\ModelService\ModelServiceInterface;
use Anthem\Auth\Model\User;
use AnthemCM\Tour\View\TourHelpers;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\TransactionQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Transaction object admin page.
 */
class TransactionAdmin extends ListAdminPage
{
  /**
   * @var \Mockingbird\ModelService\TransactionService $model
   */
  protected $model;

  /**
   * @var boolean
   */
  public $exclude_parent_transactions = false;

  public function __construct(ModelServiceInterface $model, Application $app)
  {
    // mockingbird.alldata.ro -- user can view data of any users but cannot change it
    // default -- user can view only his own data and can change it
    if ($app['auth']->hasPolicies('mockingbird.alldata.ro')) $this->ro = true;

    parent::__construct($model, $app);
  }

  public function getTitle()
  {
    return _t('MENU_TRANSACTIONS');
  }

  public function getSubtitle()
  {
    return _t('TRANSACTIONS_SUBTITLE');
  }

  protected function getOptions()
  {
    $app = $this->app;

    $columns = array(
      'created_at' => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\DateTimeColumn',
        'title' => _t('DATE'), 'width' => '10%', 'sort' => true, 'filter' => true),
      'isprojected' => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\BooleanColumn',
        'title' => _t('ISPROJECTED'), 'width' => '48px', 'sort' => true, 'filter' => true,
        'options' => array('false_empty' => true)),
      'title'      => array('class' => 'Mockingbird\\Admin\\TableColumn\\TransactionTitleColumn',
        'title' => _t('TITLE'), 'sort' => true, 'filter' => true, 'link_form' => true),
      'account_id' => array('class' => 'Mockingbird\\Admin\\TableColumn\\AccountColumn',
        'title' => _t('ACCOUNT'), 'width' => '10%', 'sort' => true, 'filter' => true),
      'amount'     => array('class' => 'Mockingbird\\Admin\\TableColumn\\TransactionCurrencyColumn',
        'title' => _t('AMOUNT'),  'width' => '9%', 'sort' => true, 'filter' => true),
      'target'     => array('class' => 'Mockingbird\\Admin\\TableColumn\\TransactionTargetColumn',
        'title' => _t('TARGET'),  'width' => '15%', 'sort' => true, 'filter' => true, 'is_virtual' => true),
      'tagging'    => array('class' => 'Mockingbird\\Admin\\TableColumn\\TransactionTaggingColumn',
        'title' => _t('TAGGING'), 'width' => '15%', 'sort' => true, 'filter' => true, 'is_virtual' => true),
    );
    if ($app['auth']->hasPolicies('mockingbird.alldata.ro'))
      $columns['user'] = array('class' => 'Anthem\\Auth\\Admin\\TableColumn\\UserColumn',
        'title' => _t('USER'), 'width' => '100px', 'sort' => true, 'filter' => true);

    return array(
      'list_template' => 'Mockingbird:transactions_list.php',
      'empty_list_template' => 'Mockingbird:transactions_empty_list.php',
      'form'          => 'Mockingbird\\Form\\TransactionForm',
      'table_columns' => $columns,
      'action_column_width' => '90px',
      'default_sort'  => array('created_at', 'desc'),
      'can_edit'      => false,
      'can_purge'     => false,
      'extra_css'     => array(
        'Anthem/Forms:lib/bootstrap-datepicker.css',
      ),
      'extra_js'      => array(
        'Anthem/Forms:lib/bootstrap-datepicker.js',
        'Anthem/Core:lib/jquery.format.js',
        'Anthem/Forms:propelsubforms.js',
        'Mockingbird:transactions.js',
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
  protected function getQuery($skip_with = false)
  {
    // Yeah, that's a lot of joins.
    return TransactionQuery::create('t')
                 ->_if(!$this->app['auth']->hasPolicies('mockingbird.alldata.ro'))
                   ->filterByUser($this->app['auth']->getUser())
                 ->_endif()
                 ->useAccountQuery('a')
                   ->innerJoinCurrency('c')
                 ->endUse()
                 ->leftJoinTargetAccount('ta')
                 ->leftJoinCounterParty('cp')
                 ->leftJoinCategory('tc')
                 ->leftJoinParentTransaction('pt')
                 ->leftJoinSubTransactions('st')
                 ->_if(!$skip_with)
                   ->with('a')
                   ->with('c')
                   ->with('tc')
                 ->_endif()
                 ->withColumn('ta.Title')
                 ->withColumn('ta.Color')
                 ->withColumn('cp.Title')
                 ->withColumn('pt.Title')
                 ->withColumn('COALESCE(SUM(st.Amount), 0) + t.Amount', 'TotalAmount')
                 ->groupBy('t.Id');
  }


  /**
   * Applies current filtering options to the query.
   *
   * @param  \ModelCriteria
   * @return \ModelCriteria
   */
  protected function applyFilters($query)
  {
    $query = parent::applyFilters($query);

    // Filter by parent transaction, if applicable
    if (!empty($this->filters['parent']))
    {
      $query->filterByParentTransactionId($this->filters['parent']);
      return $query;
    }

    // Usually, sub-transactions of parent transaction are hidden by default,
    // and may be expanded with an AJAX request.
    // If certain filter columns are set however, this restriction is lifted,
    // and parent transactions do not show at all.
    $this->exclude_parent_transactions = false;
    if (!empty($this->filters['title']) ||
        !empty($this->filters['tagging']['category']) ||
        !empty($this->filters['tagging']['tag']) ||
        (!empty($this->filters['amount']['from']) || !empty($this->filters['amount']['to']) ||
        (isset($this->filters['amount']['from']) && $this->filters['amount']['from'] === '0') ||
        (isset($this->filters['amount']['to']) && $this->filters['amount']['to'] === '0')))
      $this->exclude_parent_transactions = true;

    if ($this->exclude_parent_transactions)
      $query->filterByAmount(0, \Criteria::NOT_EQUAL);
    else
      $query->filterByParentTransactionId(null, \Criteria::ISNULL);

    return $query;
  }

  /**
   * Renders extra pager content, if any.  Returns sum of all transactions, if any filters are set.
   *
   * @return string
   */
  protected function getExtraPagerContent()
  {
    if (count($this->filters))
    {
      $query = $this->getQuery(true);
      $query = $this->applyFilters($query);
      $sum = TransactionQuery::create()
                             ->addSelectQuery($query, 'subquery')
                             ->withColumn('SUM(TotalAmount)', 'sum')
                             ->select('sum')
                             ->findOne();
      $sum = $sum / $this->app['mockingbird.model.currency']->getDefaultCurrency()->getRateToPrimary();
      return $this->app['core.view']->render('Mockingbird:transactions_pager_extra.php', array(
        'sum' => $sum,
      ));
    }

    return null;
  }

  /**
   * Checks if an object can be deleted.
   *
   * @param  Transaction $object
   * @return bool
   */
  public function testDelete($object)
  {
    // Do not allow is transaction was updated sufficiently long ago, or if subtransaction
    return (time() - $object->getUpdatedAt('U') <= 86400 * $this->app['settings']->get('mockingbird.max_transaction_editable_age')) &&
            !$object->getParentTransactionId();
  }

  /**
   * Checks if several objects can be deleted.
   *
   * @param  array $ids
   * @return boolean
   */
  public function testDeleteMass(array $ids)
  {
    // Do not allow is transaction was updated sufficiently long ago, or if subtransaction
    $ndates = TransactionQuery::create()
                              ->filterByPrimaryKeys($ids)
                              ->filterByUpdatedAt(time() - 86400 * $this->app['settings']->get('mockingbird.max_transaction_editable_age'), \Criteria::GREATER_EQUAL)
                              ->filterByParentTransactionId(null, \Criteria::ISNULL)
                              ->count();
    return ($ndates > 0);
  }

  /**
   * Deletes several objects.
   *
   * @param  array $ids
   * @return boolean
   */
  public function deleteMass(array $ids)
  {
    // Do not allow is transaction was updated sufficiently long ago.
    TransactionQuery::create()
                    ->filterByPrimaryKeys($ids)
                    ->filterByUpdatedAt(time() - 86400 * $this->app['settings']->get('mockingbird.max_transaction_editable_age'), \Criteria::GREATER_EQUAL)
                    ->delete();
    return true;
  }

  /**
   * AJAX action to retrieve transaction tags.
   *
   * @param Request $request
   * @return string
   */
  public function getTagsAjax(Request $request)
  {
    $tags = $this->app['mockingbird.model.transaction']->find($request->get('id'))->getTransactionTags();
    $titles = array();
    foreach ($tags as $_tag) $titles[] = $_tag->getTitle();
    if (!count($tags)) $titles[] = '<i>' . _t('NO_TAGS') .'</i>';
    return implode(', ', $titles);
  }

  /**
   * AJAX action for search by title.
   *
   * @param Request $request
   * @return string JSON.
   */
  public function searchTitleAjax(Request $request)
  {
    $titles = $this->app['mockingbird.model.transaction']->searchTitles($request->get('q'));
    return new Response(json_encode($titles), 200, array('Content-Type' => 'application/json'));
  }

  /**
   * AJAX action for search by counterparties.
   *
   * @param Request $request
   * @return string JSON.
   */
  public function searchCounterpartiesAjax(Request $request)
  {
    $counterparties= $this->app['mockingbird.model.counterparty']->search($request->get('q'));
    $result = array();
    foreach ($counterparties as $_counterparty)
      $result[] = $_counterparty->getTitle();
    return new Response(json_encode($result), 200, array('Content-Type' => 'application/json'));
  }

  /**
   * AJAX action for search by tags.
   *
   * @param Request $request
   * @return string JSON.
   */
  public function searchTagsAjax(Request $request)
  {
    $tags = $this->app['mockingbird.model.tag']->search($request->get('q'));
    $result = array();
    foreach ($tags as $_tag)
      $result[] = $_tag->getTitle();
    return new Response(json_encode($result), 200, array('Content-Type' => 'application/json'));
  }

  /**
   * Checks that there are any overdue transactions.  Creates a notification if there are.
   * Removes it if there aren't.
   *
   * @param \Anthem\Auth\Model\User $user
   * @return void
   */
  public function updateOverdueTransactionsNotice(User $user)
  {
    $noverdue = TransactionQuery::create()
                                ->filterByUser($user)
                                ->filterByIsprojected(true)
                                ->filterByCreatedAt(time(), \Criteria::LESS_EQUAL)
                                ->count();
    if ($noverdue)
      $this->app['notify']->addPersistent(_t('TRANSACTION_OVERDUE_MESSAGE', $this->app['Core']['web_root'], date('Y-m-d%20H:i:s'), $noverdue),
        '', 'mockingbird.overdue_transactions', false, $user);
    else
      $this->app['notify']->removePersistent('mockingbird.overdue_transactions', $user);
  }

  public function postFormSave($object)
  {
    $this->updateOverdueTransactionsNotice($this->app['auth']->getUser());
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
   * Renders a form.  Checks for permissions to create a transaction.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function formAjax(Request $request)
  {
    if (!$request->get('id') && !$this->app['auth']->hasPolicies('mockingbird.user.unlimited_transactions'))
    {
      $ntransactions = $this->model->countTransactionsInMonth();
      $limit = $this->app['Mockingbird']['limit.transactions_per_month'];
      if ($ntransactions >= $limit)
      {
        return new Response(json_encode(array('error' => _t('ERROR_LIMIT_TRANSACTIONS', $limit))), 200, array('Content-Type' => 'application/json'));
      }
    }
    if (!$request->get('id') && !$this->app['mockingbird.model.account']->countAccounts())
    {
      return new Response(json_encode(array('error' => _t('ERROR_NEED_ACCOUNT_FOR_TRANSACTION'))), 200, array('Content-Type' => 'application/json'));
    }

    return parent::formAjax($request);
  }
}
