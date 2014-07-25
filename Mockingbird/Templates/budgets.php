<?php
/**
 * Mockingbird budgets template.
 *
 * @var integer $first_year
 * @var integer $last_year
 * @var \Core\View\ViewHelpers $view
 * @var \Core\View\AssetHelpers $asset
 */
?>

<?php $view->extend($print ? 'Anthem/Admin:layout_print.php' : 'Anthem/Admin:layout.php') ?>
<?php $view->setSlot('title', _t('MENU_BUDGET')) ?>
<?php $view->setSlot('subtitle', _t('BUDGET_SUBTITLE')) ?>

<?php // CSS and Javascript includes ?>
<?php $view->beginSlot('head') ?>
  <link type="text/css" rel="stylesheet" href="<?php echo $asset->css('Anthem/Admin:table.css') ?>">
  <script type="text/javascript" src="<?php echo $asset->js('Anthem/Forms:propelsubforms.js') ?>"></script>
  <script type="text/javascript" src="<?php echo $asset->js('Mockingbird:budget.js') ?>"></script>
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">
    google.load('visualization', '1.0', {'packages':['corechart']});
    google.setOnLoadCallback(Budget.onChartsLoaded);
  </script>
  <?php echo $tour->init('mockingbird') ?>
<?php $view->endSlotAppend() ?>

<?php $view->beginSlot('global-actions') ?>
  <div class="global-actions">
    <a class="btn btn-primary" id="edit-link" style="display: none;"><i class="icon-pencil icon-white"></i> <?php echo _t('Admin.EDIT') ?></a>
    <a class="btn" target="_blank" href="?print=1" id="print-link" style="display: none;"><i class="icon-print"></i> <?php echo _t('Admin.PRINT') ?></a>
  </div>
<?php $view->endSlot() ?>

<div class="row-fluid">
  <div class="span12">
    <form id="budget-form">
      <label><?php echo _t('BUDGET_PROMPT') ?></label>
      <select name="month" id="budget-month">
        <option value="all"><?php echo _t('BUDGET_ENTIRE_YEAR') ?></option>
        <?php for ($i = 1; $i <= 12; $i++): ?>
          <option value="<?php echo $i ?>"<?php if ($i == date('m')) echo ' selected' ?>>
            <?php echo strftime('%B', mktime(0, 0, 0, $i, 1, 2000)) ?>
          </option>
        <?php endfor; ?>
      </select>
      <select name="year" id="budget-year">
        <?php for ($i = $first_year; $i <= $last_year; $i++): ?>
          <option value="<?php echo $i ?>"<?php if ($i == date('Y')) echo ' selected' ?>><?php echo $i ?></option>
        <?php endfor; ?>
      </select>
      <select name="as" id="budget-as">
        <option value="table" selected><?php echo _t('BUDGET_TABLE') ?></option>
        <option value="chart"><?php echo _t('BUDGET_CHART') ?></option>
      </select>
      <label>:</label>
      <br>
      <small class="muted" style="margin-bottom: 10px; display: block;"><?php echo _t('WARNING_CURRENT_RATES') ?></small>
    </form>

    <div id="budget-container"></div>
    <div class="chart-container" id="chart-income-container"></div>
    <div class="chart-container" id="chart-expense-container" style="margin-top: 20px;"></div>
    <form id="form-container"></form>
  </div>
</div>

<?php $view->beginSlot('footer') ?>
  <div class="form-horizontal">
    <div id="form-links-container" class="form-actions" style="margin-top: -15px; display: none;">
      <a class="btn btn-primary" onclick="Budget.save(); return false;"><i class="icon-ok icon-white"></i> <?php echo _t('Admin.SAVE') ?></a>
      <a class="btn" onclick="Budget.cancelEdit(); return false;"><i class="icon-arrow-left"></i> <?php echo _t('Admin.BACK') ?></a>
    </div>
  </div>
<?php $view->endSlotAppend() ?>
