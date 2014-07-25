<?php

$config['debug'] = DEBUG;

$config['Core'] = array(
  'project'  => 'Anthem CMF',
  'root_dir' => realpath(__DIR__.'/../..'),
  'web_root' => isset($localconfig['web_root']) ? $localconfig['web_root'] : '',
  'modules'  => $localconfig['modules'],
  'view_engines' => array('php' => 'core.view.php_engine'),
  'extend_default' => 'Anthem/Core:layout.php',
  'asset_dirs' => array('CSS', 'JS', 'Images', 'Static'),
  'log.file' => isset($localconfig['log']) ? $localconfig['log'] : (DEBUG ? '/tmp/anthem.log' : null),
  'l10n'     => true,
  'l10n.languages' => array(
    'en' => 'English',
    'ru' => 'Русский',
  ),
  'l10n.selector_in_menu' => DEBUG,
  'l10n.default_language' => isset($localconfig['default_language']) ? $localconfig['default_language'] : 'en',
  'l10n.allow_user_setting' => true,
  'mail.config' => isset($localconfig['mailconfig']) ? $localconfig['mailconfig'] : null,
  'mail.default_from' => isset($localconfig['mailfrom']) ? $localconfig['mailfrom'] : 'noreply@localhost',
);
