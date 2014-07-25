<?php
/**
 * Page template.
 *
 * @var \AnthemCM\Pages\Model\Page $page
 * @var \Anthem\Core\View\ViewHelpers $view
 */
?>
<?php $view->extend() ?>

<?php $view->setSlot('title', htmlspecialchars($page->getTitle())) ?>

<?php echo $page->getContent() ?>
