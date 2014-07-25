<?php
/**
 * Policies input template.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $js_validation_options
 * @var string $value
 * @var array $options
 * @var boolean $valid
 * @var \Anthem\Core\View\ViewHelpers $view
 */
?>
<?php $view->squash() ?>
<dl>
<?php foreach ($options['all_policies'] as $_id => $policy_group): ?>
  <dt><?php echo _t('POLICY.' . $_id) ?></dt>
  <?php foreach ($policy_group as $_id): ?>
    <dd>
      <select id="<?php echo $id ?>_<?php echo $_id ?>"
              name="<?php echo $name ?>[<?php echo $_id ?>]"
              class="<?php echo $class ?><?php if (!$valid) echo ' error' ?>"
              <?php if (isset($options['readonly']) && $options['readonly']): ?>
                disabled
              <?php endif; ?>>
        <option value="own_enable"<?php if ($value[$_id] == 'own_enable') echo ' selected' ?>><?php echo _t('Auth.POLICY_ENABLE') ?></option>
        <option value="own_disable"<?php if ($value[$_id] == 'own_disable') echo ' selected' ?>><?php echo _t('Auth.POLICY_DISABLE') ?></option>
        <?php if (isset($options['inherited_policies'])): ?>
          <?php if (!empty($options['inherited_policies'][$_id])): ?>
            <option value="inherited_enable"<?php if ($value[$_id] == 'inherited_enable') echo ' selected' ?>><?php echo _t('Auth.POLICY_ENABLE_INHERITED') ?></option>
          <?php else: ?>
            <option value="inherited_disable"<?php if ($value[$_id] == 'inherited_disable') echo ' selected' ?>><?php echo _t('Auth.POLICY_DISABLE_INHERITED') ?></option>
          <?php endif; ?>
        <?php endif; ?>
      </select>
      <?php echo _t('POLICY.' . $_id) ?>
    </dd>
  <?php endforeach; ?>
<?php endforeach; ?>
</dl>
<?php $view->endSquash() ?>
