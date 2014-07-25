<?php
/**
 * Single notification template.
 *
 * @var array $notice
 * @var \Core\View\ViewHelpers $view
 */
?>
<div class="alert fade <?php if (!isset($hidden) || !$hidden) echo ' in'?><?php if ($notice['class']) echo ' alert-' . $notice['class'] ?>" data-uniqid="<?php echo $notice['uniqid'] ?>">
  <?php if ($notice['close_button']): ?>
    <button type="button" class="close" data-dismiss="alert"
      <?php if ($notice['ajax_dismiss']): ?>onclick="$.post('<?php echo $link->url('_notify_dismiss', array('uniqid' => $notice['uniqid'])) ?>');"<?php endif; ?>>×</button>
  <?php endif; ?>
  <?php echo $notice['message'] ?>
</div>
