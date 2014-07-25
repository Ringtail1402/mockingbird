<?php
/**
 * Mockingbird dashboard accounts and balances template.
 *
 * @var string                       $date
 * @var \Mockingbird\Model\Account[] $normal_accounts
 * @var \Mockingbird\Model\Account[] $credit_accounts
 * @var \Mockingbird\Model\Account[] $debit_accounts
 * @var float[integer]               $balances
 * @var float                        $sum
 * @var float                        $sum_credits
 * @var float                        $sum_debits
 * @var float                        $sum_total
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>

<div class="row-fluid">
  <?php echo $view->sub('Mockingbird:dashboard_account.php', array('accounts' => $normal_accounts,
                                                                    'balances' => $balances,
                                                                    'title' => sprintf(_t('DASHBOARD_NORMAL_ACCOUNTS', $date)),
                                                                    'empty' => _t('DASHBOARD_NORMAL_ACCOUNTS_EMPTY') .
                                                                    (strtotime($date) >= strtotime(date('Y-m-d'))
                                                                      ? _t('DASHBOARD_NORMAL_ACCOUNTS_EMPTY_CREATE', $link->url('accounts') . '#id=0')
                                                                      : ''),
                                                                    'sum' => $sum)) ?>
  <?php echo $view->sub('Mockingbird:dashboard_account.php', array('accounts' => $debit_accounts,
                                                                    'balances' => $balances,
                                                                    'title' => _t('DASHBOARD_DEBIT_ACCOUNTS'),
                                                                    'empty' => '' /* _t('DASHBOARD_DEBIT_ACCOUNTS_EMPTY') */,
                                                                    'sum' => $sum_debits)) ?>
  <?php echo $view->sub('Mockingbird:dashboard_account.php', array('accounts' => $credit_accounts,
                                                                    'balances' => $balances,
                                                                    'title' => _t('DASHBOARD_CREDIT_ACCOUNTS'),
                                                                    'empty' => '' /* _t('DASHBOARD_CREDIT_ACCOUNTS_EMPTY') */,
                                                                    'sum' => $sum_credits)) ?>
</div>

<?php if (count($credit_accounts) || count($debit_accounts)): ?>
  <div class="dashboard-accounts-list">
    <h3><?php echo _t('TOTAL') ?></h3>
    <div style="text-align: right; font-size: xx-large;">
      <?php echo $m->cc($sum_total) ?>
    </div>
  </div>
<?php endif; ?>