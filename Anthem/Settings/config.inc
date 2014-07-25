<?php

$config['Settings'] = array(

);

$config['Auth']['policy_definitions'] = array_merge_recursive($config['Auth']['policy_definitions'], array(
  'settings.admin' => array('settings.admin.ro', 'settings.admin.rw')
));
