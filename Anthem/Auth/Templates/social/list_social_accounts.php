<?php
/**
 * Social accounts list page.
 *
 * @var \Anthem\Auth\Model\UserSocialAccount[] $social_accounts
 * @var array $providers
 * @var boolean $can_add
 * @var boolean $can_delete
 * @var \Anthem\Core\View\ViewHelpers  $view
 * @var \Anthem\Core\View\LinkHelpers  $link
 * @var \Anthem\Core\View\AssetHelpers $asset
 */
?>

<?php $view->extend() ?>

<?php $view->setSlot('title', _t('Auth.SOCIAL_ACCOUNTS_LIST')) ?>

<div class="hero-unit small-form">
<?php if (count($social_accounts)): ?>
  <h4><?php echo _t('Auth.SOCIAL_ACCOUNTS') ?></h4>
  <hr>
  <?php foreach ($social_accounts as $provider => $social_account): ?>
    <h4 style="margin-bottom: 5px;"><?php echo htmlspecialchars($social_account->getTitle()) ?></h4>
    <p style="font-size: 12px; line-height: 16px;">
      <img style="vertical-align: top;" src="<?php echo $asset->image($providers[$provider]['icon']) ?>" title="<?php echo $providers[$provider]['title'] ?>">
      <?php echo $providers[$provider]['title'] ?>
    </p>
    <?php if ($can_delete): ?>
      <form method="post" action="<?php echo $link->url('auth.social.delete', array('provider' => $provider)) ?>">
        <input type="submit" class="btn btn-small btn-danger" value="<?php echo _t('Auth.SOCIAL_REMOVE') ?>">
      </form>
    <?php endif; ?>
    <hr>
  <?php endforeach; ?>
<?php else: ?>
  <h4><?php echo _t('Auth.SOCIAL_NO_ACCOUNTS') ?></h4>
<?php endif; ?>

<?php if ($can_add): ?>
  <?php echo $view->sub('Anthem/Auth:social/links.php', array('social' => $providers, 'message' => _t('Auth.SOCIAL_AVAILABLE'))) ?>
<?php endif; ?>
</div>