<?php
/**
 * Category/tag column filter template, Transaction-specific.
 *
 * @var string $field
 * @var array $filter
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<select name="filter[<?php echo $field ?>][category]"
       class="apply-filters-on-change"
       data-id="<?php echo $field ?>">
  <option value=""><?php echo _t('PLACEHOLDER_CATEGORY') ?></option>
  <?php echo $m->renderCategoryOptionTags(isset($filter['category']) ? $filter['category'] : null) ?>
</select>
&nbsp;<?php echo _t('OR') ?>&nbsp;
<input type="text" name="filter[<?php echo $field ?>][tag]"
       value="<?php echo htmlspecialchars(isset($filter['tag']) ? $filter['tag'] : null) ?>"
       class="apply-filters-on-enter tag-typeahead"
       data-id="<?php echo $field ?>"
       placeholder="<?php echo _t('PLACEHOLDER_TAG') ?>"
>
<?php $view->endSquash() ?>
