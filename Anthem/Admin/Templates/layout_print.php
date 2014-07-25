<!DOCTYPE HTML>
<?php
/**
 * Admin layout template for printing.
 *
 * @var \Silex\Application $app
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\AssetHelpers $asset
 * @var \Anthem\Core\View\LinkHelpers $link
 * @var \Anthem\Admin\View\AdminHelpers $admin
 * @var string $content
 */
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7; IE=EmulateIE9" />
    <?php if (!empty($app['Admin']['favicon'])): ?>
      <link rel="shortcut icon" href="<?php echo $asset->image($app['Admin']['favicon']) ?>" type="image/x-icon">
    <?php endif; ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $asset->css('Anthem/Core:lib/bootstrap.css') ?>">
    <link type="text/css" rel="stylesheet" href="<?php echo $asset->css('Anthem/Core:anthem.css') ?>">
    <link type="text/css" rel="stylesheet" href="<?php echo $asset->css('Anthem/Admin:admin.css') ?>">
    <?php if (!empty($app['Admin']['extra_css'])): ?>
    <?php foreach ($app['Admin']['extra_css'] as $css): ?>
          <link type="text/css" rel="stylesheet" href="<?php echo $asset->css($css) ?>">
      <?php endforeach; ?>
    <?php endif; ?>
    <script type="text/javascript" src="<?php echo $link->url('_l10n_js.ignorehttps') ?>"></script>
    <script type="text/javascript" src="<?php echo $asset->js('Anthem/Core:lib/jquery.js') ?>"></script>
    <script type="text/javascript" src="<?php echo $asset->js('Anthem/Core:lib/bootstrap.js') ?>"></script>
    <script type="text/javascript" src="<?php echo $asset->js('Anthem/Admin:admin.js') ?>"></script>
    <script type="text/javascript">var baseURL='<?php echo $app['request']->getBaseUrl() ?>', ajaxBaseURL = '<?php echo $admin->ajax('') ?>/';</script>
  <?php echo $view->getSlot('head') ?>
    <title>
      <?php if ($view->isSlotSet('title')) echo $view->getSlot('title') . ' &mdash; ' ?><?php echo $app['Core']['project'] ?>
    </title>
</head>
<body class="print">
<div class="wrap">
  <?php if (!empty($app['Admin']['default_menu'])): ?>
    <?php echo $view->sub('Anthem/Nav:fixed_menu/menu.php', array('menu' => $app['Nav']['fixed_menu'][$app['Admin']['default_menu']])) ?>
  <?php endif; ?>
  <?php echo $content ?>
</div>
</body>
</html>