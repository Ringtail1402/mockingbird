<?php
/**
 * Admin menu item template.
 *
 * @var string $id
 * @var string $activeurl
 * @var array $item Item params.
 * @var \Silex\Application $app
 * @var \Core\View\ViewHelpers $view
 * @var \Core\View\LinkHelpers $link
 */
?>
<?php if (isset($item['auth'])): ?>
  <?php if ($item['auth'] && $app['auth']->isGuest() || !$item['auth'] && !$app['auth']->isGuest()) return ?>
<?php endif; ?>
<?php if (isset($item['policies'])): ?>
  <?php if (!$app['auth']->hasPolicies($item['policies'])) return ?>
<?php endif; ?>
<?php if (isset($item['no_policies'])): ?>
  <?php if ($app['auth']->hasPolicies($item['no_policies'])) return ?>
<?php endif; ?>

<?php if (!isset($item['url'])) $item['url'] = $id ?>
<?php $item['url'] = str_replace('~', $app['request']->getBaseUrl(), $item['url']) ?>
<?php if ($link->url($item['url']) == $app['request']->getBaseUrl() . $activeurl): ?>
  <li class="active menu-item-<?php echo $id ?> <?php echo isset($item['class']) ? $item['class'] : '' ?>">
    <a href="#<?php if (!empty($item['hash'])) echo $item['hash'] ?>"<?php if (isset($item['tooltip'])) echo ' title="' . htmlspecialchars($view->str($item['tooltip'])) . '"' ?>>
      <?php echo $view->str($item['title']) ?>
    </a>
  </li>
<?php else: ?>
  <li class="menu-item-<?php echo $id ?> <?php echo isset($item['class']) ? $item['class'] : '' ?>">
    <a href="<?php echo $link->url($item['url']) ?><?php echo isset($item['hash']) ? '#' . $item['hash'] : '' ?>"<?php if (isset($item['tooltip'])) echo ' title="' . htmlspecialchars($view->str($item['tooltip'])) . '"' ?>>
      <?php echo $view->str($item['title']) ?>
    </a>
  </li>
<?php endif; ?>
