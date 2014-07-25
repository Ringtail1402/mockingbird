<?php
/**
 * "Remember Me" checkbox template.
 *
 * @var \Silex\Application $app
 */
?>
<?php if (!empty($app['Auth']['features']['remember_me']) && !empty($app['Auth']['remember_me_age'])): ?>
<div class="form-horizontal"><fieldset><div class="control-group"><div class="controls">
  <label class="checkbox">
    <input type="checkbox" name="remember"<?php if ($app['request']->get('remember')) echo ' checked' ?>>
    <?php echo _t('Auth.REMEMBER_ME') ?>
  </label>
</div></div></fieldset></div>
<?php endif; ?>