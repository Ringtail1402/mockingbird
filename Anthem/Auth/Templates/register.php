<?php
/**
 * Register page.
 *
 * @var string $form
 * @var string $redir
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\LinkHelpers $link
 */
?>

<?php $view->extend() ?>

<?php $view->setSlot('title', _t('Auth.REGISTER')) ?>

<form class="hero-unit mini-form" action="<?php echo $link->url('auth.register') . ($redir ? '?redir=' . urlencode($redir) : '') ?>" method="post">
  <?php echo $form ?>

  <div class="form-horizontal"><fieldset><div class="control-group" style="margin: 0;"><div class="controls">
      <input type="submit" class="btn btn-primary" value="<?php echo _t('Auth.REGISTER') ?>">
  </div></div></fieldset></div>

  <?php if (count($social) && !empty($app['Auth']['features']['social_accounts'])): ?>
    <?php echo $view->sub('Anthem/Auth:social/links.php', array('social' => $social, 'message' => _t('Auth.SOCIAL_REGISTER'))) ?>
  <?php endif; ?>
</form>