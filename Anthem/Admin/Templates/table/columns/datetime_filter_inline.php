<?php
/**
 * Date/time column filter template.
 *
 * @var string $field
 * @var array $filter
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<?php echo _t('Admin.BETWEEN') ?>
 <input type="text" name="filter[<?php echo $field ?>][from]"
       value="<?php echo htmlspecialchars(isset($filter['from']) && strtotime($filter['from']) ? date(_t('Forms.DATE_FORMAT'), strtotime($filter['from'])) : null) ?>"
       class="apply-filters-on-enter apply-filters-on-change datefilter input-small"
       data-id="<?php echo $field ?>" data-subid="from">
 <?php echo _t('Admin.AND') ?>
 <input type="text" name="filter[<?php echo $field ?>][to]"
       value="<?php echo htmlspecialchars(isset($filter['to']) && strtotime($filter['to']) ? date(_t('Forms.DATE_FORMAT'), strtotime($filter['to'])) : null) ?>"
       class="apply-filters-on-enter apply-filters-on-change datefilter input-small"
       data-id="<?php echo $field ?>" data-subid="to">
<?php $view->endSquash() ?>

