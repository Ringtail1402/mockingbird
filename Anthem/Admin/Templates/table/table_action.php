<?php
/**
 * Renders a single global action button.  By default it is disabled.
 *
 * @var string $name
 * @var array $table_action
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Admin\View\AdminHelpers $admin
 */
?>
<?php $view->squash() ?>
<button data-id="<?php echo $name ?>" disabled
  class="table-action btn <?php if (isset($table_action['button_class'])) echo $table_action['button_class'] ?>"
  <?php if (isset($table_action['confirm']) && $table_action['confirm']): ?>
    onclick="if (confirm('<?php echo isset($table_action['confirm_message']) ?
                                        htmlspecialchars($table_action['confirm_message']) :
                                        _t('Admin.CONFIRM') ?>')) TableAdmin.tableAction('<?php echo $name ?>'); return false;"
  <?php else: ?>
    onclick="TableAdmin.tableAction('<?php echo $name ?>'); return false;"
  <?php endif; ?>
  <?php if (isset($table_action['error_message'])): ?>
    data-error-message="<?php echo htmlspecialchars($table_action['error_message']) ?>"
  <?php endif; ?>
>
  <?php echo isset($table_action['title']) ? $table_action['title'] : $name ?>
</button>
<?php $view->endSquash() ?>
