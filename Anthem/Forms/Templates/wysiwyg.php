<?php
/**
 * TinyMCE-using textarea input template.  Trivial.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $js_validation_options
 * @var string $value
 * @var array $options
 * @var boolean $valid
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<textarea style="width: 100%; visibility: hidden;"
  id="<?php echo $id ?>"
  name="<?php echo $name ?>"
  class="wysiwyg-<?php echo $id ?> <?php echo $class ?><?php if (!$valid) echo ' error' ?>"
  <?php echo $js_validation_options ?>
>
<?php $view->endSquash() ?><?php echo htmlspecialchars($value) ?><?php $view->squash() ?>
</textarea>
<?php $view->endSquash() ?>

<?php // Dummy element for event catching.  TinyMCE seems to change textarea id ?>
<span style="display: none;" class="receive-update receive-unload" id="anchor-<?php echo $id ?>"></span>

<script type="text/javascript">
  // Manage editor load/update/unload
  tinyMCE.init(<?php echo json_encode($options['editor']) ?>);
  $('#anchor-<?php echo $id ?>').on('form-update', function(e) {
    tinyMCE.get('<?php echo $id ?>').save();
  });
  $('#anchor-<?php echo $id ?>').on('form-unload', function(e) {
    tinyMCE.remove(tinyMCE.get('<?php echo $id ?>'));
  });
</script>
