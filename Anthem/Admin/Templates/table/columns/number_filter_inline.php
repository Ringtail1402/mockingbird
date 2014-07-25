<?php
/**
 * Integer column inline filter template.
 *
 * @var string $field
 * @var string $filter
 * @var \Anthem\Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<?php echo _t('Admin.BETWEEN') ?>
<input type="text" name="filter[<?php echo $field ?>][from]"
       id="filter_<?php echo $field ?>_from"
       value="<?php echo htmlspecialchars($filter['from']) ?>"
       class="input-mini apply-filters-on-enter apply-validation-on-keypress"
       data-id="<?php echo $field ?>"
       data-validators="validate-integer"
       placeholder="<?php echo _t('Admin.FROM_TOOLTIP') ?>">
 <?php echo _t('Admin.TO') ?>
 <input type="text" name="filter[<?php echo $field ?>][to]"
       id="filter_<?php echo $field ?>_to"
       value="<?php echo htmlspecialchars($filter['to']) ?>"
       class="input-mini apply-filters-on-enter apply-validation-on-keypress"
       data-id="<?php echo $field ?>"
       data-validators="validate-integer"
       placeholder="<?php echo _t('Admin.TO_TOOLTIP') ?>">
<?php $view->endSquash() ?>