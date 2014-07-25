<?php
/**
 * Boolean column filter template.
 *
 * @var string $field
 * @var string $filter
 */
?>
<input type="hidden" name="filter[<?php echo $field ?>]" value="<?php echo htmlspecialchars($filter) ?>" data-id="<?php echo $field ?>">
<div style="text-align: center;"><input type="checkbox" class="boolean-filter" data-id="<?php echo $field ?>"></div>
