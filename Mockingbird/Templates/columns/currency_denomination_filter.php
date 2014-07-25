<?php
/**
 * Currency denomination column filter template.
 *
 * @var string $field
 * @var integer $filter
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<select name="filter[<?php echo $field ?>]"
       class="apply-filters-on-change"
       style="width: 100%; padding: 2px;"
       data-id="<?php echo $field ?>">
  <option value=""></option>
  <?php echo $m->renderCurrencyOptionTags($filter) ?>
</select>
<?php $view->endSquash() ?>
