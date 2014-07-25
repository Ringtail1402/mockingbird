<?php
/**
 * First login through social network account page.
 *
 * @var string $form
 * @var array  $social
 * @var string $provider_id
 * @var string $provider_title
 * @var string $provider_icon
 * @var string $user_display_name
 * @var \Anthem\Core\View\ViewHelpers  $view
 * @var \Anthem\Core\View\LinkHelpers  $link
 * @var \Anthem\Core\View\AssetHelpers $asset
 */
?>

<?php $view->extend() ?>

<?php $view->setSlot('title', _t('Auth.LOGIN')) ?>

<form class="hero-unit small-form" action="<?php echo $link->url('auth.social.first_login', array('provider' => $provider_id)) ?>" method="post">
  <h3 style="margin: 0;">
    <?php echo htmlspecialchars($user_display_name) ?>
  </h3>
  <p style="margin: 0 0 15px 0; font-size: 12px; line-height: 16px;">
    <img style="vertical-align: top;" src="<?php echo $asset->image($provider_icon) ?>" title="<?php echo $provider_title ?>">
    <?php echo $provider_title ?>
  </p>

  <p><?php echo _t('Auth.SOCIAL_FIRST_LOGIN', $provider_title) ?></p>

  <label class="radio" style="margin: 10px 0 10px 0;">
    <input type="radio" name="new_user" value="1">
    <?php echo _t('Auth.SOCIAL_NEW_USER') ?>
  </label>

  <label class="radio" style="margin: 10px 0 30px 0;">
    <input type="radio" name="new_user" value="0">
    <?php echo _t('Auth.SOCIAL_EXISTING_USER') ?>
  </label>

  <script type="text/javascript">
    $('input[name=new_user]').click(function () {
      if ($(this).val() == '1')
      {
        $('#existing-user').hide();
        $('#new-user').show();
        $('#cancel-button').show();
      }
      else
      {
        $('#new-user').hide();
        $('#existing-user').show();
        $('#cancel-button').hide();
      }
    });
  </script>

  <div id="existing-user" style="display: none;">
    <p><?php echo _t('Auth.SOCIAL_EXISTING_USER_FORM') ?></p>

    <?php echo $form ?>

    <?php echo $view->sub('Anthem/Auth:remember_me.php') ?>

    <div class="form-horizontal"><fieldset><div class="control-group" style="margin: 0;"><div class="controls">
      <input type="submit" class="btn btn-primary" value="<?php echo _t('Auth.LOGIN') ?>">
      <a class="btn" href="<?php echo $link->url('auth.login') ?>"><?php echo _t('Auth.SOCIAL_CANCEL') ?></a>
    </div></div></fieldset></div>

    <?php if (count($social)): ?>
      <?php echo $view->sub('Anthem/Auth:social/links.php', array('social' => $social, 'message' => _t('Auth.SOCIAL_LOGIN'))) ?>
    <?php endif; ?>
  </div>

  <input type="submit" id="new-user" class="btn btn-primary" style="display: none;" value="<?php echo _t('Auth.SOCIAL_NEW_USER_OK') ?>">
  <a class="btn" id="cancel-button" href="<?php echo $link->url('auth.login') ?>"><?php echo _t('Auth.SOCIAL_CANCEL') ?></a>
</form>
