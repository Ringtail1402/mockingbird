<?php
/**
 * Transaction tags input template.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $js_validation_options
 * @var string $value
 * @var array $options
 * @var boolean $valid
 * @var \Anthem\Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<?php // Existing tags ?>
<?php if (is_array($value)) foreach ($value as $i => $tag): ?>
  <?php if ($i === 'new') continue ?>
  <span id="<?php echo $id ?>_<?php echo $i ?>"><nobr>
    <?php if (isset($tag['id'])): ?>
      <input type="hidden" name="<?php echo $name ?>[<?php echo $i ?>][id]" value="<?php echo $tag['id'] ?>">
    <?php endif; ?>
    <input type="hidden" name="<?php echo $name ?>[<?php echo $i ?>][title]" value="<?php echo htmlspecialchars($tag['title']) ?>">
    <?php echo htmlspecialchars($tag['title']) ?>
    <?php if (empty($options['readonly'])): ?>
      <i style="cursor: pointer;" class="icon-remove" onclick="$('#<?php echo $id ?>_<?php echo $i ?>').remove(); return false;"></i>,
    <?php else: echo ';'; endif; ?>
  </nobr></span>
<?php endforeach; ?>
<?php // New tag field ?>
<?php if (empty($options['readonly'])): ?>
<nobr><input type="text"
       id="<?php echo $id ?>_new"
       name="<?php echo $name ?>[new][title]"
       value="<?php echo isset($value['new']['title']) ? htmlspecialchars($value['new']['title']) : '' ?>">
<i id="<?php echo $id ?>_add" style="cursor: pointer;" class="icon-plus"></i></nobr>
<?php // Tag adding JS ?>
<script type="text/javascript">
  $('#<?php echo $id ?>_add').click(function () {
    var newid = 'new' + Math.round(Math.random() * 10000),
        title = $('#<?php echo $id ?>_new').val().replace('&', '&amp;').replace('<', '&lt').replace('>', '&gt;').trim();
    if (title.length)
      $('<span id="<?php echo $id ?>_' + newid + '"><nobr>' +
          '<input type="hidden" name="<?php echo $name ?>[' + newid + '][title]" value="' + title + '">' +
          title +
          ' <i style="cursor: pointer;" class="icon-remove" onclick="$(\'#<?php echo $id ?>_' + newid + '\').remove(); return false;"></i>, ' +
        '</nobr></span>').insertBefore($('#<?php echo $id ?>_new'));
    $('#<?php echo $id ?>_new').val('').focus();
  });
</script>
<?php endif; ?>
<?php $view->endSquash() ?>