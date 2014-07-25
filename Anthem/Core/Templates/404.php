<?php // 404 error handler. ?>

<?php $view->extend() ?>
<?php $view->setSlot('title', '404') ?>
<?php $view->setSlot('subtitle', _t('Core.404')) ?>

<div class="error-page">
  <h1>:-(</h1>
  <h2><?php echo _t('Core.404_TEXT') ?></h2>
</div>