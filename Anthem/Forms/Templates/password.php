<?php
/**
 * Password input template.  Trivial.
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
<input
        type="password"
        id="<?php echo $id ?>"
        name="<?php echo $name ?>"
        class="<?php echo $class ?><?php if (!$valid) echo ' error' ?>"
    <?php echo $js_validation_options ?>
        value="<?php $view->endSquash() ?><?php echo !empty($options['keep_value']) ? htmlspecialchars($value) : '' ?><?php $view->squash() ?>"
    <?php if (isset($options['readonly']) && $options['readonly']): ?>
        readonly
  <?php endif; ?>
        >
<?php $view->endSquash() ?>