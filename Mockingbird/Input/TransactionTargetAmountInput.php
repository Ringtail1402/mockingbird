<?php

namespace Mockingbird\Input;

use Anthem\Forms\Input\VirtualInputInterface;
use Mockingbird\Input\CurrencyInput;
use Silex\Application;
use Mockingbird\Model\Transaction;

/**
 * A TransactionForm field for amount column of target transaction.
 */
class TransactionTargetAmountInput extends CurrencyInput
                                   implements VirtualInputInterface
{
  /**
   * Loads a value from object.
   *
   * @param  $object
   * @return void
   * @throws \LogicException
   */
  public function load($object)
  {
    if (!$object instanceof Transaction)
      throw new \LogicException('TransactionTargetAmountInput may be used only with Transaction objects.');
    if ($object->getCounterTransactionId())
      $this->setValue($object->getCounterTransaction()->getAmount());
    else
      $this->setValue(0);
  }

  /**
   * Saves a value into object.
   *
   * @param  $object
   * @return void
   * @throws \LogicException
   */
  public function save($object)
  {
    if (!$object instanceof Transaction)
      throw new \LogicException('TransactionTargetAmountInput may be used only with Transaction objects.');

    if ($object->getCounterTransaction())
      $object->getCounterTransaction()->setAmount(sprintf('%.2F', abs($this->value)));
  }
}