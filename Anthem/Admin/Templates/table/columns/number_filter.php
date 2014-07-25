<?php
/**
 * Integer column filter template.
 *
 * @var string $field
 * @var string $filter
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<input type="text" name="filter[<?php echo $field ?>][from]"
       id="filter_<?php echo $field ?>_from"
       value="<?php echo htmlspecialchars($filter['from']) ?>"
       class="apply-filters-on-enter apply-validation-on-keypress"
       style="width: 25%;"
       data-id="<?php echo $field ?>"
       data-validators="validate-integer"
       placeholder="<?php echo _t('Admin.FROM_TOOLTIP') ?>">
&hellip;
<input type="text" name="filter[<?php echo $field ?>][to]"
       id="filter_<?php echo $field ?>_to"
       value="<?php echo htmlspecialchars($filter['to']) ?>"
       class="apply-filters-on-enter apply-validation-on-keypress"
       style="width: 25%;"
       data-id="<?php echo $field ?>"
       data-validators="validate-integer"
       placeholder="<?php echo _t('Admin.TO_TOOLTIP') ?>">
<?php $view->endSquash() ?>