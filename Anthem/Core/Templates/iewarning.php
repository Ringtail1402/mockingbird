<div class="modal fade in" id="iemodal" tabindex="-1" role="dialog" style="top: 75%; z-index: 10000;">
  <div class="modal-header">
    <button type="button" class="close">×</button>
    <h3 id="modal-header"><?php echo _t('Core.IEWARNING_TITLE') ?></h3>
  </div>
  <form class="modal-body" id="modal-body">
    <p><?php echo _t('Core.IEWARNING') ?></p>
    <ul>
      <?php // Offer newer IE version only if OS is newer than Windows 2000/XP ?>
      <?php if (strpos($app['request']->headers->get('USER_AGENT'), 'Windows NT 5') === false): ?>
        <li><?php echo _t('Core.IEWARNING_IELINK') ?></li>
      <?php endif; ?>
      <li><?php echo _t('Core.IEWARNING_FIREFOXLINK') ?></li>
      <li><?php echo _t('Core.IEWARNING_CHROMELINK') ?></li>
    </ul>
  </form>
  <div class="modal-footer">
    <button class="btn btn-danger"><?php echo _t('Core.IEWARNING_IGNORE') ?></button>
  </div>
</div>