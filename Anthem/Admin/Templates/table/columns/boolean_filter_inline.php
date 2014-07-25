<?php
/**
 * Boolean column filter template, for inline view.
 *
 * @var string $field
 * @var string $filter
 */
?>
<input type="hidden" name="filter[<?php echo $field ?>]" value="<?php echo htmlspecialchars($filter) ?>" data-id="<?php echo $field ?>">
<label class="inline checkbox"><input type="checkbox" class="boolean-filter" data-id="<?php echo $field ?>">&nbsp;</label>

