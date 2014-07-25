<?php

namespace Anthem\Auth;

use Silex\Application;
use Anthem\Core\Core;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Anthem\Core\Module;
use Anthem\Propel\Fixtures\FixtureProviderInterface;
use Anthem\Auth\Auth;
use Anthem\Auth\EventHandler\SessionLoginCheckHandler;
use Anthem\Auth\EventHandler\SessionLoginHandler;
use Anthem\Auth\EventHandler\SessionLogoutHandler;
use Anthem\Auth\EventHandler\RememberMeLoginCheckHandler;
use Anthem\Auth\EventHandler\RememberMeLoginHandler;
use Anthem\Auth\EventHandler\RememberMeLogoutHandler;
use Anthem\Auth\EventHandler\SocialAccountAttachLoginHandler;
use Anthem\Auth\ModelService\UserService;
use Anthem\Auth\ModelService\GroupService;
use Anthem\Auth\ModelService\UserKeyService;
use Anthem\Auth\ModelService\UserSocialAccountService;
use Anthem\Auth\Social\VKProvider;
use Anthem\Auth\Social\GoogleProvider;
use Anthem\Auth\Controller\AuthController;
use Anthem\Auth\Controller\SocialAuthController;
use Anthem\Auth\Admin\UserAdmin;
use Anthem\Auth\Admin\GroupAdmin;

/**
 * Users and groups support module.
 */
class AuthModule extends Module
                 implements ServiceProviderInterface,
                            ControllerProviderInterface,
                            FixtureProviderInterface
{
  /**
   * Registers services.
   *
   * @param \Anthem\Core\Core $app
   */
  public function register(Application $app)
  {
    // Central auth service
    // FIXME: this is not a correct place to detect CLI!!!
    $app['auth'] = $app->share(function () use ($app) { return new Auth($app, php_sapi_name() == 'cli'); });

    // Model services
    $app['auth.model.user']  = $app->share(function () use ($app) { return new UserService(); });
    $app['auth.model.group'] = $app->share(function () use ($app) { return new GroupService(); });
    $app['auth.model.user_key'] = $app->share(function () use ($app) {
      return new UserKeyService($app['Auth']['hash'], $app['Core']['project']);
    });
    $app['auth.model.user_social_account'] = $app->share(function () use ($app) { return new UserSocialAccountService(); });

    // Event handlers
    $app['auth.event.login_check.session'] = $app->share(function () use ($app) {
      return new SessionLoginCheckHandler($app['auth.model.user']);
    });
    $app['auth.event.login_check.remember_me'] = $app->share(function () use ($app) {
      return new RememberMeLoginCheckHandler($app['auth.model.user_key'],
                                             !empty($app['Auth']['features']['remember_me']) ? $app['Auth']['remember_me_age'] : 0);
    });
    $app['auth.event.login.session'] = $app->share(function () use ($app) {
      return new SessionLoginHandler($app['session']);
    });
    $app['auth.event.login.remember_me'] = $app->share(function () use ($app) {
      return new RememberMeLoginHandler($app['request'],
                                        $app['auth.model.user_key'],
                                        !empty($app['Auth']['features']['remember_me']) ? $app['Auth']['remember_me_age'] : 0,
                                        // FIXME: this check should be done by Auth::needSecureCookies(), but Auth needs this already set
                                        $app['Auth']['https'] == 'always' || $app['Auth']['https'] == 'auth',
                                        $app['dispatcher']);
    });
    $app['auth.event.logout.session'] = $app->share(function () use ($app) {
      return new SessionLogoutHandler($app['session']);
    });
    $app['auth.event.logout.remember_me'] = $app->share(function () use ($app) {
      return new RememberMeLogoutHandler($app['request'], $app['auth.model.user_key'], $app['dispatcher']);
    });
    $app['auth.event.login.social_account_attach'] = $app->share(function () use ($app) {
      return new SocialAccountAttachLoginHandler($app, $app['session'], $app['dispatcher']);
    });

    // Social auth providers
    $app['auth.social.vk'] = $app->share(function () use ($app) {
      return new VKProvider($app['session'], $app['url_generator'], 'auth.social.vk', $app['Auth']['social_auth']['auth.social.vk']);
    });
    $app['auth.social.google'] = $app->share(function () use ($app) {
      return new GoogleProvider($app['session'], $app['url_generator'], 'auth.social.google', $app['Auth']['social_auth']['auth.social.google']);
    });

    // Controllers
    $app['auth.controller']        = $app->share(function () use ($app) { return new AuthController($app); });
    $app['auth.social.controller'] = $app->share(function () use ($app) { return new SocialAuthController($app); });

    // Admin pages
    $app['auth.admin.users']  = $app->share(function () use ($app) {
      return new UserAdmin($app['auth.model.user'], $app);
    });
    $app['auth.admin.groups'] = $app->share(function () use ($app) {
      return new GroupAdmin($app['auth.model.group'], $app);
    });

    $this->setupHTTPS($app);
  }

  /**
   * Returns routes to connect to the given application.
   *
   * @param  Application $app
   * @return ControllerCollection
   */
  public function connect(Application $app)
  {
    $controllers = new ControllerCollection();

    $controllers->match('/login/force',
      function(Request $request) use($app) { return $app['auth.controller']->loginForceAction($request); }
    )->bind('auth.login.force');
    $controllers->match('/login/ajax',
      function(Request $request) use($app) { return $app['auth.controller']->loginAjaxAction($request); }
    )->bind('auth.login.ajax');
    $controllers->match('/login',
      function(Request $request) use($app) { return $app['auth.controller']->loginAction($request); }
    )->bind('auth.login');
    $controllers->match('/logout',
      function(Request $request) use($app) { return $app['auth.controller']->logoutAction($request); }
    )->bind('auth.logout');
    $controllers->match('/register',
      function(Request $request) use($app) { return $app['auth.controller']->registerAction($request); }
    )->bind('auth.register');
    $controllers->match('/register/validation',
      function(Request $request) use($app) { return $app['auth.controller']->registerEmailValidationNeededAction($request); }
    )->bind('auth.register.email_validation_needed');
    $controllers->match('/register/validate',
      function(Request $request) use($app) { return $app['auth.controller']->registerEmailValidate($request); }
    )->bind('auth.register.validate');
    $controllers->match('/change_email',
      function(Request $request) use($app) { return $app['auth.controller']->changeEmailAction($request); }
    )->bind('auth.change_email');
    $controllers->match('/change_email/validate',
      function(Request $request) use($app) { return $app['auth.controller']->changeEmailValidateAction($request); }
    )->bind('auth.change_email.validate');
    $controllers->match('/change_password',
      function(Request $request) use($app) { return $app['auth.controller']->changePasswordAction($request); }
    )->bind('auth.change_password');
    $controllers->match('/request_password',
      function(Request $request) use($app) { return $app['auth.controller']->requestPasswordAction($request); }
    )->bind('auth.request_password');
    $controllers->match('/reset_password',
      function(Request $request) use($app) { return $app['auth.controller']->resetPasswordAction($request); }
    )->bind('auth.reset_password');

    $controllers->get('/social/{provider}/login',
      function(Request $request, $provider) use ($app) { return $app['auth.social.controller']->promptAction($request, $provider); }
    )->bind('auth.social.prompt');
    $controllers->match('/social/{provider}/auth',
      function(Request $request, $provider) use ($app) { return $app['auth.social.controller']->authAction($request, $provider); }
    )->bind('auth.social.auth');
    $controllers->match('/social/{provider}/first_login',
      function(Request $request, $provider) use ($app) { return $app['auth.social.controller']->firstLoginAction($request, $provider); }
    )->bind('auth.social.first_login');
    $controllers->get('/social_logins',
      function(Request $request) use ($app) { return $app['auth.social.controller']->listSocialAccountsAction($request); }
    )->bind('auth.social.list_social_accounts');
    $controllers->post('/social/{provider}/delete',
      function(Request $request, $provider) use ($app) { return $app['auth.social.controller']->deleteAction($request, $provider); }
    )->bind('auth.social.delete');

    return $controllers;
  }

  /**
   * Returns an array of class names implementing FixtureInterface in this module.
   *
   * @return string[]
   */
  public function getFixtureClasses()
  {
    return array('Anthem\\Auth\\Fixtures\\AuthFixtures');
  }

  /**
   * Sets up http/https handling.
   *
   * @param \Silex\Application $app
   * @return void
   */
  protected function setupHTTPS($app)
  {
    // Make session honor HTTPS settings
    $session_param = isset($app['session.storage.options']) ? $app['session.storage.options'] : array();
    $session_param['httponly'] = true;
    // FIXME: this check should be done by Auth::needSecureCookies(), but Auth needs this already set
    $session_param['secure'] = $app['Auth']['https'] == 'always' || $app['Auth']['https'] == 'auth';
    $app['session.storage.options'] = $session_param;

    // On 'auth' https setting, downgrade to HTTP for non-authorized users on pages other than
    // authorization pages.
    if ($app['Auth']['https'] == 'auth')
    {
      $app->before(function () use ($app) {
        if ($app['request']->isSecure() &&
            strpos($app['request']->attributes->get('_route'), 'auth.') !== 0 &&
            !strpos($app['request']->attributes->get('_route'), '.ignorehttps') &&
            $app['auth']->isGuest())
          $app['auth']->redirectHTTPS(false);
      });
    }
  }
}
