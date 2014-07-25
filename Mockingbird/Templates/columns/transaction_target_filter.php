<?php
/**
 * Target column filter template, Transaction-specific.
 *
 * @var string $field
 * @var array $filter
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<input type="text" name="filter[<?php echo $field ?>][counter_party]"
       value="<?php echo htmlspecialchars(isset($filter['counter_party']) ? $filter['counter_party'] : '') ?>"
       class="apply-filters-on-enter counterparty-typeahead"
       data-id="<?php echo $field ?>"
       placeholder="<?php echo _t('PLACEHOLDER_COUNTER_PARTY') ?>"
>
&nbsp;<?php echo _t('OR') ?>&nbsp;
<select name="filter[<?php echo $field ?>][target_account]"
       class="apply-filters-on-change"
       data-id="<?php echo $field ?>">
  <option value=""><?php echo _t('PLACEHOLDER_TARGET_ACCOUNT') ?></option>
  <?php echo $m->renderAccountOptionTags(isset($filter['target_account']) ? $filter['target_account'] : null) ?>
</select>
<?php $view->endSquash() ?>
