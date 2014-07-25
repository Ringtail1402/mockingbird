<?php

namespace Anthem\Auth;

/**
 * Auth events.
 */
final class AuthEvents
{
  // An event which checks that a user is logged in via any mechanisms.
  const LOGIN_CHECK       = 1;

  // "Manual" login event (e.g. when user explicitly typed in email and password).
  const LOGIN_MANUAL      = 2;

  // "Automatic" login event (e.g. when user id was stored in session or user has "remember me" cookie set)
  const LOGIN_AUTO        = 3;

  // Logout event.  This will not fire in case of session expiration.
  const LOGOUT            = 4;

  // Successful user registration event.
  const USER_REGISTER     = 5;

  // Successful attaching of a social account recort to user.
  const USER_ATTACH_SOCIAL = 6;
}