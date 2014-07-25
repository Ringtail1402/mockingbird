// JS with tour screens and custom conditions
var TourScreens = {
<?php $i = 0; foreach ($screens as $id => $screen): if (!empty($screen['text']['title'])) $screen['text']['title'] = $view->str($screen['text']['title']) ?>
  <?php echo str_replace('.', '_', $id) ?>: function () {
    if (!TourCommon.shownScreens['<?php echo $id ?>']<?php if (!empty($screen['custom_client_condition'])) echo ' && (' . $screen['custom_client_condition'] . ')' ?>)
      TourCommon.showScreen('<?php echo $tour ?>', '<?php echo $id ?>', <?php echo json_encode($screen['text']) ?>);
  }<?php if ($i < count($screens) - 1) echo ',' ?>
<?php endforeach; ?>
};
