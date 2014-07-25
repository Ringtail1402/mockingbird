<?php
/**
 * Currency denomination column value template.
 *
 * @var object $object
 */
?>
<?php if ($object->getCurrency()->getIsprimary()) echo '<b>' ?>
  <?php echo htmlspecialchars($object->getCurrency()->getTitle()) ?>
<?php if ($object->getCurrency()->getIsprimary()) echo '</b>' ?>

