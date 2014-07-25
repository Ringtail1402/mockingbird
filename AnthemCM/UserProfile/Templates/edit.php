<?php
/**
 * Profile edit form.
 *
 * @var \AnthemCM\UserProfile\Form\UserProfileForm $form
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\AssetHelpers $asset
 */
?>

<?php $view->extend('Anthem/Admin:layout.php') ?>
<?php $view->setSlot('title', _t('UserProfile.USER_PROFILE_EDIT')) ?>

<form id="form-container" method="POST" action="<?php echo $link->url('user_profile.edit') ?>">
  <?php echo $form->render() ?>
</form>

<?php $view->beginSlot('footer') ?>
  <div class="form-horizontal">
    <div id="form-links-container" class="form-actions" style="margin-top: -15px;">
      <a class="btn btn-primary" onclick="$('#form-container').submit(); return false;"><i class="icon-ok icon-white"></i> <?php echo _t('Admin.SAVE') ?></a>
    </div>
  </div>
<?php $view->endSlotAppend() ?>
