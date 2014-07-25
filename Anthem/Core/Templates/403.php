<?php // 403 error handler. ?>

<?php $view->extend() ?>
<?php $view->setSlot('title', '403') ?>
<?php $view->setSlot('subtitle', _t('Core.403')) ?>

<div class="error-page">
  <h1>:-(</h1>
  <h2><?php echo _t('Core.403_TEXT') ?></h2>
</div>