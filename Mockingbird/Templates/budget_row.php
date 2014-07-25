<?php
/**
 * Mockingbird budget table row template (incomes or expenses).
 *
 * @var \Mockingbird\Model\Budget $budget
 * @var string $title
 * @var array $values
 * @var boolean $income
 * @var \Core\View\ViewHelpers $view
 */
?>
<?php $format = str_replace('#', '%.2f', $app['mockingbird.model.currency']->getDefaultCurrency()->getFormat()) ?>
<td>
  <?php if (isset($title)): ?>
    <?php if ($title == '*'): ?>
      <?php echo _t('BUDGET_TOTAL') ?>
    <?php elseif (!$title): ?>
      <?php echo _t('BUDGET_LEFTOVER') ?>
    <?php else: ?>
      <span style="color: <?php echo $values['color'] ?>;"><?php echo htmlspecialchars($title) ?></span>
    <?php endif; ?>
  <?php endif; ?>
  <?php if ($values['description']): ?>
    <div class="description muted"><?php echo htmlspecialchars($values['description']) ?></div>
  <?php endif; ?>
</td>
<td class="date">
  <?php if (isset($values['when'])): ?>
    <?php if ($values['when']): ?>
      <?php if ($budget->getMonth()): ?>
        <?php $date = mktime(0, 0, 0, $budget->getMonth(), $values['when'], $budget->getYear()) ?>
        <?php echo strftime('%x', $date) ?>
      <?php else: ?>
        <?php $date = mktime(0, 0, 0, $values['when'], 1, $budget->getYear()) ?>
        <?php echo strftime('%B %Y', $date) ?>
      <?php endif; ?>
    <?php else: ?>
      <span class="muted"><?php echo _t('BUDGET_WITHINPERIOD') ?></span>
    <?php endif; ?>
  <?php endif; ?>
</td>
<td class="figures"><?php echo sprintf($format, $values['estimated_total']) ?></td>
<td class="figures"><?php echo sprintf($format, $values['estimated_current']) ?></td>
<td class="figures"><?php if (isset($values['actual'])) echo sprintf($format, $values['actual']) ?></td>
<td class="figures">
  <?php if (isset($values['percent']) && $values['percent'] !== null): ?>
    <?php if (abs($values['percent']) > 5): ?>
      <span class="<?php echo (($income && $values['percent'] > 5) || (!$income && $values['percent'] < 5)) ? 'good' : 'bad' ?>percent">
    <?php endif; ?>
    <?php echo $values['percent'] > 0 ? '+' : ($values['percent'] < 0 ? '&ndash;' : '') ?><?php echo sprintf('%.2f%%', abs($values['percent'])) ?>
    <?php if (abs($values['percent']) > 5): ?>
      </span>
    <?php endif; ?>
  <?php endif; ?>
</td>
