<?php // Modal message ?>
<div class="modal hide fade" id="modal" tabindex="-1" role="dialog" style="display: none; top: 75%;">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3 id="modal-header">...</h3>
  </div>
  <form class="modal-body" id="modal-body">...</form>
  <div class="modal-footer">
    <button id="modal-button-ok" class="btn btn-primary" data-dismiss="modal" style="display: none;"><?php echo _t('Admin.MODAL_OK') ?></button>
    <button id="modal-button-yes" class="btn btn-primary" data-dismiss="modal" style="display: none;"><?php echo _t('Admin.MODAL_YES') ?></button>
    <button id="modal-button-no" class="btn" data-dismiss="modal" style="display: none;"><?php echo _t('Admin.MODAL_NO') ?></button>
    <button id="modal-button-cancel" class="btn" data-dismiss="modal" style="display: none;"><?php echo _t('Admin.MODAL_CANCEL') ?></button>
  </div>
</div>