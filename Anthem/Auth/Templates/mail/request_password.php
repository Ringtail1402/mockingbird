<?php
/**
 * Password recovery e-mail template.
 *
 * @var \Silex\Application $app
 * @var string $key
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\LinkHelpers $link
 */
?>

<?php $view->beginSlot('content') ?>

<p><?php echo _t('Auth.REQUEST_PASSWORD_MAIL_BODY1', $app['request']->getUriForPath('/'), $app['Core']['project']) ?></p>

<big><a href="<?php echo $link->url('auth.reset_password', array(), true) ?>?key=<?php echo $key ?>">
  <?php echo $link->url('auth.reset_password', array(), true) ?>?key=<?php echo $key ?>
</a></big>

<p><?php echo _t('Auth.REQUEST_PASSWORD_MAIL_BODY2') ?></p>

<?php $view->endSlot() ?>
<?php echo $view->sub('Anthem/Core:mail_layout.php') ?>