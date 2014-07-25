<?php
/**
 * Mockingbird charts template.
 *
 * @var integer $first_year
 * @var integer $last_year
 * @var \Core\View\ViewHelpers $view
 * @var \Core\View\AssetHelpers $asset
 */
?>

<?php $view->extend($print ? 'Anthem/Admin:layout_print.php' : 'Anthem/Admin:layout.php') ?>
<?php $view->setSlot('title', _t('MENU_CHARTS')) ?>
<?php $view->setSlot('subtitle', _t('CHART_SUBTITLE')) ?>

<?php // CSS and Javascript includes ?>
<?php $view->beginSlot('head') ?>
  <link type="text/css" rel="stylesheet" href="<?php echo $asset->css('Anthem/Admin:table.css') ?>">
  <script type="text/javascript" src="<?php echo $asset->js('Mockingbird:charts.js') ?>"></script>
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">
    google.load('visualization', '1.0', {'packages':['corechart']});
    google.setOnLoadCallback(Charts.loadChart);
  </script>
<?php $view->endSlotAppend() ?>

<?php $view->beginSlot('global-actions') ?>
  <div class="global-actions table-view">
    <a class="btn" target="_blank" href="?print=1" id="print-link"><i class="icon-print"></i> <?php echo _t('Admin.PRINT') ?></a>
  </div>
<?php $view->endSlot() ?>

<div class="row-fluid">
  <div class="span12">
    <form id="charts-form">
      <label for="chart-type"><?php echo _t('CHART_PROMPT1') ?></label>
      <select name="type" id="chart-type">
        <option value="pie" selected><?php echo _t('CHART_PIE') ?></option>
        <option value="time"><?php echo _t('CHART_TIME') ?></option>
      </select>
      <label for="chart-for"><?php echo _t('CHART_PROMPT2') ?></label>
      <select name="for" id="chart-for">
        <option value="balance"><?php echo _t('CHART_BALANCE') ?></option>
        <option value="income"><?php echo _t('CHART_INCOME') ?></option>
        <option value="expense" selected><?php echo _t('CHART_SPENDING') ?></option>
      </select>
      <label for="chart-by"><?php echo _t('CHART_PROMPT3') ?></label>
      <select name="by" id="chart-by">
        <option value="category" selected><?php echo _t('CHART_CATEGORIES') ?></option>
        <option value="account"><?php echo _t('CHART_ACCOUNTS') ?></option>
      </select>
      <label for="chart-in"><?php echo _t('CHART_PROMPT4') ?></label>
      <select name="in" id="chart-in">
        <option value="all"><?php echo _t('CHART_ALL_TIME') ?></option>
        <option value="year"><?php echo _t('CHART_YEAR') ?></option>
        <option value="month" selected><?php echo _t('CHART_MONTH') ?></option>
      </select>
      <select name="month" id="chart-month">
        <?php for ($i = 1; $i <= 12; $i++): ?>
          <option value="<?php echo $i ?>"<?php if ($i == date('m')) echo ' selected' ?>>
            <?php echo strftime('%B', mktime(0, 0, 0, $i, 1, 2000)) ?>
          </option>
        <?php endfor; ?>
      </select>
      <select name="year" id="chart-year">
        <?php for ($i = $first_year; $i <= $last_year; $i++): ?>
          <option value="<?php echo $i ?>"<?php if ($i == date('Y')) echo ' selected' ?>><?php echo $i ?></option>
        <?php endfor; ?>
      </select>
      <label>:</label>
    </form>
    <small class="muted"><?php echo _t('WARNING_CURRENT_RATES') ?></small>

    <div id="charts-container"></div>
  </div>
</div>
