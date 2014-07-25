<?php
/**
 * Mockingbird dashboard template.
 *
 * @var \Core\View\ViewHelpers $view
 * @var \Core\View\AssetHelpers $asset
 */
?>

<?php $view->extend('Anthem/Admin:layout.php') ?>
<?php $view->setSlot('title', $app['Core']['project']) ?>
<?php $view->setSlot('subtitle', _t('DASHBOARD_SUBTITLE')) ?>

<?php // CSS and Javascript includes ?>
<?php $view->beginSlot('head') ?>
  <script type="text/javascript" src="<?php echo $asset->js('Mockingbird:dashboard.js') ?>"></script>
  <?php echo $tour->init('mockingbird') ?>
<?php $view->endSlotAppend() ?>

<div class="row-fluid">
  <div class="span4" id="accounts-container"></div>
  <div class="span8" id="calendar-container"></div>
</div>
