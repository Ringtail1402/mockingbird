<?php

namespace Mockingbird\Input;

use Anthem\Forms\Input\BaseInput;
use Anthem\Forms\Input\VirtualInputInterface;
use Mockingbird\Model\BudgetEntry;

/**
 * A control for BudgetEntryForm which allows selection of transaction type
 * (income/spending).
 */
class BudgetEntryTypeInput extends BaseInput
                           implements VirtualInputInterface
{
  /**
   * Loads input from a transaction object.
   *
   * @param  BudgetEntry $object
   * @return void
   * @throws \LogicException
   */
  public function load($object)
  {
    if (!$object instanceof BudgetEntry)
      throw new \LogicException('BudgetEntryTypeInput may be used only with BudgetEntry objects.');

    if ($object->getAmount() > 0)
      $this->value = 'income';
    else
      $this->value = 'spending';
  }

  /**
   * Saves input to a transaction object.
   *
   * @param  BudgetEntry $object
   * @return void
   * @throws \LogicException
   */
  public function save($object)
  {
    if (!$object instanceof BudgetEntry)
      throw new \LogicException('BudgetEntryTypeInput may be used only with BudgetEntry objects.');

    // Normalize amount sign
    $object->setAmount(sprintf('%.2F', abs($object->getAmount())));
    if ($this->value == 'spending')
      $object->setAmount(sprintf('%.2F', -$object->getAmount()));
  }

 /**
  * Returns template used.
  *
  * @return string
  */
  protected function getDefaultTemplate()
  {
    return 'Mockingbird:input/budget_entry_type.php';
  }
}