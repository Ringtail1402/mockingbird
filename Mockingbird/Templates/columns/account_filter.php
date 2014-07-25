<?php
/**
 * Account column filter template.
 *
 * @var string $field
 * @var integer $filter
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<select name="filter[<?php echo $field ?>]"
       class="apply-filters-on-change"
       data-id="<?php echo $field ?>">
  <option value=""></option>
  <?php echo $m->renderAccountOptionTags($filter) ?>
</select>
<?php $view->endSquash() ?>
