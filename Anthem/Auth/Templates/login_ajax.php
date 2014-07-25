<?php
/**
 * AJAX login form.
 *
 * @var string $form
 * @var boolean $https_login
 * @var \Anthem\Core\View\ViewHelpers  $view
 * @var \Anthem\Core\View\LinkHelpers  $link
 * @var \Anthem\Core\View\AssetHelpers $asset
 */
?>
<?php echo $form ?>

<?php echo $view->sub('Anthem/Auth:remember_me.php') ?>

<?php $post_action = $link->url('auth.login.ajax', array(), true) ?>
<?php if ($https_login) $post_action = preg_replace('/^http:/', 'https:', $post_action) ?>
<input type="hidden" id="ajax-login-action" value="<?php echo htmlspecialchars($post_action) ?>">

<div class="form-horizontal"><fieldset>
  <div class="control-group"><div class="controls">
    <button id="login-button" class="btn btn-primary" style="margin: 0;" onclick="return false;"><?php echo _t('Auth.LOGIN') ?></button>
    <?php if (count($social) && !empty($app['Auth']['features']['social_accounts'])): ?>
      <?php echo $view->sub('Anthem/Auth:social/links_mini.php', array('social' => $social)) ?>
    <?php endif; ?>
    <img id="login-spinner" style="display: none;" src="<?php echo $asset->image('Anthem/Auth:authspinner.gif') ?>">
  </div></div>
  <?php if (!empty($app['Auth']['features']['password_recovery'])): ?>
    <div class="control-group"><div class="controls">
      <a href="<?php echo $link->url('auth.request_password') ?>"><?php echo _t('Auth.FORGOT_PASSWORD') ?></a>
    </div></div>
  <?php endif; ?>
</fieldset></div>

