<?php

// Auth module default config

require_once __DIR__.'/AuthEvents.php';

$config['Admin']['policies'] = array_merge($config['Admin']['policies'], array(
 'auth.admin.users'  => array('rw' => array('auth.admin.rw'), 'ro' => array('auth.admin.ro')),
 'auth.admin.groups' => array('rw' => array('auth.admin.rw'), 'ro' => array('auth.admin.ro')),
));

$config['Auth'] = array(
  'enable' => false,               // Need to turn on this feature explicitly

  'https' => 'ignore',             // Possible values: ignore, never, always and auth (for authorized users)
                                   // 'ignore' (default) just goes with current protocol and doesn't change it.
                                   // 'auth' uses https for authorized users and http by default.
                                   // 'auth' or 'always' are strongly recommended.  Both of them use secure cookies.
                                   // TODO: make 'auth' always downgrade to http automatically

  'features' => array(
    'remember_me' => true,         // Allow Remember Me checkbox and cookie
    'registration' => true,        // Allow free user registration
    'email_validation' => true,    // Require e-mail validation
    'password_recovery' => true,   // Allow password reset feature
    'social_accounts' => true,     // Allow login through social
  ),

  'hash' => 'sha256',              // Default hashing algorithm
  'remember_me_age' => 365 * 86400,// Remember Me cookie/key max age, seconds
  'mailed_keys_age' => 3600,       // Email validation and Reset Password key max age, seconds
  'user_form_class'     => 'Anthem\\Auth\\Form\\UserForm',  // Default user editing admin form class
  'register_form_class' => 'Anthem\\Auth\\Form\\RegisterForm',  // Default user registration form class

  // Event handlers to activate
  'handlers' => array(
    \Anthem\Auth\AuthEvents::LOGIN_CHECK => array(
      'auth.event.login_check.session',
      'auth.event.login_check.remember_me',
    ),

    \Anthem\Auth\AuthEvents::LOGIN_AUTO => array(
      'auth.event.login.session',
      'auth.event.login.remember_me',
      'auth.event.login.social_account_attach',
    ),

    \Anthem\Auth\AuthEvents::LOGIN_MANUAL => array(
      'auth.event.login.session',
      'auth.event.login.remember_me',
      'auth.event.login.social_account_attach',
    ),

    \Anthem\Auth\AuthEvents::LOGOUT => array(
      'auth.event.logout.session',
      'auth.event.logout.remember_me',
    ),
  ),

  // Social auth providers to activate
  'social_auth' => array(
    'auth.social.vk' => array(
      'client_id'     => isset($localconfig['vk_appid']) ? $localconfig['vk_appid'] : 'UNKNOWN_APP_ID',
      'client_secret' => isset($localconfig['vk_secret']) ? $localconfig['vk_secret'] : 'UNKNOWN_APP_SECRET',
      'scope'         => '',
      'user_fields'   => 'first_name,last_name,screen_name,nickname,photo_max',
    ),

    'auth.social.google' => array(
      'client_id'     => isset($localconfig['google_appid']) ? $localconfig['google_appid'] : 'UNKNOWN_APP_ID',
      'client_secret' => isset($localconfig['google_secret']) ? $localconfig['google_secret'] : 'UNKNOWN_APP_SECRET',
      'scope'         => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email ',
    ),
  ),

  // Policies which pertain to Auth module itself
  'policy_definitions' => array(
    'auth.admin' => array('auth.admin.ro', 'auth.admin.rw', 'auth.admin.force_login'),
  ),

  // User lock reasons
  'lock_reasons' => array('default', 'email_validation'),
);
