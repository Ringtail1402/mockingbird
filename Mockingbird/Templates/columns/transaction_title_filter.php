<?php
/**
 * String column filter template.
 *
 * @var string $field
 * @var string $filter
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<input type="text" name="filter[<?php echo $field ?>]"
       value="<?php echo htmlspecialchars($filter) ?>"
       class="apply-filters-on-change title-typeahead input-xxlarge"
       data-id="<?php echo $field ?>">
<?php $view->endSquash() ?>