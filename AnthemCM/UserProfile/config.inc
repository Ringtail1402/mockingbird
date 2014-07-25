<?php

require_once __DIR__.'/../../Anthem/Auth/AuthEvents.php';

$config['Auth']['user_form_class'] = 'AnthemCM\\UserProfile\\Form\\UserWithProfileForm';
$config['Auth']['register_form_class'] = 'AnthemCM\\UserProfile\\Form\\RegisterWithProfileForm';
$config['Auth']['handlers'][\Anthem\Auth\AuthEvents::USER_ATTACH_SOCIAL][] = 'user_profile.event.user_attach_social.fill_profile_from_social';

$config['UserProfile'] = array(
  'exclude_fields' => array('nickname'),
  'require_fields' => array('firstname', 'lastname'),
);