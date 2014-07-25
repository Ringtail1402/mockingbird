<?php
/**
 * Currency column filter template.
 *
 * @var string $field
 * @var array $filter
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<span style="vertical-align: bottom; font-size: larger;">&ge;</span> <input type="text" name="filter[<?php echo $field ?>][from]"
       value="<?php echo htmlspecialchars(isset($filter['from']) ? $filter['from'] : '') ?>"
       class="apply-filters-on-enter"
       style="width: 65%;"
       data-id="<?php echo $field ?>">
<br>
<span style="vertical-align: bottom; font-size: larger;">&le;</span> <input type="text" name="filter[<?php echo $field ?>][to]"
       value="<?php echo htmlspecialchars(isset($filter['to']) ? $filter['to'] : '') ?>"
       class="apply-filters-on-enter"
       style="width: 65%;"
       data-id="<?php echo $field ?>">
<?php $view->endSquash() ?>

