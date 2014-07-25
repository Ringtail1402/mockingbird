<?php
/**
 * Links to social networks login.
 *
 * @var string|null $message
 * @var array $social
 * @var \Anthem\Core\View\LinkHelpers $link
 * @var \Anthem\Core\View\AssetHelpers $asset
 */
?>
<div class="social-login">
  <?php if (!empty($message)): ?>
    <p><?php echo $message ?></p>
  <?php endif; ?>
  <ul>
    <?php foreach ($social as $provider_id => $data): ?>
      <?php if (isset($data['available']) && !$data['available']) continue ?>
      <li>
        <a href="<?php echo $link->url('auth.social.prompt', array('provider' => $provider_id)) ?>" style="text-decoration: none;">
          <img src="<?php echo $asset->image($data['icon']) ?>" title="<?php echo $data['title'] ?>">
        </a>
        <a href="<?php echo $link->url('auth.social.prompt', array('provider' => $provider_id)) ?>">
          <?php echo $data['title'] ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
