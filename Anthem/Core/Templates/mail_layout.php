<?php
/**
 * Stub layout template for e-mails.
 *
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Silex\Application $app
 */
?>

<h2><?php echo $app['Core']['project'] ?></h2>

<?php echo $view->getSlot('content') ?>