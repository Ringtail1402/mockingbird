<?php
/**
 * Change password page.
 *
 * @var string $form
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\LinkHelpers $link
 */
?>

<?php $view->extend() ?>

<?php $view->setSlot('title', _t('Auth.CHANGE_PASSWORD')) ?>

<form class="hero-unit mini-form" action="<?php echo $link->url('auth.change_password') ?>" method="post">
  <?php echo $form ?>

    <div class="form-horizontal"><fieldset><div class="control-group" style="margin: 0;"><div class="controls">
        <input type="submit" class="btn btn-primary" value="<?php echo _t('Auth.SAVE') ?>">
    </div></div></fieldset></div>
</form>