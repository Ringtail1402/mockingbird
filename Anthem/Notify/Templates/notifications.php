<?php
/**
 * Notifications output template.  Is self-contained and may be called with no params.
 *
 * @var \Silex\Application $app
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php foreach ($app['notify']->getAll() as $time => $notice): ?>
  <?php echo $view->sub('Anthem/Notify:notification.php', array('notice' => $notice)) ?>
<?php endforeach; ?>
