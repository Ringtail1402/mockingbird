<?php

$config['Feedback'] = array(
  'feedback_to' => null
);

$config['Auth']['policy_definitions'] = array_merge_recursive($config['Auth']['policy_definitions'], array(
  'feedback.admin' => array('feedback.admin.ro', 'feedback.admin.rw')
));

$config['Admin']['policies'] = array_merge($config['Admin']['policies'], array(
  'feedback.admin'  => array('rw' => array('feedback.admin.rw'), 'ro' => array('feedback.admin.ro')),
));
