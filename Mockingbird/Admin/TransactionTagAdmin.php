<?php

namespace Mockingbird\Admin;

use Silex\Application;
use Anthem\Admin\Admin\TableAdminPage;
use Anthem\Core\ModelService\ModelServiceInterface;
use Mockingbird\Model\TransactionTag;
use Mockingbird\Model\TransactionTagQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tag object admin page.
 */
class TransactionTagAdmin extends TableAdminPage
{
  public function __construct(ModelServiceInterface $model, Application $app)
  {
    // mockingbird.alldata.ro -- user can view data of any users but cannot change it
    // default -- user can view only his own data and can change it
    if ($app['auth']->hasPolicies('mockingbird.alldata.ro')) $this->ro = true;

    parent::__construct($model, $app);
  }

  public function getTitle()
  {
    return _t('MENU_TAGS');
  }

  public function getSubtitle()
  {
    return _t('TAG_SUBTITLE');
  }

  protected function getOptions()
  {
    $app = $this->app;
    $self = $this;

    $columns = array(
      'title'      => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\StringColumn',
        'title' => _t('TITLE'), 'width' => 'auto', 'sort' => true, 'filter' => true, 'link_form' => true),
      'num_transactions' => array('class' => 'Anthem\\Admin\\Admin\\TableColumn\\NumberColumn',
        'title' => _t('NUM_TRANSACTIONS'), 'width' => '75px', 'sort' => true, 'filter' => true, 'is_virtual' => true,
        'options' => array('template_filter' => 'Mockingbird:columns/num_transactions_filter.php')),
      'sum_transactions' => array('class' => 'Mockingbird\\Admin\\TableColumn\\SumTransactionsColumn',
        'title' => _t('SUM_TRANSACTIONS'), 'width' => '120px', 'sort' => true, 'filter' => true, 'is_virtual' => true,
        'options' => array('transaction_alias' => 't')),
    );
    if ($app['auth']->hasPolicies('mockingbird.alldata.ro'))
      $columns['user'] = array('class' => 'Anthem\\Auth\\Admin\\TableColumn\\UserColumn',
        'title' => _t('USER'), 'width' => '100px', 'sort' => true, 'filter' => true);

    return array(
      'form'          => 'Mockingbird\\Form\\TransactionTagForm',
      'table_columns' => $columns,
      'action_column_width' => '20%',
      'default_sort'  => array('title', 'asc'),
      'can_edit'      => false,
      'can_purge'     => false,
      'extra_links' => array(
        'view_transactions' => array(
          'title'      => '<i class="icon-arrow-right"></i> ' . _t('TO_TRANSACTIONS'),
          'url'        => function($object) use ($app, $self) {
            $url = $app['url_generator']->generate('transactions') .
                   '#filter.tagging.tag=' . str_replace('+', '%20', urlencode($object->getTitle()));
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
      ),
      'extra_js'      => array(
        'Anthem/Forms:lib/bootstrap-datepicker.js',
        'Mockingbird:tags.js',
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
    return TransactionTagQuery::create('tt')
                              ->_if(!$this->app['auth']->hasPolicies('mockingbird.alldata.ro'))
                                ->filterByUser($this->app['auth']->getUser())
                              ->_endif()
                              ->useRefTransactionTagQuery('rtt', \Criteria::LEFT_JOIN)
                                ->useTransactionQuery('t', \Criteria::LEFT_JOIN)
                                  ->useAccountQuery('a', \Criteria::LEFT_JOIN)
                                    ->innerJoinCurrency('c', \Criteria::LEFT_JOIN)
                                  ->endUse()
                                ->endUse()
                              ->endUse()
                              ->withColumn('COUNT(t.Id)', 'num_transactions')
                              ->withColumn('SUM(t.Amount * c.RateToPrimary)', 'sum_transactions')
                              ->groupBy('tt.Id');
  }

  /**
   * AJAX action for search by tags.
   *
   * @param Request $request
   * @return string JSON.
   */
  public function searchTitleAjax(Request $request)
  {
    $tags = $this->app['mockingbird.model.tag']->search($request->get('q'));
    $result = array();
    foreach ($tags as $_tag)
      $result[] = $_tag->getTitle();
    return new Response(json_encode($result), 200, array('Content-Type' => 'application/json'));
  }
}
