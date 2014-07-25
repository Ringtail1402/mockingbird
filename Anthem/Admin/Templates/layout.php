<!DOCTYPE HTML>
<?php
/**
 * Admin layout template.
 *
 * @var \Silex\Application $app
 * @var \Core\View\ViewHelpers $view
 * @var \Core\View\AssetHelpers $asset
 * @var \Core\View\LinkHelpers $link
 * @var \Admin\View\AdminHelpers $admin
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
    <?php if (!empty($app['Auth']['enable'])): ?>
      <script type="text/javascript" src="<?php echo $asset->js('Anthem/Auth:auth.js') ?>"></script>
    <?php endif; ?>
    <script type="text/javascript" src="<?php echo $asset->js('Anthem/Admin:admin.js') ?>"></script>
    <script type="text/javascript">var baseURL='<?php echo $app['request']->getBaseUrl() ?>', ajaxBaseURL = '<?php echo $admin->ajax('') ?>/';</script>
    <?php if (!empty($app['Admin']['extra_js'])): ?>
      <?php foreach ($app['Admin']['extra_js'] as $js): ?>
        <script type="text/javascript" src="<?php echo $asset->js($js) ?>"></script>
      <?php endforeach; ?>
    <?php endif; ?>
    <?php echo $view->getSlot('head') ?>
    <?php echo $view->sub('Anthem/Core:metas.php') ?>
    <title><?php echo $view->sub('Anthem/Core:title.php') ?></title>
    <!--[if lt IE 9]><script type="text/javascript" src="<?php echo $asset->js('Anthem/Core:iewarning.js') ?>"></script><![endif]-->
  </head>
  <body class="noprint">
    <div id="disable-layer" style="display: none;"></div>
    <div class="wrap">
      <?php if (!empty($app['Admin']['default_menu'])): ?>
        <?php echo $view->sub('Anthem/Nav:fixed_menu/menu.php', array('menu' => $app['Nav']['fixed_menu'][$app['Admin']['default_menu']])) ?>
      <?php endif; ?>

      <div class="container-fluid main">
        <?php if (isset($app['notify'])) echo $view->sub('Anthem/Notify:notifications.php') ?>

        <div class="page-header">
          <?php if ($view->isSlotSet('global-actions')): ?>
            <div style="float: right;">
              <?php echo $view->getSlot('global-actions') ?>
            </div>
          <?php endif; ?>
          <h1>
            <?php echo $view->getSlot('title') ?>
            <?php if ($view->isSlotSet('subtitle')): ?>
              <small><?php echo $view->getSlot('subtitle') ?></small>
            <?php endif; ?>
          </h1>
        </div>
        <?php echo $content ?>
      </div>
    </div>

    <?php if ($view->isSlotSet('footer')): ?>
      <div class="container-fluid footer">
        <?php echo $view->getSlot('footer') ?>
      </div>
    <?php endif; ?>

    <?php echo $view->sub('Anthem/Core:modal-template.php') ?>
    <?php echo $view->sub('Anthem/Core:global_footer.php') ?>
  </body>
</html>