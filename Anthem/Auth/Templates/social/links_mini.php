<?php
/**
 * Links to social networks login (small).
 *
 * @var string|null $message
 * @var array $social
 * @var \Anthem\Core\View\LinkHelpers $link
 * @var \Anthem\Core\View\AssetHelpers $asset
 */
?>
<div class="social-login mini">
  <ul>
    <?php if (!empty($message)): ?>
      <li><?php echo $message ?></li>
    <?php endif; ?>
    <li>
      <?php foreach ($social as $provider_id => $data): ?>
        <a href="<?php echo $link->url('auth.social.prompt', array('provider' => $provider_id)) ?>" style="text-decoration: none;">
          <img src="<?php echo $asset->image($data['icon']) ?>" title="<?php echo $data['title'] ?>">
        </a>
      <?php endforeach; ?>
    </li>
  </ul>
</div>
