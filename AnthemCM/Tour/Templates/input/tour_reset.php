<?php
/**
 * Tour reset input template.  This does not have an actual <input>,
 * but provides a button which POSTs a setting on the fly.
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
<?php if (!$value || !count($value)): ?>
  <button id="<?php echo $id ?>" class="btn" disabled><?php echo _t('Tour.TOUR_RESET_INPUT') ?></button>
<?php else: ?>
  <button id="<?php echo $id ?>" class="btn"><?php echo _t('Tour.TOUR_RESET_INPUT') ?></button>
  <script type="text/javascript">
    <?php $id = str_replace('.', '\\\\.', $id) ?>
    $('#<?php echo $id ?>').click(function () {
      $.post(baseURL + '/_set', { key: 'tour.state', value: {} }, function () {
        $('#<?php echo $id ?>').attr('disabled', 'disabled');
      });
      return false;
    });
  </script>
<?php endif; ?>