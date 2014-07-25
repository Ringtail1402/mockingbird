<?php
/**
 * User column value template.
 *
 * @var \Anthem\Auth\Model\User $value
 * @var \Anthem\Core\View\LinkHelpers $link
 */
?>
<?php if ($value): ?>
  <a href="<?php echo $link->url('admin.page', array('page' => 'auth.admin.users')) ?>#id=<?php echo $value->getId() ?>">
    <?php $email = implode('<wbr>', str_split($value->getEmail(), 4)) ?>
    <?php echo str_replace('&lt;wbr&gt;', '<wbr>', htmlspecialchars($email)) ?>
  </a>
<?php else: ?>
  &mdash;
<?php endif; ?>