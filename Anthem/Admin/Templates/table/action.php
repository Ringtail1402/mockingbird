<?php
/**
 * Renders a single per-object action button.
 *
 * @var string $name
 * @var array $action
 * @var object $object
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Admin\View\AdminHelpers $admin
 */
?>
<?php if ($admin->testLinkOrAction($object, $action)): ?>
<?php $view->squash() ?>
<button data-id="<?php echo $name ?>-<?php echo $object->getPrimaryKey() ?>"
  class="action btn btn-small <?php if (isset($action['button_class'])) echo $action['button_class'] ?>"
  <?php if (isset($action['confirm']) && $action['confirm']): ?>
    onclick="if (confirm('<?php echo isset($action['confirm_message']) ?
                                        htmlspecialchars($action['confirm_message']) :
                                        _t('Admin.CONFIRM') ?>'))
              TableAdmin.action('<?php echo $name ?>', <?php echo $object->getPrimaryKey() ?>); return false;"
  <?php else: ?>
    onclick="TableAdmin.action('<?php echo $name ?>', <?php echo $object->getPrimaryKey() ?>); return false;"
  <?php endif; ?>
  <?php if (isset($action['error_message'])): ?>
    data-error-message="<?php echo htmlspecialchars($action['error_message']) ?>"
  <?php endif; ?>
>
  <?php echo isset($action['title']) ? $action['title'] : $name ?>
</button>
<?php $view->endSquash() ?>
<?php endif; ?>