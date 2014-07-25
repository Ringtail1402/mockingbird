<?php

$config['Core']['project'] = 'Mockingbird Dev' . (DEBUG ? ' (debug)' : '');
$config['Core']['project_logo'] = 'Mockingbird:mockingbird-b48.png';
$config['Core']['favicon'] = 'Mockingbird:favicon.ico';
$config['Core']['global_css'] = array('Mockingbird:mockingbird.css');

// Auth
$config['Auth']['enable'] = true;
$config['Auth']['policy_definitions'] = array_merge_recursive($config['Auth']['policy_definitions'], array(
  'mockingbird' => array('mockingbird.currency.ro', 'mockingbird.currency.rw',
                         'mockingbird.alldata.ro',
                         'mockingbird.user.unlimited_accounts', 'mockingbird.user.unlimited_transactions'),
));

// Admin
$config['Admin']['policies'] = array_merge($config['Admin']['policies'], array(
  'mockingbird.currencies.admin' => array('ro' => 'mockingbird.currency.ro', 'rw' => 'mockingbird.currency.rw'),
));


// Admin
$config['Admin'] = array_merge($config['Admin'], array(
  'default_menu' => 'default',
  'favicon' => 'Mockingbird:favicon.ico',
  'extra_css' => array('Mockingbird:mockingbird.css'),
));

// Custom module settings
$config['Mockingbird'] = array(
  // Limits for free users
  'limit.accounts' => 2,
  'limit.transactions_per_month' => 100,

  'openexchangerates.appid'  => $localconfig['oxr_appid'],
);

// Settings settings %)
$config['Settings']['flatten'] = true;

// Tour definition
$config['Tour']['mockingbird'] = array(
  // Intro 1: Clean slate, no DB accounts defined yed.
  'intro1' => array(
    'require' => array('intro2' => false),
    'url' => '/',
    'text' => array(
      'anchor'   => '.dashboard-accounts-list:first-child tr:first-child td:last-child',
      'position' => 'right',
      'title'    => 'TOUR_INTRO1',
      'template' => 'Mockingbird:tour/intro1.php'
    )
  ),
  // Intro 2: Defining a first account.
  'intro2' => array(
    'require' => array('intro1' => true, 'intro3' => false),
    'url' => '/accounts',
    'custom_client_condition' => 'document.location.hash.replace("#", "") == "id=0"',
    'text' => array(
      'anchor'   => '#title',
      'position' => 'right',
      'title'    => 'TOUR_INTRO2',
      'template' => 'Mockingbird:tour/intro2.php'
    )
  ),
  // Intro 3: First account defined.
  'intro3' => array(
    'require' => array('intro1' => true, 'intro4' => false),
    'url' => '/accounts',
    'custom_client_condition' => 'document.location.hash.match(/^#?id=[1-9]/)',
    'text' => array(
      'anchor'   => '.menu-item-transactions i',
      'position' => 'bottom',
      'title'    => 'TOUR_INTRO3',
      'template' => 'Mockingbird:tour/intro3.php'
    )
  ),
  // Intro 4: Defining a first transaction.
  'intro4' => array(
    'require' => array('intro3' => true, 'intro5' => false),
    'url' => '/transactions',
    'custom_client_condition' => 'document.location.hash.replace("#", "") == "id=0"',
    'text' => array(
      'anchor'   => '.row-title .help-inline',
      'position' => 'bottom',
      'title'    => 'TOUR_INTRO4',
      'template' => 'Mockingbird:tour/intro4.php'
    )
  ),
  // Intro 5: Intro completed.
  'intro5' => array(
    'require' => array('intro3' => true),
    'url' => '/transactions',
    'custom_client_condition' => 'document.location.hash.match(/^#?id=[1-9]/)',
    'text' => array(
      'anchor'   => '.menu-item-transactions',
      'position' => 'bottom',
      'title'    => 'TOUR_INTRO5',
      'template' => 'Mockingbird:tour/intro5.php'
    )
  ),
  // Dashboard hints
    'dashboard' => array(
      'require' => array('intro5' => true),
      'url' => '/',
      'text' => array(
        'anchor'   => '#today',
        'position' => 'bottom',
        'title'    => 'TOUR_DASHBOARD',
        'template' => 'Mockingbird:tour/dashboard.php'
      )
  ),
  // Transactions hints
  'transactions' => array(
    'require' => array('intro5' => true),
    'url' => '/transactions',
    'custom_client_condition' => '!document.location.hash.match(/^#?id=/)',
    'text' => array(
      'anchor'   => '.global-actions [data-id=search]',
      'position' => 'bottom',
      'title'    => 'TOUR_TRANSACTIONS',
      'template' => 'Mockingbird:tour/transactions.php'
    )
  ),
  // Budget hints
  'budget' => array(
    'require' => array('intro5' => true),
    'url' => '/budget',
    'text' => array(
      'anchor'   => '#budget-container h4 span',
      'position' => 'bottom',
      'title'    => 'TOUR_BUDGET',
      'template' => 'Mockingbird:tour/budget.php'
    )
  )
);