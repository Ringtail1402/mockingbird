<?php
/**
 * Account column value template.
 *
 * @var object $object
 */
?>
<span style="color: <?php echo $object->getAccount()->getColor() ?>">
  <?php echo htmlspecialchars($object->getAccount()->getTitle()) ?>
</span>
