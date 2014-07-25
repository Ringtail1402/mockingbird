<input type="hidden" name="id" value="<?php echo $id ?>">
<div class="form-horizontal">
  <div class="control-group">
    <label class="control-label" for="lock-reason"><?php echo _t('Auth.REASON') ?></label>
    <div class="controls">
      <select id="lock-reason" name="lock-reason">
        <?php foreach ($app['Auth']['lock_reasons'] as $reason): ?>
          <option value="<?php echo $reason ?>"><?php echo _t('LOCK_REASON.BRIEF.' . $reason) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
</div>
