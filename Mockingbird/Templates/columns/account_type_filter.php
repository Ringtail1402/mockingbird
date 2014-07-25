<?php
/**
 * Account type column filter template.
 *
 * @var string $field
 * @var string $filter
 */
?>
<select name="filter[<?php echo $field ?>]" data-id="<?php echo $field ?>" style="width: 100%;" class="apply-filters-on-change">
  <option value=""></option>
  <option value="normal"<?php if ($filter == 'normal') echo ' selected' ?>><?php echo _t('Admin.BOOLEAN_FALSE') ?></option>
  <option value="debit"<?php if ($filter == 'debit') echo ' selected' ?>><?php echo _t('DEBIT_ACCOUNT') ?></option>
  <option value="credit"<?php if ($filter == 'credit') echo ' selected' ?>><?php echo _t('CREDIT_ACCOUNT') ?></option>
</select>