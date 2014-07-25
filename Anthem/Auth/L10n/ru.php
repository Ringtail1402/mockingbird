<?php

$_t['Auth.EMAIL'] = 'E-mail';
$_t['Auth.EMAIL_HELP'] = 'Должен быть уникальным.  Используется в качестве основного логина.';
$_t['Auth.PASSWORD'] = 'Пароль';
$_t['Auth.PASSWORD_HELP'] = 'Пароль хранится в зашифрованном виде.  Он не может быть выведен, но может быть изменен.';
$_t['Auth.PASSWORD2'] = 'Повторите пароль';
$_t['Auth.GROUPS'] = 'Группы';
$_t['Auth.GROUPS_HELP'] = 'Группы позволяют назначать одни и те же разрешения нескольким пользователям.  Пользователь ' .
    'наследует разрешения от всех групп, которым принадлежит.';
$_t['Auth.IS_SUPERUSER'] = 'Суперполь&shy;зователь';
$_t['Auth.IS_SUPERUSER_HELP'] = '<span class="label label-important">ВНИМАНИЕ!</span>  Суперпользователи всегда могут '.
    'выполнить <b>любое</b> возможное действие на сайте.  Только суперпользователь может создавать или изменять суперпользователей.';
$_t['Auth.LOCKED'] = 'Блокировка';
$_t['Auth.LOCKED_HELP'] = 'Если установлено, пользователь не сможет войти в приложение (и будет принудительно ' .
    'разлогинен, если он уже вошел).  Пользователю будет показано соответствующее сообщение об ошибке.';
$_t['Auth.NOT_LOCKED'] = 'Нет';
$_t['Auth.LOCK'] = 'Бан';
$_t['Auth.REASON'] = 'Причина';
$_t['Auth.UNLOCK'] = 'Разблокировать';
$_t['Auth.POLICIES'] = 'Разрешения';
$_t['Auth.POLICY_ENABLE'] = 'Есть';
$_t['Auth.POLICY_DISABLE'] = 'Нет';
$_t['Auth.POLICY_ENABLE_INHERITED'] = 'Есть (по умолч.)';
$_t['Auth.POLICY_DISABLE_INHERITED'] = 'Нет (по умолч.)';
$_t['Auth.CREATED_AT'] = 'Зарегистрирован';
$_t['Auth.LAST_LOGIN'] = 'Последний вход';
$_t['Auth.TITLE'] = 'Название';
$_t['Auth.NUM_USERS'] = 'Пользователей';
$_t['Auth.LOGIN'] = 'Войти';
$_t['Auth.LOGIN_REDIR'] = 'Для совершения этого действия необходимо войти';
$_t['Auth.REMEMBER_ME'] = 'Запомнить меня';
$_t['Auth.LOGOUT'] = 'Выход';
$_t['Auth.REGISTER'] = 'Регистрация';
$_t['Auth.REGISTER_EMAIL_VALIDATION'] = 'Регистрация на %s';
$_t['Auth.REGISTER_EMAIL_VALIDATION_NEEDED'] = 'Проверка e-mail';
$_t['Auth.REGISTER_EMAIL_VALIDATION_NEEDED_SUBTITLE'] = 'Вы успешно зарегистрировались, но нам нужно убедиться, ' .
    'что ваш адрес e-mail действительно принадлежит вам.  На адрес %s отправлено письмо со ссылкой на подтверждение e-mail. ' .
    'Проверьте свою почту.';
$_t['Auth.REGISTER_EMAIL_VALIDATION_MAIL_BODY1'] = <<<EOF
Вы только что зарегистрировались на сайте <a href="%s">%s</a>.  Чтобы подтвердить свой e-mail и завершить регистрацию,
щелкните на этой ссылке либо скопируйте и вставьте ее в адресную строку:
EOF;
$_t['Auth.REGISTER_EMAIL_VALIDATION_MAIL_BODY2'] = <<<EOF
Если вы не регистрировались на этом сайте, можете удалить это письмо.
EOF;
$_t['Auth.REGISTER_EMAIL_VALIDATION_INVALID_KEY'] = 'Неправильная или слишком старая ссылка подтверждения e-mail.  Вам придется зарегистрироваться снова.';
$_t['Auth.REGISTER_EMAIL_VALIDATION_SUCCESS'] = 'Вы успешно подтвердили свой e-mail.';
$_t['Auth.CHANGE_EMAIL'] = 'Смена e-mail';
$_t['Auth.CHANGE_EMAIL_MAIL_BODY1'] = <<<EOF
Вы запросили смену адреса e-mail на сайте <a href="%s">%s</a>.  Чтобы подтвердить, что этот адрес e-mail
принадлежит вам, щелкните на этой ссылке либо скопируйте и вставьте ее в адресную строку:
EOF;
$_t['Auth.CHANGE_EMAIL_MAIL_BODY2'] = <<<EOF
Если вы не меняли свой e-mail на этом сайте, можете удалить это письмо.
EOF;
$_t['Auth.CHANGE_EMAIL_NOTICE'] = 'На адрес %s отправлено письмо со ссылкой на подтверждение данного адреса.  Проверьте свою почту.';
$_t['Auth.CHANGE_EMAIL_INVALID_KEY'] = 'Неправильная или слишком старая ссылка подтверждения e-mail.  Попробуйте сменить e-mail еще раз.';
$_t['Auth.CHANGE_EMAIL_SUCCESS'] = 'Ваш адрес e-mail изменен.';
$_t['Auth.CHANGE_PASSWORD'] = 'Смена пароля';
$_t['Auth.SAVE'] = 'Сохранить';
$_t['Auth.LOGIN_AS_USER'] = 'Войти в аккаунт';
$_t['Auth.LOGIN_AS_USER_CONFIRM'] = 'Вы автоматически выйдете из своего текущего аккаунта, войдете под аккаунтом ' .
    'этого пользователя без ввода пароля, и будете перенаправлены на главную страницу.';
$_t['Auth.TO_USERS'] = 'К пользователям';
$_t['Auth.AUTH_VALIDATOR_MESSAGE'] = 'Неверный e-mail или пароль.';
$_t['Auth.UNIQUE_EMAIL_VALIDATOR_MESSAGE'] = 'Пользователь с таким e-mail уже существует.';
$_t['Auth.UNIQUE_GROUP_TITLE_VALIDATOR_MESSAGE'] = 'Группа с таким названием уже существует.';
$_t['Auth.PASSWORD_EQUALITY_VALIDATOR_MESSAGE'] = 'Пароли должны совпадать.';
$_t['Auth.USER_VALIDATOR_MESSAGE'] = 'Пользователя с таким e-mail не существует.';
$_t['Auth.FORGOT_PASSWORD'] = 'Забыли пароль?';
$_t['Auth.REQUEST_PASSWORD'] = 'Восстановление пароля';
$_t['Auth.REQUEST_PASSWORD_SUBTITLE'] = 'Введите адрес e-mail, который вы использовали при регистрации на этом сайте. ' .
    'На этот адрес будет отправлено письмо со ссылкой на восстановление пароля.';
$_t['Auth.REQUEST_PASSWORD_EMAIL_NONEXISTENT_VALIDATOR_MESSAGE'] = 'Пользователь с таким e-mail не зарегистрирован.';
$_t['Auth.REQUEST_PASSWORD_EMAIL_INVALID_VALIDATOR_MESSAGE'] = 'Указанный пользователь существует, но не имеет реального e-mail. ' .
    'Восстановить пароль этим методом невозможно.';
$_t['Auth.REQUEST_PASSWORD_MAIL'] = 'Восстановление пароля на сайте %s';
$_t['Auth.REQUEST_PASSWORD_NOTICE'] = 'На адрес %s отправлено письмо со ссылкой на восстановление пароля.  Проверьте свою почту.';
$_t['Auth.REQUEST_PASSWORD_MAIL_BODY1'] = <<<EOF
Вы запросили восстановление пароля на сайте <a href="%s">%s</a>.  Чтобы поменять свой пароль, щелкните на этой ссылке либо
скопируйте и вставьте ее в адресную строку:
EOF;
$_t['Auth.REQUEST_PASSWORD_MAIL_BODY2'] = <<<EOF
Если вы не запрашивали восстановление пароля, можете удалить это письмо.
EOF;
$_t['Auth.RESET_PASSWORD'] = 'Сброс пароля';
$_t['Auth.RESET_PASSWORD_INVALID_KEY'] = 'Неправильная или слишком старая ссылка восстановления пароля.  Запросите восстановление еще раз.';
$_t['Auth.RESET_PASSWORD_SUCCESS'] = 'Ваш пароль был изменен.';
$_t['Auth.RESET_PASSWORD_SUBTITLE'] = 'Введите ваш новый пароль.';
$_t['Auth.SEND'] = 'Отправить';
$_t['Auth.USER_ADMIN_TITLE'] = 'Пользователи';
$_t['Auth.USER_ADMIN_SUBTITLE'] = 'Все учетные записи этого приложения.';
$_t['Auth.USER_NEW'] = 'Новый пользователь';
$_t['Auth.GROUP_ADMIN_TITLE'] = 'Группы пользователей';
$_t['Auth.GROUP_ADMIN_SUBTITLE'] = 'Группы для удобного управления разрешениями учетных записей.';
$_t['Auth.GROUP_NEW'] = 'Новая группа пользователей';
$_t['Auth.SOCIAL_REGISTER'] = 'Вам не обязательно регистрироваться, если у вас есть аккаунт на любом из этих сайтов:';
$_t['Auth.SOCIAL_LOGIN'] = 'Или войдите через один из этих сайтов';
$_t['Auth.SOCIAL_VK'] = 'Вконтакте';
$_t['Auth.SOCIAL_ATTACH_FAILED'] = 'Вы не пользовались выбранным сервисом ранее для входа на этот сайт.';
$_t['Auth.SOCIAL_FIRST_LOGIN'] = 'Вы входите на сайт через %s впервые.';
$_t['Auth.SOCIAL_NEW_USER'] = 'У меня еще нет аккаунта на этом сайте.';
$_t['Auth.SOCIAL_EXISTING_USER'] = 'Я уже входил на этот сайт раньше.';
$_t['Auth.SOCIAL_EXISTING_USER_FORM'] = 'Войдите в свой аккаунт другим способом, чтобы мы запомнили, что оба аккаунта принадлежат вам.';
$_t['Auth.SOCIAL_CANCEL'] = 'Отмена';
$_t['Auth.SOCIAL_NEW_USER_OK'] = 'Создать аккаунт';
$_t['Auth.SOCIAL_ACCOUNTS_LIST'] = 'Вход через социальные сети';
$_t['Auth.SOCIAL_ACCOUNTS'] = 'Вы можете войти в свой аккаунт через следующие аккаунты социальных сетей:';
$_t['Auth.SOCIAL_NO_ACCOUNTS'] = 'Вы пока не настроили вход ни через одну социальную сеть.';
$_t['Auth.SOCIAL_AVAILABLE'] = 'Добавить вход через:';
$_t['Auth.SOCIAL_REMOVE'] = 'Отменить';

$_t['POLICY.auth.admin'] = 'Управление пользователями и группами';
$_t['POLICY.auth.admin.ro'] = 'Просмотр';
$_t['POLICY.auth.admin.rw'] = 'Изменение';
$_t['POLICY.auth.admin.force_login'] = '<span class="label label-important">ОПАСНО!</span> Вход под аккаунтом любого пользователя (кроме суперпользователей)';

$_t['LOCK_REASON.BRIEF.default'] = 'бан';
$_t['LOCK_REASON.FULL.default'] = 'Ваш аккаунт был забанен на этом сайте.';
$_t['LOCK_REASON.BRIEF.email_validation'] = 'проверка e-mail';
$_t['LOCK_REASON.FULL.email_validation'] = 'Вам нужно подтвердить ваш адрес e-mail, прежде чем вы сможете войти.  Проверьте почту.';

$_tJS['Auth.LOCK_DIALOG'] = 'Блокировка пользователя';
