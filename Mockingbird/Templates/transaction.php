<?php
/**
 * Single transaction output template.
 *
 * @var \Mockingbird\Model\Transaction $transaction
 * @var \Mockingbird\Admin\TransactionAdmin $admin_page
 * @var boolean $border
 * @var string $sort_column
 * @var array $links
 * @var array $actions
 * @var \Mockingbird\View\MockingbirdHelpers $m
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Anthem\Admin\View\AdminHelpers $admin
 */
?>
<div class="transaction clearfix<?php if ($border && (!$transaction->getParentTransactionId() || $admin_page->exclude_parent_transactions)) echo ' bordered'
                              ?><?php if ($transaction->getParentTransactionId()) echo ' sub'
                              ?><?php if ($admin_page->exclude_parent_transactions) echo ' exclude-parent'
                              ?><?php if ($transaction->getIsprojected()) echo ' projected'
                              ?><?php if ($sort_column != 'created_at') echo ' with-date' ?>"
     data-id="<?php echo $transaction->getId() ?>"<?php if ($transaction->getParentTransactionId()) echo ' data-parent-id="' . $transaction->getParentTransactionId() . '"' ?>>
    <div class="datetime">
      <input type="checkbox"
             class="mass-selector"
             data-id="<?php echo $transaction->getPrimaryKey() ?>">
      <?php echo strftime('%R', $transaction->getCreatedAt('U')) ?>
      <?php if ($sort_column != 'created_at'): ?>
        <div class="date"><?php echo strftime('%x', $transaction->getCreatedAt('U')) ?></div>
      <?php endif; ?>
      <?php if ($transaction->getIsprojected()): ?>
        <div><?php echo _t('TRANSACTION_PROJECTED') ?></div>
      <?php endif; ?>
    </div>
    <div class="amount">
      <?php $amount = $transaction->getVirtualColumn('TotalAmount') ?>
      <?php echo $m->cc($amount, $transaction->getAccount()->getCurrency(), true) ?>
    </div>
    <div class="main">
      <div class="stripe"></div>
      <div class="title">
        <?php if ($transaction->getAmount() == '0.00'): ?>
          <a class="transactions-toggle" data-id="<?php echo $transaction->getId() ?>">
            <span><?php echo htmlspecialchars($transaction->getTitle()) ?></span>
            <i class="icon-chevron-down chevron"></i>
          </a>
        <?php else: ?>
          <?php if ($transaction->getParentTransactionId() && $admin_page->exclude_parent_transactions): ?>
            <b><?php echo htmlspecialchars($transaction->getParentTransaction()->getTitle()) ?></b>&nbsp;<i class="icon-chevron-right"></i>
          <?php endif; ?>
          <?php echo htmlspecialchars($transaction->getTitle()) ?>
        <?php endif; ?>

        <?php if (!$transaction->getParentTransactionId() || $admin_page->exclude_parent_transactions): ?>
          <div class="actions">
            <?php if ($admin_page->testEdit($transaction)): ?>
              <?php $id = $transaction->getId() ?>
              <?php if ($transaction->getCounterTransactionId() && $transaction->getAmount() > 0) $id = $transaction->getCounterTransactionId() ?>
              <?php if ($transaction->getParentTransactionId()) $id = $transaction->getParentTransactionId() ?>
              <a class="btn btn-small btn-primary" href="#id=<?php echo $id ?>" onclick="TableAdmin.edit(<?php echo $id ?>); return false;">
                <i class="icon-pencil icon-white"></i> <?php echo _t('Admin.EDIT') ?>
              </a>
            <?php endif; ?>
            <?php foreach($links as $name => $link): ?>
              <?php echo $view->sub($admin->getTemplate('link'), array('name' => $name, 'link' => $link, 'object' => $transaction)) ?>
            <?php endforeach; ?>
            <?php foreach($actions as $name => $action): ?>
              <?php echo $view->sub($admin->getTemplate('action'), array('name' => $name, 'action' => $action, 'object' => $transaction)) ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="text">
        <?php if ($transaction->getCategoryId()): ?>
          <i class="icon-bookmark"></i>
          <?php echo htmlspecialchars($transaction->getCategory()->getTitle()) ?>
        <?php endif; ?>
        <?php if ($transaction->getTransactionTags()->count()): ?>
          <i class="icon-tags" style="<?php if ($transaction->getCategoryId()) echo 'margin-left: 32px;' ?>"></i>
          <?php foreach ($transaction->getTransactionTags() as $i => $tag): ?>
            <?php echo htmlspecialchars($tag->getTitle()) ?><?php if ($i != $transaction->getTransactionTags()->count() - 1) echo ',' ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <?php if (!$transaction->getParentTransactionId()): ?>
        <div class="text">
          <?php if ($transaction->getCounterPartyId() && $amount > 0): ?>
            <span class="legend"><?php echo _t('TRANSACTION_LEGEND_FROM') ?>:</span>
          <?php echo htmlspecialchars($transaction->getCounterParty()->getTitle()) ?>
          <?php endif; ?>
        </div>
        <div class="text">
          <span class="legend">
            <?php echo (($transaction->getCounterPartyId() && $amount > 0) ||
                        ($transaction->getTargetAccountId() && $amount > 0))
                       ? _t('TRANSACTION_LEGEND_TO_ACCOUNT')
                       : _t('TRANSACTION_LEGEND_FROM_ACCOUNT') ?>:
          </span>
          <?php echo htmlspecialchars($transaction->getAccount()->getTitle()) ?>
        </div>
        <div class="text">
          <?php if ($transaction->getTargetAccountId()): ?>
            <span class="legend">
              <?php echo ($amount < 0)
                         ? _t('TRANSACTION_LEGEND_TO_ACCOUNT')
                         : _t('TRANSACTION_LEGEND_FROM_ACCOUNT') ?>:
            </span>
          <?php echo htmlspecialchars($transaction->getTargetAccount()->getTitle()) ?>
          <?php endif; ?>
          <?php if ($transaction->getCounterPartyId() && $amount < 0): ?>
            <span class="legend"><?php echo _t('TRANSACTION_LEGEND_TO') ?>:</span>
          <?php echo htmlspecialchars($transaction->getCounterParty()->getTitle()) ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <div style="height: 8px;">&nbsp;</div>
    </div>
</div>
