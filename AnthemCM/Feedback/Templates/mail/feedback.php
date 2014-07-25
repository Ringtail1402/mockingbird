<?php
/**
 * Feedback mail.
 *
 * @var string $email
 * @var string $message
 * @var \Anthem\Auth\Model\User|null $user
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\LinkHelpers $link
 * @var \Silex\Application $app
 */
?>

<?php $view->beginSlot('content') ?>

<p><?php echo _t('Feedback.MAIL', $app['request']->getUriForPath('/'), $app['Core']['project']) ?></p>

<blockquote><?php echo nl2br(htmlspecialchars($message)) ?></blockquote>

<?php if ($user): ?>
    <p><?php echo _t('Feedback.MAIL_CAN_REPLY_REGISTERED', $email, $link->url('admin.page', array('page' => 'auth.admin.users'), true) . '#id=' . $user->getId()) ?></p>
<?php elseif ($email): ?>
    <p><?php echo _t('Feedback.MAIL_CAN_REPLY', $email) ?></p>
<?php else: ?>
    <p><?php echo _t('Feedback.MAIL_CANNOT_REPLY') ?></p>
<?php endif; ?>

<?php $view->endSlot() ?>
<?php echo $view->sub('Anthem/Core:mail_layout.php') ?>