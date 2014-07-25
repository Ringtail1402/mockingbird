<?php

// Top-level, non-user-editable config

// Core
$config['Core']['project'] = !empty($localconfig['override_title']) ? $localconfig['override_title'] : 'Казначей';
$config['Core']['global_css'][] = ':default.css';
$config['Core']['global_js'] = array('Anthem/Admin:admin.js', 'AnthemCM/Feedback:feedback.js');
$config['Core']['l10n.allow_user_setting'] = false;

// Auth
$config['Auth']['https'] = 'auth';
$config['Auth']['policy_definitions'] = array_merge_recursive($config['Auth']['policy_definitions'], array(
  'admin' => array('admin.show')
));

// Admin
$config['Admin']['extra_css'][] = ':default.css';
$config['Admin']['extra_js'] = array('AnthemCM/Feedback:feedback.js');
$config['Admin']['policies'] = array_merge($config['Admin']['policies'], array(
));

// Main menu
$config['Nav']['fixed_menu']['default'] = array(
  'options' => array(
    'title'      => $config['Core']['project'] . '&thinsp;<sub>&beta;</sub>',
    'logo'       => 'Mockingbird:mockingbird-w48.png',
    'login_link' => true,
  ),

  '~/about' => array(
    'title' => 'О проекте',
  ),

  'accounts' => array(
    'auth' => true,
    'title' => function ($app) {
      return _t('MENU_ACCOUNTS') . sprintf(' (%d/%d)',
        $app['mockingbird.model.account']->countAccounts(true),
        $app['mockingbird.model.account']->countAccounts());
    }
  ),

  'transactions' => array(
    'auth' => true,
    'title' => function ($app) {
      return _t('MENU_TRANSACTIONS') . sprintf(' (%d)', $app['mockingbird.model.transaction']->countTransactions()) .
             // New transaction icon
             ' <span title="' . _t('MENU_TRANSACTION_NEW') . '" onclick="document.location.href = baseURL + \'/transactions#id=0\'; return false;"><i class="icon-file icon-white"></i></span>';
    }
  ),

  'budgets' => array(
    'no_policies' => array('admin.alldata.ro'),
    'auth' => true,
    'title' => 'MENU_BUDGET'
  ),

  'charts' => array(
    'no_policies' => array('admin.alldata.ro'),
    'auth' => true,
    'title' => 'MENU_CHARTS'
  ),

  '_browse' => array(
    'auth' => true,
    'title' => 'MENU_BROWSE',
    'submenu' => array(
      'categories'     => array('title' => function ($app) {
        return _t('MENU_CATEGORIES') . sprintf(' (%d)', $app['mockingbird.model.category']->countCategories());
      }),

      'tags'           => array('title' => function ($app) {
        return _t('MENU_TAGS') . sprintf(' (%d)', $app['mockingbird.model.tag']->countTags());
      }),

      'counterparties' => array('title' => function ($app) {
        return _t('MENU_COUNTERPARTIES') . sprintf(' (%d)', $app['mockingbird.model.counterparty']->countCounterParties());
      }),
    )
  ),

  'settings.admin' => array(
    'auth' => true,
    'title' => 'MENU_SETTINGS',
  ),

  'auth.register' => array(
    'right' => true,
    'auth' => false,
    'title' => 'Auth.REGISTER',
  ),

  '_admin' => array(
    'right' => true,
    'policies' => array('admin.show'),
    'title' => 'Администрирование',
    'submenu' => array(
      'settings.admin.global' => array(
        'title' => 'Settings.SETTINGS_GLOBAL'
      ),

      'admin.page?page=auth.admin.users' => array(
        'title' => function ($app) {
          return _t('Auth.USER_ADMIN_TITLE') . sprintf(' (%d)', $app['auth.model.user']->countUsers());
        },
      ),

      'admin.page?page=auth.admin.groups' => array(
        'title' => function ($app) {
          return _t('Auth.GROUP_ADMIN_TITLE') . sprintf(' (%d)', $app['auth.model.group']->countGroups());
        },
      ),

      'admin.page?page=pages.admin' => array(
        'title' => function ($app) {
          return _t('Pages.ADMIN_TITLE') . sprintf(' (%d/%d)', $app['pages.model']->countPages(), $app['pages.model']->countPages(true));
        },
      ),

      'admin.page?page=feedback.admin' => array(
        'title' => function ($app) {
          return _t('Feedback.ADMIN_TITLE') . sprintf(' (%d)', $app['feedback.model']->countFeedbacks());
        },
      ),

      'currencies' => array(
        'title' => function ($app) {
          return _t('MENU_CURRENCIES') . sprintf(' (%d)', $app['mockingbird.model.currency']->countCurrencies());
        }
      ),
    ),
  ),

  '_user' => array(
    'right' => true,
    'auth' => true,
    'title' => function ($app) {
      $user = $app['auth']->getUser();
      $profile = $user->getUserProfile();
      $avatar = '';
      if ($profile && $profile->getAvatar())
        $avatar = '<img src="' . $profile->getAvatar() . '" style="max-width: 16px; max-height: 16px;"> ';
      if ($profile && ($profile->getFirstname() || $profile->getLastname()))
        return $avatar . $profile->getFirstname() . ' ' . $profile->getLastname();
      return $avatar . $user->getEmail();
    },
    'submenu' => array(
      'user_profile.edit' => array(
        'title' => 'UserProfile.USER_PROFILE_EDIT',
      ),
      'auth.change_email' => array(
        'title' => 'Auth.CHANGE_EMAIL',
      ),
      'auth.change_password' => array(
        'title' => 'Auth.CHANGE_PASSWORD',
      ),
      'auth.social.list_social_accounts' => array(
        'title' => 'Auth.SOCIAL_ACCOUNTS_LIST',
      ),
      'auth.logout' => array(
        'title' => 'Auth.LOGOUT',
      ),
    ),
  ),
);

// Feedback
$config['Feedback']['feedback_to'] = array($localconfig['feedbackto']);

// App
$config['demo_user_id'] = 2;
