<?php
/**
 * Settings UI.
 *
 * @var boolean $ro
 * @var boolean $global
 * @var array $pages
 * @var string $page
 * @var \Anthem\Settings\Form\SettingsForm $form
 * @var boolean $valid
 * @var boolean $is_ajax
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\AssetHelpers $asset
 */
?>

<?php if (!$is_ajax): ?>
<?php $view->extend('Anthem/Admin:layout.php') ?>
<?php $view->setSlot('title', $global ? _t('Settings.SETTINGS_GLOBAL') : _t('Settings.SETTINGS')) ?>
<?php $view->setSlot('subtitle', $global ? _t('Settings.SETTINGS_GLOBAL_SUBTITLE') : '') ?>

<?php $view->beginSlot('head') ?>
  <script type="text/javascript" src="<?php echo $asset->js('Anthem/Settings:settings_admin.js') ?>"></script>
  <?php if (!empty($app['Settings']['extra_css'])): ?>
    <?php foreach ($app['Settings']['extra_css'] as $css): ?>
      <link type="text/css" rel="stylesheet" href="<?php echo $asset->css($css) ?>">
    <?php endforeach; ?>
  <?php endif; ?>
  <?php if (!empty($app['Settings']['extra_js'])): ?>
    <?php foreach ($app['Settings']['extra_js'] as $js): ?>
      <script type="text/javascript" src="<?php echo $asset->js($js) ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
<?php $view->endSlotAppend() ?>

<?php if (!$ro): ?>
  <?php $view->beginSlot('footer') ?>
    <div class="form-horizontal">
        <div id="form-links-container" class="form-actions" style="margin-top: -15px;">
            <a class="btn btn-primary" onclick="Settings.save(<?php echo $global ? 'true' : 'false' ?>); return false;"><i class="icon-ok icon-white"></i> <?php echo _t('Admin.SAVE') ?></a>
        </div>
    </div>
  <?php $view->endSlotAppend() ?>
<?php endif; ?>

<form id="form-container" style="display: none;">
<?php endif; // !$is_ajax ?>

<ul class="nav nav-tabs"<?php if (!empty($app['Settings']['flatten'])) echo ' style="display: none;"' ?>>
  <?php foreach ($pages as $_page => $title): ?>
    <li data-page="<?php echo $_page ?>"<?php if ($page == $_page) echo ' class="active"' ?>>
      <a href="#<?php if ($_page) echo 'page=' . $_page ?>" onclick="Settings.load(<?php echo $global ? 'true' : 'false' ?>, '<?php echo $_page ?>'); return false;"><?php echo $title ?></a>
    </li>
  <?php endforeach; ?>
</ul>

<?php echo $form->render() ?>

<input type="hidden" id="is-valid" value="<?php echo (int)$valid ?>">

<?php if (!$is_ajax): ?>
</form>
<?php endif; // !$is_ajax ?>