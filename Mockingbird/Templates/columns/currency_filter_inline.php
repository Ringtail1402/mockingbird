<?php
/**
 * Currency column inline filter template.
 *
 * @var string $field
 * @var array $filter
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<?php $format = explode('#', $app['mockingbird.model.currency']->getDefaultCurrency()->getFormat()) ?>
<?php echo _t('Admin.BETWEEN') ?>
 <div class="<?php if ($format[0]) echo 'input-prepend '; if ($format[1]) echo 'input-append' ?>">
  <span class="add-on pre"<?php if (!$format[0]) echo ' style="display: none;"' ?>>
    <?php echo htmlspecialchars($format[0]) ?>
  </span>
  <input type="text" name="filter[<?php echo $field ?>][from]"
         value="<?php echo htmlspecialchars(isset($filter['from']) ? $filter['from'] : '') ?>"
         class="apply-filters-on-enter input-mini" style="text-align: right;"
         data-id="<?php echo $field ?>">
  <span class="add-on post"<?php if (!$format[1]) echo ' style="display: none;"' ?>>
    <?php echo htmlspecialchars($format[1]) ?>
  </span>
</div>
 <?php echo _t('Admin.AND') ?>
 <div class="<?php if ($format[0]) echo 'input-prepend '; if ($format[1]) echo 'input-append' ?>">
  <span class="add-on pre"<?php if (!$format[0]) echo ' style="display: none;"' ?>>
    <?php echo htmlspecialchars($format[0]) ?>
  </span>
  <input type="text" name="filter[<?php echo $field ?>][to]"
         value="<?php echo htmlspecialchars(isset($filter['to']) ? $filter['to'] : '') ?>"
         class="apply-filters-on-enter input-mini" style="text-align: right;"
         data-id="<?php echo $field ?>">
  <span class="add-on post"<?php if (!$format[1]) echo ' style="display: none;"' ?>>
    <?php echo htmlspecialchars($format[1]) ?>
  </span>
</div>
<label class="radio inline">
  <input type="radio" name="filter[<?php echo $field ?>][sign]" value="-" class="apply-filters-on-change"
         <?php if (empty($filter['sign']) || $filter['sign'] == '-') echo 'checked' ?>>
  <div><i class="icon-minus"></i> расхода</div>
</label>
<label class="radio inline">
  <input type="radio" name="filter[<?php echo $field ?>][sign]" value="+" class="apply-filters-on-change"
         <?php if (isset($filter['sign']) && $filter['sign'] == '+') echo 'checked' ?>>
  <div><i class="icon-plus"></i> дохода</div>
</label>
<?php $view->endSquash() ?>

