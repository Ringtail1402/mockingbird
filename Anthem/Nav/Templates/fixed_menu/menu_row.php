<?php
/**
 * Fixed menu row template.
 *
 * @var array $menu
 * @var string $activeurl
 * @var boolean $is_right
 * @var \Silex\Application $app
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\LinkHelpers $link
 * @var \Anthem\Core\View\AssetHelpers $asset
 */
?>
<?php foreach ($menu as $id => $item): if ($id == 'options') continue ?>
  <?php if (empty($item['right']) == $is_right) continue ?>
  <?php if (!empty($item['submenu'])): // Submenu ?>
    <?php if (isset($item['auth'])): // Access control ?>
      <?php if ($item['auth'] && $app['auth']->isGuest() || !$item['auth'] && !$app['auth']->isGuest()) continue ?>
    <?php endif; ?>
    <?php if (isset($item['policies'])): ?>
      <?php if (!$app['auth']->hasPolicies($item['policies'])) continue; ?>
    <?php endif; ?>
    <?php if (isset($item['no_policies'])): ?>
      <?php if ($app['auth']->hasPolicies($item['no_policies'])) continue; ?>
    <?php endif; ?>

    <li class="dropdown menu-item-<?php echo $id ?> <?php echo isset($item['class']) ? $item['class'] : '' ?>">
      <a class="dropdown-toggle" data-toggle="dropdown">
        <?php echo $view->str($item['title']) ?>
          <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <?php foreach ($item['submenu'] as $id => $item): ?>
        <?php echo $view->sub('Anthem/Nav:fixed_menu/menu_item.php', array('id' => $id, 'item' => $item, 'activeurl' => $activeurl)) ?>
        <?php endforeach; ?>
      </ul>
    </li>
  <?php else: // Top-level item ?>
  <?php echo $view->sub('Anthem/Nav:fixed_menu/menu_item.php', array('id' => $id, 'item' => $item, 'activeurl' => $activeurl)) ?>
  <?php endif; ?>
<?php endforeach; ?>
