<?php
/**
 * Login page.
 *
 * @var string $form
 * @var string $redir
 * @var array  $social
 * @var boolean $https_login
 * @var \Anthem\Core\View\ViewHelpers  $view
 * @var \Anthem\Core\View\LinkHelpers  $link
 * @var \Anthem\Core\View\AssetHelpers $asset
 */
?>

<?php $view->extend() ?>

<?php $view->setSlot('title', $redir ? _t('Auth.LOGIN_REDIR') : _t('Auth.LOGIN')) ?>

<?php $post_action = $link->url('auth.login', array(), true) . ($redir ? '?redir=' . urlencode($redir) : '') ?>
<?php if ($https_login) $post_action = preg_replace('/^http:/', 'https:', $post_action) ?>

<form class="hero-unit mini-form" action="<?php echo $post_action ?>" method="post">
  <?php echo $form ?>

  <?php echo $view->sub('Anthem/Auth:remember_me.php') ?>

  <div class="form-horizontal"><fieldset><div class="control-group" style="margin: 0;"><div class="controls">
    <input type="submit" class="btn btn-primary" value="<?php echo _t('Auth.LOGIN') ?>">
    <?php if (!empty($app['Auth']['features']['registration'])): ?>
      <a class="btn" href="<?php echo $link->url('auth.register') ?>"><?php echo _t('Auth.REGISTER') ?></a>
    <?php endif; ?>
    <?php if (!empty($app['Auth']['features']['password_recovery'])): ?>
      <a class="btn" href="<?php echo $link->url('auth.request_password') ?>"><?php echo _t('Auth.FORGOT_PASSWORD') ?></a>
    <?php endif; ?>
  </div></div></fieldset></div>

  <?php if (count($social) && !empty($app['Auth']['features']['social_accounts'])): ?>
    <?php echo $view->sub('Anthem/Auth:social/links.php', array('social' => $social, 'message' => _t('Auth.SOCIAL_LOGIN'))) ?>
  <?php endif; ?>
</form>