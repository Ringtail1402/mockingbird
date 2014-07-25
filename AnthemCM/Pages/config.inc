<?php

$config['Auth']['policy_definitions'] = array_merge_recursive($config['Auth']['policy_definitions'], array(
  'pages.admin' => array('pages.admin.ro', 'pages.admin.rw')
));

$config['Admin']['policies'] = array_merge($config['Admin']['policies'], array(
  'pages.admin'  => array('rw' => array('pages.admin.rw'), 'ro' => array('pages.admin.ro')),
));
