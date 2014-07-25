<?php
/**
 * Renders a single per-object link.
 *
 * @var string $name
 * @var array $link
 * @var object $object
 * @var \Anthem\Admin\View\AdminHelpers $admin
 * @var \Anthem\Core\View\ViewHelpers $view
 */
?>
<?php if ($admin->testLinkOrAction($object, $link)): ?>
  <?php $view->squash() ?>
  <a class="btn btn-small <?php if (isset($link['link_class'])) echo $link['link_class'] ?>"
    <?php if (isset($link['external']) && $link['external']): ?>
      target="_blank"
    <?php endif; ?>
    <?php if (isset($link['js'])): ?>
      onclick="<?php echo htmlspecialchars($link['js']($object)) ?>"
    <?php endif; ?>
    <?php if (isset($link['url'])): ?>
      href="<?php echo $link['url']($object) ?>"
    <?php endif; ?>
  >
    <?php echo isset($link['title']) ? $link['title'] : $name ?>
  </a>
  <?php $view->endSquash() ?>
<?php endif; ?>