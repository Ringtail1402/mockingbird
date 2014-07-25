<?php
/**
 * Transaction list template.
 *
 * @var \Anthem\Admin\Admin\ListAdminPage $admin_page
 * @var \Mockingbird\Model\Transaction[] $records
 * @var array $links
 * @var array $actions
 * @var \Anthem\Core\View\ViewHelpers $view
 * @var \Mockingbird\View\MockingbirdHelpers $m
 */
?>
<?php $old_group_value = null; $group_value = null ?>
<?php
  $amounts = array(
    '-100000' => _t('SPENDING_OVER', $m->c(100000)),
      '-50000' => _t('SPENDING_IN', $m->c(50000), $m->c(100000)),
      '-10000' => _t('SPENDING_IN', $m->c(10000), $m->c(50000)),
       '-5000' => _t('SPENDING_IN', $m->c(5000), $m->c(10000)),
       '-1000' => _t('SPENDING_IN', $m->c(1000), $m->c(5000)),
        '-500' => _t('SPENDING_IN', $m->c(500), $m->c(1000)),
        '-100' => _t('SPENDING_IN', $m->c(100), $m->c(500)),
         '-50' => _t('SPENDING_IN', $m->c(50), $m->c(100)),
         '-10' => _t('SPENDING_IN', $m->c(10), $m->c(50)),
           '0' => _t('SPENDING_LESS', $m->c(10)),
        '9.99' => _t('INCOME_LESS', $m->c(10)),
       '49.99' => _t('INCOME_IN', $m->c(10), $m->c(50)),
       '99.99' => _t('INCOME_IN', $m->c(50), $m->c(100)),
      '499.99' => _t('INCOME_IN', $m->c(100), $m->c(500)),
      '999.99' => _t('INCOME_IN', $m->c(500), $m->c(1000)),
     '4999.99' => _t('INCOME_IN', $m->c(1000), $m->c(5000)),
     '9999.99' => _t('INCOME_IN', $m->c(5000), $m->c(10000)),
    '49999.99' => _t('INCOME_IN', $m->c(10000), $m->c(50000)),
    '99999.99' => _t('INCOME_IN', $m->c(50000), $m->c(100000)),
           '*' => _t('INCOME_OVER', $m->c(100000)),
  );
?>
<?php foreach ($records as $record): ?>
  <?php
    // Determine section title
    switch ($admin_page->getSortColumn())
    {
      case 'created_at':
        $group_value = strftime('%x', $record->getCreatedAt('U'));
        break;

      case 'isprojected':
        $group_value = $record->getIsprojected() ? _t('ISPROJECTED_YES') : _t('ISPROJECTED_NO');
        break;

      case 'title':
        $group_value = htmlspecialchars($record->getTitle());
        break;

      case 'account_id':
        $group_value = htmlspecialchars($record->getAccount()->getTitle());
        break;

      case 'amount':
        foreach ($amounts as $limit => $title)
        {
          if ($limit === '*' || (float)$record->getVirtualColumn('TotalAmount') < (float)$limit)
          {
            $group_value = $title;
            break;
          }
        }
        break;

      case 'target':
        $group_value = $record->getCounterPartyId()
                       ? htmlspecialchars($record->getCounterParty()->getTitle())
                       : htmlspecialchars($record->getTargetAccount()->getTitle());
        break;

      case 'tagging':
        $group_value = $record->getCategoryId()
                       ? htmlspecialchars($record->getCategory()->getTitle())
                       : _t('NO_CATEGORY');
        break;

      case 'user':
        $group_value = htmlspecialchars($record->getUser()->getEmail());
        break;
    }
  ?>
  <?php if ($group_value != $old_group_value): ?>
    <h3><?php echo $group_value ?></h3>
    <?php $old_group_value = $group_value ?>
    <?php $border = false ?>
  <?php else: ?>
    <?php $border = true ?>
  <?php endif; ?>

  <?php echo $view->sub('Mockingbird:transaction.php', array('transaction' => $record, 'border' => $border,
                                                             'sort_column' => $admin_page->getSortColumn(),
                                                             'links' => $links, 'actions' => $actions, 'admin_page' => $admin_page)) ?>
<?php endforeach; ?>
