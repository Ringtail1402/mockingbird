<?php

$_t['Auth.EMAIL'] = 'E-mail';
$_t['Auth.EMAIL_HELP'] = 'Must be unique.  Used as main user handle.';
$_t['Auth.PASSWORD'] = 'Password';
$_t['Auth.PASSWORD_HELP'] = 'Password is encrypted for storage.  It cannot be viewed but can be changed.';
$_t['Auth.PASSWORD2'] = 'Repeat password';
$_t['Auth.GROUPS'] = 'Groups';
$_t['Auth.GROUPS_HELP'] = 'Groups allow assigning same permissions to multiple users.  User will inherit permissions ' .
    'from all groups he belongs to.';
$_t['Auth.IS_SUPERUSER'] = 'Superuser';
$_t['Auth.IS_SUPERUSER_HELP'] = '<span class="label label-important">WARNING!</span>  Superusers always have permissions ' .
    'to execute <b>any</b> possible action in this application.  Only another superuser can create or modify superusers.';
$_t['Auth.LOCKED'] = 'Lock Reason';
$_t['Auth.LOCKED_HELP'] = 'If set, the user will not be able to log into application (and will be forcibly logged out, ' .
    'if already logged in).  The user will be shown an appropriate error message.';
$_t['Auth.NOT_LOCKED'] = 'None';
$_t['Auth.LOCK'] = 'Lock';
$_t['Auth.REASON'] = 'Reason';
$_t['Auth.UNLOCK'] = 'Unlock';
$_t['Auth.POLICIES'] = 'Permissions';
$_t['Auth.POLICY_ENABLE'] = 'Yes';
$_t['Auth.POLICY_DISABLE'] = 'No';
$_t['Auth.POLICY_ENABLE_INHERITED'] = 'Yes (by default)';
$_t['Auth.POLICY_DISABLE_INHERITED'] = 'No (by default)';
$_t['Auth.CREATED_AT'] = 'Registered';
$_t['Auth.LAST_LOGIN'] = 'Last Online';
$_t['Auth.TITLE'] = 'Title';
$_t['Auth.NUM_USERS'] = 'Users';
$_t['Auth.LOGIN'] = 'Log in';
$_t['Auth.LOGIN_REDIR'] = 'Authorization Required';
$_t['Auth.REMEMBER_ME'] = 'Remember Me';
$_t['Auth.LOGOUT'] = 'Log out';
$_t['Auth.REGISTER'] = 'Register';
$_t['Auth.REGISTER_EMAIL_VALIDATION'] = '%s registration';
$_t['Auth.REGISTER_EMAIL_VALIDATION_NEEDED'] = 'E-mail Verification';
$_t['Auth.REGISTER_EMAIL_VALIDATION_NEEDED_SUBTITLE'] = 'You have registered successfully, but we need to make sure ' .
    'that the email address you specified really belongs to you.  A letter with e-mail validation link has been sent ' .
    'to %s address.  Check your e-mail.';
$_t['Auth.REGISTER_EMAIL_VALIDATION_MAIL_BODY1'] = <<<EOF
You have just registered at the website <a href="%s">%s</a>.  To confirm your e-mail address and complete the
registration, click on this link or cut and paste it into your web browser:
EOF;
$_t['Auth.REGISTER_EMAIL_VALIDATION_MAIL_BODY2'] = <<<EOF
If you have not actually registered on this website, you may ignore this e-mail safely.
EOF;
$_t['Auth.REGISTER_EMAIL_VALIDATION_INVALID_KEY'] = 'E-mail validation link is invalid or too old.  You will need to register again.';
$_t['Auth.REGISTER_EMAIL_VALIDATION_SUCCESS'] = 'You have successfully verified your e-mail.';
$_t['Auth.CHANGE_EMAIL'] = 'Change E-mail';
$_t['Auth.CHANGE_EMAIL_MAIL_BODY1'] = <<<EOF
You have requested to change your email at the website <a href="%s">%s</a>.  To confirm this e-mail address
belongs to you, click on this link or cut and paste it into your web browser:
EOF;
$_t['Auth.CHANGE_EMAIL_MAIL_BODY2'] = <<<EOF
If you have not actually requested e-mail change on this website, you may ignore this e-mail safely.
EOF;
$_t['Auth.CHANGE_EMAIL_NOTICE'] = 'A letter with e-mail confirmation link has been sent to %s address.  Check your e-mail.';
$_t['Auth.CHANGE_EMAIL_INVALID_KEY'] = 'E-mail validation link is invalid or too old.  Change your e-mail again.';
$_t['Auth.CHANGE_EMAIL_SUCCESS'] = 'Your e-mail address has been changed.';
$_t['Auth.CHANGE_PASSWORD'] = 'Change Password';
$_t['Auth.SAVE'] = 'Save';
$_t['Auth.LOGIN_AS_USER'] = 'Login As User';
$_t['Auth.LOGIN_AS_USER_CONFIRM'] = 'This will log you out as current user, log you in as this user without ' .
    'prompting for password, and redirect to main page.';
$_t['Auth.TO_USERS'] = 'View Users';
$_t['Auth.AUTH_VALIDATOR_MESSAGE'] = 'Invalid e-mail or password.';
$_t['Auth.UNIQUE_EMAIL_VALIDATOR_MESSAGE'] = 'An user with this e-mail already exists.';
$_t['Auth.UNIQUE_GROUP_TITLE_VALIDATOR_MESSAGE'] = 'A group with this title already exists.';
$_t['Auth.PASSWORD_EQUALITY_VALIDATOR_MESSAGE'] = 'The passwords must match.';
$_t['Auth.USER_VALIDATOR_MESSAGE'] = 'No user with this e-mail exist.';
$_t['Auth.FORGOT_PASSWORD'] = 'Forgot your password?';
$_t['Auth.REQUEST_PASSWORD'] = 'Password Recovery';
$_t['Auth.REQUEST_PASSWORD_SUBTITLE'] = 'Enter the e-mail address you used to register on this website.  A password recovery link ' .
    'will be sent to this address';
$_t['Auth.REQUEST_PASSWORD_EMAIL_NONEXISTENT_VALIDATOR_MESSAGE'] = 'No user with this e-mail is registered.';
$_t['Auth.REQUEST_PASSWORD_EMAIL_INVALID_VALIDATOR_MESSAGE'] = 'The specified user does exist, but his e-mail is invalid and password ' .
    'cannot be recovered in this way.';
$_t['Auth.REQUEST_PASSWORD_MAIL'] = 'Password recovery for website %s';
$_t['Auth.REQUEST_PASSWORD_NOTICE'] = 'A letter with password recovery link has been sent to %s address.  Check your e-mail.';
$_t['Auth.REQUEST_PASSWORD_MAIL_BODY1'] = <<<EOF
You have requested password recovery at the website <a href="%s">%s</a>.  To change your password, click on this link
or cut and paste it into your web browser:
EOF;
$_t['Auth.REQUEST_PASSWORD_MAIL_BODY2'] = <<<EOF
If you have not actually requested password recovery, you may ignore this e-mail safely.
EOF;
$_t['Auth.RESET_PASSWORD'] = 'Password Reset';
$_t['Auth.RESET_PASSWORD_INVALID_KEY'] = 'Password recovery link is invalid or too old.  Request password recovery again.';
$_t['Auth.RESET_PASSWORD_SUCCESS'] = 'Your password has been changed.';
$_t['Auth.RESET_PASSWORD_SUBTITLE'] = 'Enter your new password.';
$_t['Auth.SEND'] = 'Send';
$_t['Auth.USER_ADMIN_TITLE'] = 'Users';
$_t['Auth.USER_ADMIN_SUBTITLE'] = 'All user accounts of this application.';
$_t['Auth.USER_NEW'] = 'New User';
$_t['Auth.GROUP_ADMIN_TITLE'] = 'User Groups';
$_t['Auth.GROUP_ADMIN_SUBTITLE'] = 'Groups for managing user permissions.';
$_t['Auth.GROUP_NEW'] = 'New User Group';
$_t['Auth.SOCIAL_REGISTER'] = 'You do not need to register if you have an account on any of these websites:';
$_t['Auth.SOCIAL_LOGIN'] = 'Or log in through a social network';
$_t['Auth.SOCIAL_VK'] = 'VK';
$_t['Auth.SOCIAL_ATTACH_FAILED'] = 'You have not used that social network for log on yet either.';
$_t['Auth.SOCIAL_FIRST_LOGIN'] = 'You are logging in with %s for the first time.';
$_t['Auth.SOCIAL_NEW_USER'] = 'I do not have an account on this website yet.';
$_t['Auth.SOCIAL_EXISTING_USER'] = 'I already have an account on this website.';
$_t['Auth.SOCIAL_EXISTING_USER_FORM'] = 'Log into your account through other means, so we may remember these accounts to be the same.';
$_t['Auth.SOCIAL_CANCEL'] = 'Cancel';
$_t['Auth.SOCIAL_NEW_USER_OK'] = 'Create an Account';
$_t['Auth.SOCIAL_ACCOUNTS_LIST'] = 'Login via Social Networks';
$_t['Auth.SOCIAL_ACCOUNTS'] = 'You can log into your account through any of these social networks:';
$_t['Auth.SOCIAL_NO_ACCOUNTS'] = 'You have not set up login via any social networks yet.';
$_t['Auth.SOCIAL_AVAILABLE'] = 'Add login via:';
$_t['Auth.SOCIAL_REMOVE'] = 'Remove';

$_t['POLICY.auth.admin'] = 'User and Group Control';
$_t['POLICY.auth.admin.ro'] = 'View';
$_t['POLICY.auth.admin.rw'] = 'Change';
$_t['POLICY.auth.admin.force_login'] = '<span class="label label-important">DANGEROUS!</span> Log in as any non-superuser';

$_t['LOCK_REASON.BRIEF.default'] = 'ban';
$_t['LOCK_REASON.FULL.default'] = 'Your account has been banned from this website.';
$_t['LOCK_REASON.BRIEF.email_validation'] = 'email validation';
$_t['LOCK_REASON.FULL.email_validation'] = 'You need to verify your e-mail address before you can login.  Check your mail.';

$_tJS['Auth.LOCK_DIALOG'] = 'Lock User';
