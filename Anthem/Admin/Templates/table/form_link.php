<?php
/**
 * Renders a single form link.
 *
 * @var string $name
 * @var array $form_link
 * @var object $object
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Admin\View\AdminHelpers $admin
 */
?>
<?php $view->squash() ?>
<?php if ($admin->testLinkOrAction($object, $form_link)): ?>
  <a class="btn <?php if (isset($form_link['link_class'])) echo $form_link['link_class'] ?>"
    <?php if (isset($form_link['external']) && $form_link['external']): ?>
      target="_blank"
    <?php endif; ?>
    <?php if (isset($form_link['js'])): ?>
      onclick="<?php echo htmlspecialchars($form_link['js']($object)) ?>"
    <?php endif; ?>
    <?php if (isset($form_link['url'])): ?>
      href="<?php echo $form_link['url']($object) ?>"
    <?php endif; ?>
  >
    <?php echo isset($form_link['title']) ? $form_link['title'] : $name ?>
  </a>
<?php endif; ?>
<?php $view->endSquash() ?>