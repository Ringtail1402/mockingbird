<?php
/**
 * Transaction category input template.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $js_validation_options
 * @var string $value
 * @var array $options
 * @var boolean $valid
 * @var \Core\View\ViewHelpers $view
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>
<?php $view->squash() ?>
<select
  id="<?php echo $id ?>_id"
  name="<?php echo $name ?>[id]"
  class="<?php echo $class ?><?php if (!$valid) echo ' error' ?>"
  <?php echo $js_validation_options ?>
  <?php if (isset($options['readonly']) && $options['readonly']): ?>
    readonly disabled
  <?php endif; ?>
>
  <option value=""></option>
  <?php echo $m->renderCategoryOptionTags($value['id'], $options['user']) ?>
  <option value="-1"<?php if ($value['id'] == -1) echo ' selected' ?>><?php echo _t('TRANSACTION_CATEGORY_NEW') ?></option>
</select>
<input
  type="text"
  <?php if ($value['id'] != -1) echo 'style="display: none;"' ?>
  id="<?php echo $id ?>_new"
  name="<?php echo $name ?>[new]"
  value="<?php echo htmlspecialchars($value['new']) ?>"
>
<script type="text/javascript">
  $('#<?php echo $id ?>_id').change(function () {
    if ($('#<?php echo $id ?>_id option:selected').val() == '-1')
      $('#<?php echo $id ?>_new').css('display', 'inline').focus();
    else
      $('#<?php echo $id ?>_new').hide();
  });
</script>
<?php $view->endSquash() ?>