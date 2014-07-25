<?php
/**
 * Renders a single mass action button.  By default it is disabled.
 *
 * @var string $name
 * @var array $mass_action
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Admin\View\AdminHelpers $admin
 */
?>
<?php $view->squash() ?>
<button data-id="<?php echo $name ?>" disabled
  class="mass-action btn <?php if (isset($mass_action['button_class'])) echo $mass_action['button_class'] ?>"
  <?php if (isset($mass_action['confirm']) && $mass_action['confirm']): ?>
    onclick="if (confirm('<?php echo isset($mass_action['confirm_message']) ?
                                        htmlspecialchars($mass_action['confirm_message']) :
                                        _t('Admin.CONFIRM') ?>'));
               TableAdmin.massAction('<?php echo $name ?>'); return false;"
  <?php else: ?>
    onclick="TableAdmin.massAction('<?php echo $name ?>'); return false;"
  <?php endif; ?>
  <?php if (isset($mass_action['error_message'])): ?>
    data-error-message="<?php echo htmlspecialchars($mass_action['error_message']) ?>"
  <?php endif; ?>
>
  <?php echo isset($mass_action['title']) ? $mass_action['title'] : $name ?>
</button>
<?php $view->endSquash() ?>

