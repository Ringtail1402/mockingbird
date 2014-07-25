<?php

$config['Propel'] = array(
  'datasources' => array(
    'default' => array(
      'adapter'    => $localconfig['db_driver'],
      'connection' => array(
        'dsn'        => $localconfig['db_dsn'],
        'user'       => $localconfig['db_username'],
        'password'   => $localconfig['db_password'],
        'settings'   => $localconfig['db_driver'] == 'mysql' ? array('queries' => array('SET NAMES utf8')) : array(),
        'classname'  => DEBUG ? 'DebugPDO' : 'PropelPDO',
      ),
      'tableType'  => !empty($localconfig['db_tabletype']) ? $localconfig['db_tabletype'] :
          ($localconfig['db_driver'] == 'mysql' ? 'InnoDB' : null)
    ),
  ),
  'log' => array(
    'name'  => '/tmp/propel.log',
    'level' => 7,
  ),
);