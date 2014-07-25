<?php
/**
 * Request password recovert page.
 *
 * @var string $form
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\LinkHelpers $link
 */
?>

<?php $view->extend() ?>

<?php $view->setSlot('title', _t('Auth.REQUEST_PASSWORD')) ?>

<form class="hero-unit mini-form" action="<?php echo $link->url('auth.request_password') ?>" method="post">
  <p><?php echo _t('Auth.REQUEST_PASSWORD_SUBTITLE') ?></p>

  <?php echo $form ?>

    <div class="form-horizontal"><fieldset><div class="control-group" style="margin: 0;"><div class="controls">
        <input type="submit" class="btn btn-primary" value="<?php echo _t('Auth.SEND') ?>">
    </div></div></fieldset></div>
</form>