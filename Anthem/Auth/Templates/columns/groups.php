<?php
/**
 * Groups column value template.
 *
 * @var \Anthem\Auth\Model\User $object
 */
?>
<?php $groups = array() ?>
<?php foreach ($object->getGroups() as $group) $groups[] = $group->getTitle() ?>
<?php echo count($groups) ? implode($groups, ', ') : '&mdash;' ?>

