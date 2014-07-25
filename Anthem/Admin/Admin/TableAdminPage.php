<?php

namespace Anthem\Admin\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Anthem\Core\ModelService\ModelServiceInterface;
use Anthem\Admin\Admin\AdminPageInterface;

/**
 * Table-based admin page.  Provides two views: a table with sorting, filtering etc.,
 * and a form for editing an individual object.
 */
abstract class TableAdminPage implements AdminPageInterface
{
  /**
   * @var \Silex\Application
   */
  protected $app;

  /**
   * @var \Anthem\Core\ModelService\ModelServiceInterface
   */
  protected $model;

 /**
  * @var array Admin page options.
  */
  protected $options;

 /**
  * @var array[string] Links.
  */
  protected $links = array();

 /**
  * @var array[string] Global links.
  */
  protected $table_links = array();

 /**
  * @var array[string] Form links.
  */
  protected $form_links = array();

 /**
  * @var array[string] Per-record actions.
  */
  protected $actions = array();

 /**
  * @var array[string] Mass actions.
  */
  protected $mass_actions = array();

 /**
  * @var array[string] Global actions.
  */
  protected $table_actions = array();

 /**
  * @var \Anthem\Admin\Admin\TableColumn\BaseColumn[string] Table columns
  */
  protected $columns = array();

 /**
  * @var string[] Sort options (column and direction).
  */
  protected $sort;

 /**
  * @var integer Current page.
  */
  protected $page = 1;

 /**
  * @var integer Records per page.
  */
  protected $per_page;

 /**
  * @var integer Total number of records.
  */
  protected $total_records;

 /**
  * @var integer Total number of records after filters.
  */
  protected $total_filtered_records;

 /**
  * @var integer Total number of pages.
  */
  protected $total_pages;

 /**
  * @var integer Offset for query.
  */
  protected $offset = null;

 /**
  * @var boolean Does this page have any filters?
  */
  protected $has_filters = false;

 /**
  * @var array Filter options.
  */
  protected $filters = array();

  /**
   * @var boolean Read only mode.
   */
  protected $ro = false;

  /**
  * @var string[string] Templates used.
  */
  protected $templates = array(
    'frame'        => 'Anthem/Admin:table/frame.php',
     'table_link'   => 'Anthem/Admin:table/table_link.php',
     'mass_action'  => 'Anthem/Admin:table/mass_action.php',
     'table_action' => 'Anthem/Admin:table/table_action.php',
     'filters'      => 'Anthem/Admin:table/filters.php',
     'table'        => 'Anthem/Admin:table/table.php',
       'link'         => 'Anthem/Admin:table/link.php',
       'action'       => 'Anthem/Admin:table/action.php',
       'table_empty'  => 'Anthem/Admin:table/table_empty.php',
     'pager'        => 'Anthem/Admin:table/pager.php',
       'pager_empty'  => 'Anthem/Admin:table/pager_empty.php',
     'form_links'   => 'Anthem/Admin:table/form_links.php',
       'form_link'    => 'Anthem/Admin:table/form_link.php',
    'frame_print'  => 'Anthem/Admin:table/frame_print.php',
  );

 /**
  * The constructor.  Sets up options.
  */
  public function __construct(ModelServiceInterface $model, Application $app)
  {
    $this->model = $model;
    $this->app   = $app;

    // Access
    if (!empty($app['Auth']['enable']) && !empty($app['Admin']['policies'][$this->app['admin.controller']->getActivePageName()]))
    {
      $policies = $app['Admin']['policies'][$this->app['admin.controller']->getActivePageName()];
      $policies_ro = isset($policies['ro']) ? $policies['ro'] : array();
      $policies_rw = isset($policies['rw']) ? $policies['rw'] : array();
      if (!$policies_ro) $policies_ro = $policies_rw;

      $app['auth']->checkPolicies($policies_ro);
      if (!$app['auth']->hasPolicies($policies_rw)) $this->ro = true;
    }

    $this->options = $this->getOptions();
    $this->setDefaultOptions();
    $this->loadOptionsFromSession();
    if (isset($this->app['request']) && !$this->app['request']->get('no_save'))
      $this->app->after(array($this, 'saveOptionsToSession'));
  }

 /**
  * Renders this admin page.
  *
  * @param  \Symfony\Component\HttpFoundation\Request $request
  * @return string
  */
  public function render(Request $request)
  {
    return $this->app['core.view']->render($request->get('print') ? $this->getTemplate('frame_print')
                                                                  : $this->getTemplate('frame'), array(
      'admin_page' => $this,
    ));
  }

 /**
  * Renders an object table.
  *
  * @param  \Symfony\Component\HttpFoundation\Request  $request
  * @return \Symfony\Component\HttpFoundation\Response
  */
  public function tableAjax(Request $request)
  {
    // Apply options from request
    $this->loadOptionsFromRequest($request);
    $filters_query_string = $this->getFiltersQueryString();

    // Calculate pagination
    $this->setupPagination();

    // No records at all?  Too bad!
    if (!$this->total_filtered_records)
    {
      // Determine message to display
      if ($this->total_records)  // there are some records, but not with current filter settings
      {
        $message = _t('Admin.TABLE_EMPTY_FILTERED');
        if (isset($this->options['no_filtered_results_message']))
          $message = $this->options['no_filtered_results_message'];
      }
      else  // no records whatsoever
      {
        $message = _t('Admin.TABLE_EMPTY');
        if (isset($this->options['no_results_message']))
          $message = $this->options['no_results_message'];
      }

      return new Response(json_encode(array(
        'table'         => $this->app['core.view']->render($this->getTemplate('table_empty'), array(
          'message'       => $message,
          'filtered'      => (boolean)$this->total_records,
        )),
        'pager'         => $this->app['core.view']->render($this->getTemplate('pager_empty'), array(
          'total_records' => $this->total_records,
          'total_filtered_records' => $this->total_filtered_records,
          'extra_content' => $this->getExtraPagerContent(),
        )),
        'filters'       => $this->app['core.view']->render($this->getTemplate('filters'), array(
          'columns'       => $this->columns,
          'column_options' => $this->options['table_columns'],
          'use_mass_select' => $this->useMassSelect(),
          'filter_data'   => $this->filters,
        )),
        'buttons'       => array_merge($this->testTableLinks(), $this->testTableActions()),
        'canonical_address' => $filters_query_string,
        'pages'         => 0,
        'has_filters'   => (boolean)$filters_query_string,
      )), 200, array('Content-Type' => 'application/json'));
    }

    // New query
    $query = $this->getQuery();

    // Apply everything
    $query = $this->applySorting($query);
    $query = $this->applyFilters($query);
    $query = $this->applyPagination($query);

    // Go!
    $records = $this->model->query($query);

    // Results
    return new Response(json_encode(array(
      'table'         => $this->app['core.view']->render($this->getTemplate('table'), array(
        'admin_page'    => $this,
        'records'       => $records,
        'columns'       => $this->columns,
        'column_options' => $this->options['table_columns'],
        'use_mass_select' => $this->useMassSelect(),
        'test_mass_selector' => isset($this->options['test_mass_selector']) ? $this->options['test_mass_selector'] : null,
        'links'         => $this->getLinks(),
        'actions'       => $this->getActions(),
        'row_class'     => $request->get('row_class'),
        'action_column_width' => $this->getActionColumnWidth(),
        'print'         => $request->get('print')
      )),
      'pager'         => $this->app['core.view']->render($this->getTemplate('pager'), array(
        'page'          => $this->getPage(),
        'first_record'  => $this->offset + 1,
        'last_record'   => $this->offset + count($records),
        'total_records' => $this->total_records,
        'total_filtered_records' => $this->total_filtered_records,
        'total_pages'   => $this->total_pages,
        'per_page'    => $this->getPerPage(),
        'per_page_options' => $this->getPerPageOptions(),
        'extra_content' => $this->getExtraPagerContent(),
      )),
      'filters'       => $this->app['core.view']->render($this->getTemplate('filters'), array(
        'columns'       => $this->columns,
        'column_options' => $this->options['table_columns'],
        'use_mass_select' => $this->useMassSelect(),
        'filter_data'   => $this->filters,
      )),
      'buttons'       => array_merge($this->testTableLinks(), $this->testTableActions()),
      'canonical_address' => 'page=' . $this->getPage() . '&per_page=' . $this->getPerPage() .
                             '&sort.column=' . $this->getSortColumn() . '&sort.dir=' . $this->getSortDir() .
                             ($filters_query_string ? '&' . $filters_query_string : ''),
      'pages'         => $this->total_pages,
      'has_filters'   => (boolean)$filters_query_string,
    )), 200, array('Content-Type' => 'application/json'));
  }

 /**
  * Executes a per-object action.
  *
  * @param  \Symfony\Component\HttpFoundation\Request  $request
  * @return \Symfony\Component\HttpFoundation\Response
  */
  public function actionAjax(Request $request)
  {
    $action = $request->request->get('action');
    if (!isset($this->actions[$action]))
      throw new NotFoundHttpException('Unknown action \'' . $action . '\'.');
    $params = $this->actions[$action];

    $id = $request->request->get('id');
    if (!$id)
      throw new NotFoundHttpException('Missing record id for action \'' . $action . '\'.');
    $object = $this->model->find($id);
    if (!$object)
      throw new NotFoundHttpException('Unknown record id #' . $id . ' for action \'' . $action . '\'.');

    if (isset($params['test']) && !$params['test']($object))
      return new HttpException(403, 'Action \'' . $action . '\' is not accessible for record #' . $id . '.');

    $result = $params['action']($object);

    if ($result)
    {
      return new Response(json_encode(array(
        'success' => true,
        'reload'  => isset($params['reload']) ? $params['reload'] : false,
      )), 200, array('Content-Type' => 'application/json'));
    }
    else
    {
      return new Response(isset($params['error_message']) ? $params['error_message'] : '', 500);
    }
  }

 /**
  * Returns mass actions state.
  *
  * @param  \Symfony\Component\HttpFoundation\Request  $request
  * @return \Symfony\Component\HttpFoundation\Response
  */
  public function updateMassActionsAjax(Request $request)
  {
    $ids = $request->get('ids');
    if (!is_array($ids))
      throw new NotFoundHttpException('Missing record ids array for mass actions state update.');

    return new Response(json_encode($this->testMassActions($ids)), 200, array('Content-Type' => 'application/json'));
  }

 /**
  * Executes a mass action.
  *
  * @param  \Symfony\Component\HttpFoundation\Request  $request
  * @return \Symfony\Component\HttpFoundation\Response
  */
  public function massActionAjax(Request $request)
  {
    $action = $request->request->get('action');
    if (!isset($this->mass_actions[$action]))
      throw new NotFoundHttpException('Unknown mass action \'' . $action . '\'.');
    $params = $this->mass_actions[$action];

    $ids = $request->get('ids');
    if (!is_array($ids))
      throw new NotFoundHttpException('Missing record ids array for mass action \'' . $action . '\'.');

    if (isset($params['test']) && !$params['test']($ids))
      return new HttpException(403, 'Mass action \'' . $action . '\' is not accessible for some or all selected records.');

    $result = $params['action']($ids);

    if ($result)
    {
      $reload = isset($params['reload']) ? $params['reload'] : false;
      return new Response(json_encode(array(
        'success' => true,
        'reload'  => $reload,
        // Buttons will be reloaded anyway if reload is true
        'buttons' => $reload ? null : array_merge($this->testTableLinks(), $this->testTableActions()),
      )), 200, array('Content-Type' => 'application/json'));
    }
    else
    {
      return new Response(isset($params['error_message']) ? $params['error_message'] : '', 500);
    }
  }


 /**
  * Executes a table action.
  *
  * @param  \Symfony\Component\HttpFoundation\Request  $request
  * @return \Symfony\Component\HttpFoundation\Response
  */
  public function tableActionAjax(Request $request)
  {
    $action = $request->request->get('action');
    if (!isset($this->table_actions[$action]))
      throw new NotFoundHttpException('Unknown global action \'' . $action . '\'.');
    $params = $this->table_actions[$action];

    if (isset($params['test']) && !$params['test']())
      return new HttpException(403, 'Global action \'' . $action . '\' is not accessible in current context.');

    $result = $params['action']();

    if ($result)
    {
      return new Response(json_encode(array(
        'success' => true,
        'reload'  => isset($params['reload']) ? $params['reload'] : false,
      )), 200, array('Content-Type' => 'application/json'));
    }
    else
    {
      return new Response(isset($params['error_message']) ? $params['error_message'] : '', 500);
    }
  }

 /**
  * Executes a form action.  Handles both form display and submission.
  *
  * @param  \Symfony\Component\HttpFoundation\Request $request
  * @return \Symfony\Component\HttpFoundation\Response
  * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
  */
  public function formAjax(Request $request)
  {
    if ($request->get('id'))
    {
      // Existing object
      $object = $this->model->find($request->get('id'));
      if (!$object)
        throw new NotFoundHttpException('Missing record #' . $request->get('id') . '.');
    }
    else
    {
      // Create an object
      $object = $this->getNewObject();
    }

    $form = new $this->options['form']($this->app, $object,
      isset($this->options['form_options']) ? $this->options['form_options'] : array());
    /** @var \Anthem\Forms\Form\Form $form */
    if ($this->ro) $form->setReadOnly(true);
    $valid = true;

    // Handle save
    if ($request->getMethod() == 'POST')
    {
      if ($this->ro) $this->app['auth']->abort();

      $form->setValue($request->request->all());
      if ($form->validate())
      {
        $this->model->begin();
        $form->save();                  // Form -> model
        $this->model->save($object);    // Model -> DB
        $this->postFormSave($object);
        $this->model->commit();

        // Re-fetch object, re-instance and reload form
        $this->model->flush();
        $object = $this->model->find($this->model->id($object));
        $form = new $this->options['form']($this->app, $object,
          isset($this->options['form_options']) ? $this->options['form_options'] : array());
      }
      else
        $valid = false;
    }

    return new Response(json_encode(array(
      'id'    => (integer) $object->getPrimaryKey(),
      'form'  => $form->render(),
      'links' => $this->app['core.view']->render($this->getTemplate('form_links'), array(
        'object'     => $object,
        'form_links' => $this->form_links,
      )),
      'valid' => $valid,
    )), 200, array('Content-Type' => 'application/json'));
  }

  /**
   * Executes validate action.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function validateAjax(Request $request)
  {
    $object = $this->getNewObject();

    $form = new $this->options['form']($this->app, $object,
      isset($this->options['form_options']) ? $this->options['form_options'] : array());

    $form->setValue($request->request->all());
    return new Response(json_encode(array(
      'valid' => $form->validate()
    )), 200, array('Content-Type' => 'application/json'));
  }

  /**
   * Executes preview action.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function previewAjax(Request $request)
  {
    $object = $this->getNewObject();

    $form = new $this->options['form']($this->app, $object,
      isset($this->options['form_options']) ? $this->options['form_options'] : array());

    $form->setValue($request->request->all());
    if ($form->validate())
    {
      $form->save();

      // Install an after handler to rewrite the title
      $this->app->after(function (Request $request, Response $response) {
        $response->setContent(preg_replace('#<title>[^<]*</title>#i',
                                           '<title>' . _t('Admin.PREVIEW_PAGE_TITLE') . '</title>',
                                           $response->getContent()));
      }, -100);

      return $this->options['preview']($object);
    }

    throw new HttpException(400, 'Invalid preview request.');
  }

   /**
    * Renders menu.  Might be useful if menu contains some dynamic information that can be updated.
    *
    * @param  Request $request
    * @return string
    */
   public function menuUpdateAjax(Request $request)
   {
     if (!isset($this->app['Admin']['default_menu'])) return '';
     return $this->app['core.view']->render('Anthem/Nav:fixed_menu/menu.php', array(
       'menu' => $this->app['Nav']['fixed_menu'][$this->app['Admin']['default_menu']],
       'activeurl' => $request->get('url'),
       'print' => $request->get('print')));
   }

  /**
   * Returns an array of options.  Must be implemented by child pages.  Options are:
   *
   * - form (string, requried): Form class.
   * - form_options (array): Custom form options.
   * - url (function): Function taking a single record parameter and returning URL to this record, if any.
   * - preview (function): Function taking a single record parameter and showing a front-end page
   *             with current object previewed.
   * - table_columns (array, required): Columns for table view.  Column options are:
   *   - type (string, required): Column type.  Must be a registered TableColumn.
   *   - width (string): Column width (100px, 20% etc.).
   *             This should always be set, otherwise table headers and columns may not align.
   *   - sort (boolean): Is sorting by this column possible.  Must be implemented by column type.
   *   - filter (boolean): Is filtering by this column possible.  Must be implemented by column type.
   *   - link_form (boolean): Should this column be wrapped in a link to edit page.
   *   - link_attrs (string): Function returning attributes (href and/or onclick) for link to edit page.
   *   - is_virtual (boolean): Is this a virtual column.
   *             Virtual columns need not have getter functions implemented in model class.
   *   - options (array): Any extra type-specific options.
   * - default_sort (array): Default column for sorting.  First value is column, second is asc/desc for
   *             sort direction.  Default is use the first sortable column, ascending.
   * - records_per_page (array): Options for records per page selector.
   * - default_records_per_page (integer): Default records per page.
   * - no_results_message (string): Custom message to show in case there are no results to display.
   * - no_filtered_results_message (string): Custom message to show in case there are no results ti display
   *             with current filter settings, but there possibly are some other results.
   * - use_mass_select (boolean): Use mass action buttons and show mass selection checkboxes.
   * - test_mass_selector (function): Function checking whether mass selection checkbox should be displayed for
   *             object.  Receives object as argument.
   * - can_create (boolean): Display "Create" button.  Default is true.
   * - can_print (boolean): Display "Print" button.  Default is true.
   * - can_delete (boolean): Display individual, mass and form "Delete" buttons.  Default is true.
   * - can_purge (boolean): Display "Delete All" button.  Default is true.
   * - can_edit (boolean): Display "Edit"/"View" button.  table_columns\form_link may also be used.  Default is true.
   * - can_view (boolean): Display "View on Website" button.  Requires url option.  Default is FALSE.
   * - can_save (boolean): Display "Save" button on form.  Default is true.
   * - can_save_create (boolean): Display "Save and Create Another" button on form.  Default is true.
   * - can_preview (boolean): Display "Preview" button on form.  Required preview option.  Default is FALSE.
   * - extra_links (array): Extra links for a single record.
   * - extra_table_links (array): Global links rendered on top of page.
   * - extra_form_links (array): Links rendered on form.
   *   Note: links are visually rendered as buttons but actually are simple A elements.  Link options are:
   *   - title (string): Link title.
   *   - url (function): Returns a link target URL.  Receives object as argument.
   *             Either url or js options must be set.
   *   - js (function): Returns a link onclick handler.  Receives object as argument.
   *             Either url or js options must be set.
   *   - test (function): Functions checking whether the action should be enabled in this context.
   *             Receive same parameters as action functions and return a boolean value.  By default,
   *             action is always enabled.
   *   - external (boolean): Open in another window.
   *   - link_class (string): Extra CSS classes for links, if any.
   * - extra_actions (array): Extra buttons for a single record.  Action takes a single $record parameter.
   * - extra_mass_actions (array): Extra buttons for mass actions.  Action takes an array of IDs
   *             (not actual records!)  Buttons will be unavailable if no checkboxes are set.
   * - extra_table_actions (array): Extra buttons for actions affecting entire table.  Action has no
   *             parameters.
   *   Note: Actions are rendered as buttons on POST forms.  Actions have no UI other than these buttons.
   *             Action options are the same for all types of actions:
   *   - title (string): Button title.
   *   - action (function, required): Actual function executing action.  Should return true on success.
   *   - test (function): Functions checking whether the action should be enabled in this context.
   *             Receive same parameters as action functions and return a boolean value.  By default,
   *             action is always enabled.
   *   - reload (boolean): Should the table view be reloaded on successful return.  Default is false.
   *   - button_class (string): Extra CSS classes for buttons, if any.
   *   - confirm (boolean): Should we ask for confirmation on executing this action.
   *   - confirm_message (string): Custom confirmation message.
   *   - error_message (string): Custom failure message.
   * - extra_css (array): Extra CSS to include.
   * - extra_js (array): Extra Javascripts to include.
   *
   * @abstract
   * @return array
   */
  abstract protected function getOptions();

  /**
   * Returns a template for the specified part of page.
   *
   * @param  $template string
   * @return string
   */
  public function getTemplate($template)
  {
    return $this->templates[$template];
  }

  /**
   * Extends options array and sets various basic options.
   *
   * @param  none
   * @return void
   */
  protected function setDefaultOptions()
  {
    $model = $this->model;

    // Check required options
    if (!isset($this->options['table_columns']) || !is_array($this->options['table_columns']))
      throw new \LogicException('table_columns option is not set for admin page \'' . $this->getTitle() . '\'');

    // Set columns
    foreach ($this->options['table_columns'] as $name => $column)
    {
      if ($this->ro) $column['options']['readonly'] = true;
      $this->columns[$name] = $this->app['admin.table.column_factory']->createColumn($name, $column);
      if (isset($column['filter']) && $column['filter']) $this->has_filters = true;
    }

    // Set default_sort
    if (!isset($this->options['default_sort']))
    {
      foreach ($this->options['table_columns'] as $name => $column)
      {
        if (isset($column['sort']) && $column['sort'])
        {
          $this->options['default_sort'] = array($name, 'asc');
          break;
        }
      }
      if (!isset($this->options['default_sort']))
        throw new \LogicException('No sortable columns exist for admin page \'' . $this->getTitle() . '\'');
    }
    $this->sort = $this->options['default_sort'];

    // Set records_per_page
    if (!isset($this->options['records_per_page']))
      $this->options['records_per_page'] = array(25, 50, 100, 250);
    if (!isset($this->options['default_records_per_page']))
      $this->options['default_records_per_page'] = $this->options['records_per_page'][0];
    $this->per_page = $this->options['default_records_per_page'];

    // Set default actions
    $self = $this;

    if ($this->ro) $this->options['can_save'] = false;

    if (!(isset($this->options['can_create']) && !$this->options['can_create']) && !$this->ro)
    {
      $this->table_links['create'] = array(
        'title'      => '<i class="icon-file icon-white"></i> ' . _t('Admin.CREATE'),
        'url'        => function() { return '#id=0'; },
        'js'         => function() use ($self) { return 'TableAdmin.edit(0); return false;'; },
        'test'       => function() use ($self) { return $self->testCreate(); },
        'link_class' => 'btn-primary',
      );
    }

    if ($this->hasFilters())
    {
      $this->table_links['search'] = array(
        'title'      => '<i class="icon-search"></i> ' . _t('Admin.SEARCH'),
        'js'         => function () { return 'TableAdmin.toggleFilters();'; }
      );
    }

    if (!(isset($this->options['can_print']) && !$this->options['can_print']))
    {
      $this->table_links['print'] = array(
        'title'      => '<i class="icon-print"></i> ' . _t('Admin.PRINT'),
        'url'        => function() { return '?print=1'; },
        'external'   => true,
      );
    }

    if (!(isset($this->options['can_edit']) && !$this->options['can_edit']) ||
        (isset($this->options['can_edit_ro']) && $this->options['can_edit_ro']))
    {
      $this->links['edit'] = array(
        'title'      => $this->ro || !empty($this->options['can_edit_ro'])
                          ? ('<i class="icon-eye-open icon-white"></i> ' ._t('Admin.VIEW'))
                          : ('<i class="icon-pencil icon-white"></i> ' . _t('Admin.EDIT')),
        'url'        => function($object) use ($model) { return '#id=' . $model->id($object); },
        'js'         => function($object) use ($model) { return 'TableAdmin.edit(' . $model->id($object). '); return false;'; },
        'test'       => function($object) use ($self) { return $self->testEdit($object); },
        'link_class' => 'btn-primary',
      );
    }

    if (!(isset($this->options['can_view']) && !$this->options['can_view']) && isset($this->options['url']))
    {
      $url = $this->options['url'];
      $this->links['view'] = array(
        'title'      => '<i class="icon-th"></i> ' . _t('Admin.VIEW_FRONTEND'),
        'external'   => true,
        'url'        => function($object) use ($self, $url) { return $url($object); },
        'test'       => function($object) use ($self) { return $self->testView($object); },
      );
    }

    if (!(isset($this->options['can_save']) && !$this->options['can_save']))
    {
      $this->form_links['save'] = array(
        'title'      => '<i class="icon-ok icon-white"></i> ' . _t('Admin.SAVE'),
        'js'         => function($object) use ($model) { return 'TableAdmin.save(\'' . $model->id($object) . '\'); return false;'; },
        'link_class' => 'btn-primary',
        'test'       => function($object) use ($self) { return $self->testEdit($object); }
      );
    }

    if (!(isset($this->options['can_preview']) && !$this->options['can_preview']) && isset($this->options['preview']))
    {
      $this->form_links['preview'] = array(
        'title'      => '<i class="icon-search"></i> ' . _t('Admin.PREVIEW'),
        'js'         => function($object) { return 'TableAdmin.preview(); return false;'; },
        'test'       => function($object) use ($self) { return !$self->isRO(); }
      );
    }

    // "Back" form link will always be preset.
    $this->form_links['cancel'] = array(
      'title'      => '<i class="icon-arrow-left"></i> ' . _t('Admin.BACK'),
      'js'         => function($object) { return 'TableAdmin.tableView(); return false;'; },
    );

    if (!(isset($this->options['can_save_create']) && !$this->options['can_save_create']))
    {
      $this->form_links['save_create'] = array(
        'title'      => '<i class="icon-share-alt"></i> ' . _t('Admin.SAVE_CREATE'),
        'js'         => function($object) use ($model) {
          return 'TableAdmin.saveAndCreate(\'' . $model->id($object) . '\'); return false;';
        },
        'test'       => function($object) use ($self) { return $self->testEdit($object) && $self->testCreate(); }
      );
    }

    if (!(isset($this->options['can_delete']) && !$this->options['can_delete']) && !$this->ro)
    {
      $this->actions['delete'] = array(
        'title'   => '<i class="icon-remove icon-white"></i> ' . _t('Admin.DELETE'),
        'action'  => function($object) use($self) { return $self->delete($object); },
        'test'    => function($object) use($self) { return $self->testDelete($object); },
        'reload'  => true,
        'button_class' => 'btn-danger',
        'confirm' => true,
      );
      $this->mass_actions['mass_delete'] = array(
        'title'   => '<i class="icon-remove icon-white"></i> ' . _t('Admin.MASS_DELETE'),
        'action'  => function($ids) use($self) { return $self->deleteMass($ids); },
        'test'    => function($ids) use($self) { return $self->testDeleteMass($ids); },
        'reload'  => true,
        'button_class' => 'btn-danger',
        'confirm' => true,
      );
      $this->form_links['delete'] = array(
        'title'   => '<i class="icon-remove icon-white"></i> ' . _t('Admin.DELETE'),
        'js'      => function($object) use ($model) {
          return 'if (confirm(\'' . _t('Admin.CONFIRM') . '\')) ' .
                   'TableAdmin.action(\'delete\', \'' . $model->id($object) . '\'); return false;';
        },
        'test'    => function($object) use($self) { return $self->testDelete($object); },
        'link_class' => 'btn-danger',
      );
    }

    if (!(isset($this->options['can_purge']) && !$this->options['can_purge']) && !$this->ro)
    {
      $this->table_actions['purge'] = array(
        'title'   => '<i class="icon-trash icon-white"></i> ' . _t('Admin.PURGE'),
        'action'  => function() use($self) { return $self->purge(); },
        'test'    => function() use($self) { return $self->testPurge(); },
        'reload'  => true,
        'button_class' => 'btn-danger',
        'confirm' => true,
      );
    }

    // Add extra links/actions
    if (isset($this->options['extra_links']))
      $this->links = array_merge($this->links, $this->options['extra_links']);
    if (isset($this->options['extra_table_links']))
      $this->table_links = array_merge($this->table_links, $this->options['extra_table_links']);
    if (isset($this->options['extra_form_links']))
      $this->form_links = array_merge($this->form_links, $this->options['extra_form_links']);
    if (isset($this->options['extra_actions']))
      $this->actions = array_merge($this->actions, $this->options['extra_actions']);
    if (isset($this->options['extra_mass_actions']))
      $this->mass_actions = array_merge($this->mass_actions, $this->options['extra_mass_actions']);
    if (isset($this->options['extra_table_actions']))
      $this->table_actions = array_merge($this->table_actions, $this->options['extra_table_actions']);

    // Disable mass actions if necessary
    if (isset($this->options['use_mass_actions']) && !$this->options['use_mass_actions'])
      $this->mass_actions = array();
  }
 
 /**
  * Loads options from session storage, if available.
  * 
  * @param  none
  * @return void
  */
  protected function loadOptionsFromSession()
  {
    // Allow creating an admin object in CLI, possible for some aux methods.
    if (php_sapi_name() == 'cli') return;

    $prefix = $this->app['admin.controller']->getActivePageName() . '.';
    
    $this->page     = $this->app['session']->get($prefix . 'page', $this->page);
    $this->per_page = $this->app['session']->get($prefix . 'per_page', $this->per_page);
    $this->sort[0]  = $this->app['session']->get($prefix . 'sort.column', $this->sort[0]);
    $this->sort[1]  = $this->app['session']->get($prefix . 'sort.dir', $this->sort[1]);
    $this->filters  = $this->app['session']->get($prefix . 'filter', $this->filters);
  }
  
 /**
  * Saves option to session storage.
  * 
  * @param  none
  * @return void
  */
  public function saveOptionsToSession()
  {
    // Allow creating an admin object in CLI, possible for some aux methods.
    if (php_sapi_name() == 'cli') return;

    $prefix = $this->app['admin.controller']->getActivePageName() . '.';
    
    $this->app['session']->set($prefix . 'page', $this->page);
    $this->app['session']->set($prefix . 'per_page', $this->per_page);
    $this->app['session']->set($prefix . 'sort.column', $this->sort[0]);
    $this->app['session']->set($prefix . 'sort.dir', $this->sort[1]);
    $this->app['session']->set($prefix . 'filter', $this->filters);
  }
  
 /**
  * Loads options from request parameters.
  * 
  * @param  Request $request
  * @return void
  */
  protected function loadOptionsFromRequest(Request $request)
  {
    $this->page     = $request->query->get('page', $this->page);
    $this->per_page = $request->query->get('per_page', $this->per_page);
    $this->sort[0]  = $request->query->get('sort_column', $this->sort[0]);
    $this->sort[1]  = $request->query->get('sort_dir', $this->sort[1]);
    if ($request->query->get('filter_reset')) $this->filters = array();
    $this->filters  = $request->query->get('filter', $this->filters);
  }

  /**
   * Sanitizes filters array, removing empty entries.
   *
   * @return void
   */
  protected function cleanupFilters()
  {
    $filters = $this->filters;
    foreach ($filters as $name => $filter)
    {
      if (empty($filter))
        unset($filters[$name]);
      elseif (is_array($filter))
      {
        $has_params = false;
        foreach ($filter as $param_name => $filter_param)
        {
          if (empty($filter_param))
            unset($filter[$param_name]);
          else
            $has_params = true;
        }
        if ($has_params)
          $filters[$name] = $filter;
        else
          unset($filters[$name]);
      }
    }
    $this->filters = $filters;
  }

  /**
   * Returns active filters.
   *
   * @return array
   */
  public function getFilters()
  {
    return $this->filters;
  }

  /**
   * Returns current filter settings as a query string.
   *
   * @return string
   */
  protected function getFiltersQueryString()
  {
    $this->cleanupFilters();
    return str_replace('+', '%20', http_build_query(array('filter' => $this->filters)));
  }

 /**
  * Returns a new object.
  *
  * @param  none
  * @return object
  */
  protected function getNewObject()
  {
    return $this->model->create();
  }
  
 /**
  * Returns a new query object.
  *
  * @param  none
  * @return mixed
  */
  protected function getQuery()
  {
    return $this->model->createQuery();
  }

 /**
  * Applies current filtering options to the query.
  *
  * @param  mixed
  * @return mixed
  */
  protected function applyFilters($query)
  {
    foreach ($this->options['table_columns'] as $name => $params)
    {
      if (isset($params['filter']) && $params['filter'] &&
          isset($this->filters[$name]) && $this->filters[$name])
      {
        $query = $this->columns[$name]->addFilter($query, $this->filters[$name]);
      }
    }

    return $query;
  }

 /**
  * Applies current sorting options to the query.
  *
  * @param  mixed $query
  * @return mixed
  */
  protected function applySorting($query)
  {
    if (isset($this->columns[$this->sort[0]]))  // just in case
      $query = $this->columns[$this->sort[0]]->addSortCriteria($query, $this->sort[1]);
    return $query;
  }

 /**
  * Applies current pagination options to the query.
  *
  * @param  mixed $query
  * @return mixed
  */
  protected function applyPagination($query)
  {
    if ($this->offset === null)
      $this->setupPagination();

    $this->model->paginate($query, $this->per_page, $this->offset);

    return $query;
  }

 /**
  * Calculates pagination-related variables (total_records,
  * total_filtered_records, total_pages, offset).
  *
  * @param  none
  * @return void
  */
  protected function setupPagination()
  {
    // This only has to be calculated once
    if ($this->offset !== null) return;

    // Start with counting records
    $query = $this->getQuery();
    $this->total_records = $this->model->count($query);
    $query = $this->getQuery();
    $this->applyFilters($query);
    $this->total_filtered_records = $this->model->count($query);

    // Note that applyFilters() might be overridden to apply some criteria
    // even when no filters are set, but "N search results from M total" type of message will not be shown then.
    if (!count($this->filters))
      $this->total_records = $this->total_filtered_records;

    // Calculate number of pages
    if ($this->per_page)
    {
      if (!($this->total_filtered_records % $this->per_page))
        $this->total_pages = $this->total_filtered_records / $this->per_page;
      else
        $this->total_pages = (integer)($this->total_filtered_records / $this->per_page + 1);
    }
    else
      $this->total_pages = 1;

    // Normalize number of pages
    $this->page = (integer) $this->page;
    if ($this->page < 1)                  $this->page = 1;
    if ($this->page > $this->total_pages) $this->page = $this->total_pages;

    // Calculate offset for query
    $this->offset = ($this->page - 1) * $this->per_page;
  }

  /**
   * Returns any extra content which will be appended to pager, if any.
   *
   * @return string
   */
  protected function getExtraPagerContent()
  {
      return null;
  }

 /**
  * Checks if an object can be created.
  *
  * @param  none
  * @return bool
  */
  public function testCreate()
  {
    return !$this->ro;
  }

 /**
  * Checks if an object can be edited.
  *
  * @param  object $object
  * @return bool
  */
  public function testEdit($object)
  {
    return !$this->isRO();
  }

 /**
  * Checks if an object can be viewed.
  *
  * @param  object $object
  * @return bool
  */
  public function testView($object)
  {
    return isset($this->options['url']) && $this->options['url']($object);
  }

 /**
  * Deletes an object.
  *
  * @param  object $object
  * @return boolean
  */
  public function delete($object)
  {
    if ($this->ro) $this->app['auth']->abort();
    $this->model->delete($object);
    return true;
  }

 /**
  * Checks if an object can be deleted.
  *
  * @param  object $object
  * @return boolean
  */
  public function testDelete($object)
  {
    return !$this->ro && $this->model->id($object);
  }

 /**
  * Deletes several objects.
  *
  * @param  array $ids
  * @return boolean
  */
  public function deleteMass(array $ids)
  {
    if ($this->ro) $this->app['auth']->abort();
    foreach ($ids as $id) $this->model->delete($this->model->find($id));
    return true;
  }

 /**
  * Checks if several objects can be deleted.
  *
  * @param  array $ids
  * @return boolean
  */
  public function testDeleteMass(array $ids)
  {
    return !$this->ro;
  }

 /**
  * Deletes all objects from database.
  *
  * @param  none
  * @return boolean
  */
  public function purge()
  {
    if ($this->ro) $this->app['auth']->abort();
    $query = $this->getQuery();
    $this->applyFilters($query);
    $this->model->queryDelete($query);
    return true;
  }

 /**
  * Checks if all objects can be deleted from database.
  *
  * @param  none
  * @return boolean
  */
  public function testPurge()
  {
    $this->setupPagination();
    return (!$this->ro && $this->total_filtered_records > 0);
  }

 /**
  * Checks if various global links are available.
  * 
  * @param  none
  * @return boolean[string]
  */
  public function testTableLinks()
  {
    $result = array();
    foreach ($this->table_links as $name => $table_link)
    {
      if (!isset($table_link['test']))
        $result[$name] = true;  // default is enabled
      else
        $result[$name] = $table_link['test']();
    }
    return $result;
  }

 /**
  * Checks if various mass actions are available.
  *
  * @param  integer[] $ids
  * @return boolean[string]
  */
  public function testMassActions(array $ids)
  {
    $result = array();
    foreach ($this->mass_actions as $name => $mass_action)
    {
      if (!isset($mass_action['test']))
        $result[$name] = true;  // default is enabled
      else
        $result[$name] = $mass_action['test']($ids);
    }
    return $result;
  }

 /**
  * Checks if various global actions are available.
  * 
  * @param  none
  * @return boolean[string]
  */
  public function testTableActions()
  {
    $result = array();
    foreach ($this->table_actions as $name => $table_action)
    {
      if (!isset($table_action['test']))
        $result[$name] = true;  // default is enabled
      else
        $result[$name] = $table_action['test']();
    }
    return $result;
  }

 /**
  * Gets model class for this page.
  *
  * @param  none
  * @return string
  */
  public function getModel()
  {
    return $this->options['model'];
  }

 /**
  * Gets extra scripts used on this page.  This also handles column-specific JS.
  *
  * @param  none
  * @return string[]
  */
  public function getExtraScripts()
  {
    $js = array();
    if (isset($this->options['extra_js'])) $js = $this->options['extra_js'];
    $js = array_merge($js, $this->app['admin.table.column_factory']->getJavascripts());
    return $js;
  }

 /**
  * Gets extra CSS used on this page.
  *
  * @param  none
  * @return string[]
  */
  public function getExtraStylesheets()
  {
    if (!isset($this->options['extra_css'])) return array();
    return $this->options['extra_css'];
  }

 /**
  * Gets extra HTML code rendered on this page.
  *
  * @param  none
  * @return string
  */
  public function getExtraHtml()
  {
    return '';
  }

  // Getters for various options

  public function getLinks()
  {
    return $this->links;
  }

  public function getTableLinks()
  {
    return $this->table_links;
  }

  public function getFormLinks()
  {
    return $this->form_links;
  }

  public function getActions()
  {
    return $this->actions;
  }

  public function getMassActions()
  {
    return $this->mass_actions;
  }

  public function getTableActions()
  {
    return $this->table_actions;
  }

  public function useMassSelect()
  {
    return isset($this->options['use_mass_select']) ? $this->options['use_mass_select'] : true;
  }

  public function getTableColumns()
  {
    return $this->columns;
  }

  public function getColumnOptions()
  {
    return $this->options['table_columns'];
  }

  public function getActionColumnWidth()
  {
    return isset($this->options['action_column_width']) ? $this->options['action_column_width'] : null;
  }

  public function hasFilters()
  {
    return $this->has_filters;
  }

  public function getSortColumn()
  {
    return $this->sort[0];
  }

  public function getSortDir()
  {
    return $this->sort[1];
  }

  public function getPage()
  {
    return $this->page;
  }

  public function getPerPage()
  {
    if (in_array($this->per_page, $this->options['records_per_page']))
      return $this->per_page;
    return $this->options['records_per_page'][0];
  }

  public function getPerPageOptions()
  {
    return $this->options['records_per_page'];
  }

  /**
   * Returns max. amount of entries which may be shown at once in print view.
   *
   * @return int
   */
  public function getMaxPrintEntries()
  {
    return 1000;
  }

  /**
   * Runs after an object has been saved (after $object->save(), after ObjectPeer::clearInstancePool() but
   * before commit).
   *
   * @param object $object
   */
  public function postFormSave($object)
  {
  }

  public function isRO()
  {
    return $this->ro;
  }
}