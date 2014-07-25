<?php
/**
 * Fixed menu template.
 *
 * @var array $menu
 * @var string $activeurl
 * @var \Silex\Application $app
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\LinkHelpers $link
 * @var \Anthem\Core\View\AssetHelpers $asset
 */
?>
<?php if (!isset($activeurl)) $activeurl = $app['request']->getPathInfo() ?>
<div class="navbar navbar-static-top <?php if (!empty($menu['options']['class'])) echo $menu['options']['class'] ?>">
  <div class="navbar-inner">
    <div class="container-fluid">
      <a class="brand<?php if ($activeurl == '/' || !$activeurl) echo ' active'; if (!empty($menu['options']['subtitle'])) echo ' subtitled' ?>"
         href="<?php echo $app['request']->getBaseUrl() ?>/">
        <?php if (!empty($print) && !empty($menu['options']['logo_print'])): ?>
          <img src="<?php echo $asset->image($menu['options']['logo_print']) ?>" alt="logo" title="<?php echo $menu['options']['title'] ?>">
        <?php elseif (!empty($menu['options']['logo'])): ?>
          <img src="<?php echo $asset->image($menu['options']['logo']) ?>" alt="logo" title="<?php echo $menu['options']['title'] ?>">
        <?php endif; ?>
        <?php echo $view->str($menu['options']['title']) ?>
        <?php if (!empty($menu['options']['subtitle'])): ?>
          <br><span><?php echo $view->str($menu['options']['subtitle']) ?></span>
        <?php endif; ?>
      </a>
      <ul class="nav">
        <?php echo $view->sub('Anthem/Nav:fixed_menu/menu_row.php', array('menu' => $menu, 'activeurl' => $activeurl, 'is_right' => false)) ?>
      </ul>

      <ul class="nav pull-right">
        <li id="spinner" style="display: none;">
          <?php echo $view->sub('Anthem/Core:spinner.php') ?>
        </li>

        <?php if (!empty($app['Auth']['enable']) && !empty($menu['options']['login_link']) && $app['auth']->isGuest()): // Auth ?>
          <?php // TODO: move in Auth module ?>
          <li>
            <a id="login-link"><span><?php echo _t('Auth.LOGIN') ?></span></a>
            <form id="login-form" class="login-form alert alert-info hide fade">
              <input type="hidden" id="ajax-login-form-url"
                     value="<?php echo preg_replace('/^https?/', $app['auth']->needSecureCookies() ? 'https' : 'http', $link->url('auth.login.ajax', array(), true)) ?>">
            </form>
          </li>
        <?php endif; ?>

        <?php echo $view->sub('Anthem/Nav:fixed_menu/menu_row.php', array('menu' => $menu, 'activeurl' => $activeurl, 'is_right' => true)) ?>

        <?php if (!empty($app['Core']['l10n.selector_in_menu'])): ?>
          <select id="language" style="width: auto; height: auto; margin: 8px; padding: 2px;" autocomplete="off">
            <?php foreach ($app['Core']['l10n.languages'] as $key => $name): ?>
              <option value="<?php echo $key ?>"<?php if ($app['l10n.language'] == $key) echo ' selected' ?>>
                <?php echo $name ?>
              </option>
            <?php endforeach; ?>
          </select>
          <script type="text/javascript">
            $('#language').change(function () {
              $.post('<?php echo $link->url('settings.set') ?>',
                { key: 'core.l10n.language', value: $('#language').val() },
                function(response) {
                  if (response) location.reload();
                }
              );
            });
          </script>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>
