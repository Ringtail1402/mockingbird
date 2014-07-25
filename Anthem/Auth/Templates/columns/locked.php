<?php
/**
 * Locked column value template.
 *
 * @var \Anthem\Auth\Model\User $object
 */
?>
<?php if (!$value): ?>
  <?php echo _t('Auth.NOT_LOCKED') ?>
  <?php if (empty($options['readonly'])): ?>
    <button onclick="Users.lockUser(<?php echo $object->getId() ?>); return false;" class="btn btn-mini btn-danger"><?php echo _t('Auth.LOCK') ?></button>
  <?php endif;?>
<?php else: ?>
  <b class="text-error"><?php echo _t('LOCK_REASON.BRIEF.' . $value) ?></b>
  <?php if (empty($options['readonly'])): ?>
    <button onclick="Users.unlockUser(<?php echo $object->getId() ?>); return false;" class="btn btn-mini btn-success"><?php echo _t('Auth.UNLOCK') ?></button>
  <?php endif;?>
<?php endif;?>