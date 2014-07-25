<?php
/**
 * Dashboard account mini-table template.
 *
 * @var \Mockingbird\Model\Account[] $accounts
 * @var float[integer]               $balances
 * @var string                       $title
 * @var string                       $empty
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>

<?php if (count($accounts) || $empty): ?>

<div class="dashboard-accounts-list">
  <h3><?php echo $title ?></h3>
  <table class="table table-bordered">
    <?php foreach ($accounts as $_account): ?>
      <tr>
        <td style="<?php if ($_account->getColor()) echo 'color: ' . $_account->getColor() ?>">
          <?php echo htmlspecialchars($_account->getTitle()) ?>
        </td>
        <td class="sum">
          <a href="<?php echo $link->url('transactions') ?>#filter.account_id=<?php echo $_account->getId() ?>">
            <?php echo $m->cc($balances[$_account->getId()], $_account->getCurrency()) ?>
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!count($accounts)): ?>
      <tr><td><?php echo $empty ?></td></tr>
    <?php endif; ?>
  </table>
</div>

<div style="text-align: right; font-size: x-large;">
  <?php echo $m->cc($sum) ?>
</div>

<?php endif; ?>