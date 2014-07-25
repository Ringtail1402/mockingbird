<?php
/**
 * A control for BudgetEntryForm which allows selection of transaction type
 * (income/spending).
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $js_validation_options
 * @var string $value
 * @var array $options
 * @var boolean $valid
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<?php $icons = array('spending' => 'minus', 'income' => 'plus') ?>
<?php foreach (array('spending', 'income') as $type): ?>
  <label class="radio inline" style="margin-right: 20px;">
    <input type="radio" id="<?php echo $id ?>_<?php echo $type ?>" name="<?php echo $name ?>" value="<?php echo $type ?>"
           class="<?php echo $class ?><?php if (!$valid) echo ' error' ?>"
           <?php if ($value == $type) echo 'checked' ?>
           <?php if (!empty($options['readonly'])) echo 'readonly disabled' ?>
    >
    <i class="icon-<?php echo $icons[$type] ?>" style="margin-left: 5px;"></i>
    <?php echo _t('TRANSACTION_TYPE_' . strtoupper($type)) ?>
  </label>
<?php endforeach; ?>
<?php $view->endSquash() ?>