<?php
/**
 * Renders a single global link.  By default it is disabled.
 *
 * @var string $name
 * @var array $table_link
 */
?>
<?php $view->squash() ?>
<a data-id="<?php echo $name ?>" class="btn disabled <?php if (isset($table_link['link_class'])) echo $table_link['link_class'] ?>"
  <?php if (isset($table_link['external']) && $table_link['external']): ?>
    target="_blank"
  <?php endif; ?>
  <?php if (isset($table_link['js'])): ?>
    onclick="<?php echo htmlspecialchars($table_link['js']()) ?>"
  <?php endif; ?>
  <?php if (isset($table_link['url'])): ?>
    href="<?php echo $table_link['url']() ?>"
  <?php endif; ?>
>
  <?php echo isset($table_link['title']) ? $table_link['title'] : $name ?>
</a>
<?php $view->endSquash() ?>