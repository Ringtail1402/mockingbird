<?php
/**
 * E-mail validation message page.
 *
 * @var string $email
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Core\View\LinkHelpers $link
 */
?>

<?php $view->extend() ?>

<?php $view->setSlot('title', _t('Auth.REGISTER_EMAIL_VALIDATION_NEEDED')) ?>

<div class="hero-unit mini-form">
  <p><?php echo _t('Auth.REGISTER_EMAIL_VALIDATION_NEEDED_SUBTITLE', htmlspecialchars($email)) ?></p>
</div>