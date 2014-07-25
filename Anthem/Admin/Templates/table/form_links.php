<?php
/**
 * Form links panel template.
 *
 * @var object $object
 * @var array $form_links
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Admin\View\AdminHelpers $admin
 */
?>
<?php foreach ($form_links as $name => $form_link): ?>
  <?php echo $view->sub($admin->getTemplate('form_link'), array('name' => $name, 'form_link' => $form_link, 'object' => $object)) ?>
<?php endforeach; ?>