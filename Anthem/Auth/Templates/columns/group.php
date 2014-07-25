<?php
/**
 * Group column value template.
 *
 * @var \Anthem\Auth\Model\Group $value
 * @var \Anthem\Core\View\LinkHelpers $link
 */
?>
<?php if ($value): ?>
  <a href="<?php echo $link->url('admin.page', array('page' => 'auth.admin.groups')) ?>#id=<?php echo $value->getId() ?>">
    <?php echo htmlspecialchars($value->getTitle()) ?>
  </a>
<?php else: ?>
  &mdash;
<?php endif; ?>