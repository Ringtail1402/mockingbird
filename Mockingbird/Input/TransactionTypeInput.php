<?php

namespace Mockingbird\Input;

use Anthem\Forms\Input\BaseInput;
use Anthem\Forms\Input\VirtualInputInterface;
use Mockingbird\Model\Transaction;

/**
 * A control for TransactionForm which allows selection of transaction type
 * (income/spending/transfer).
 */
class TransactionTypeInput extends BaseInput
                           implements VirtualInputInterface
{
  /**
   * Loads input from a transaction object.
   *
   * @param  Transaction $object
   * @return void
   * @throws \LogicException
   */
  public function load($object)
  {
    if (!$object instanceof Transaction)
      throw new \LogicException('TransactionTypeInput may be used only with Transaction objects.');

    if ($object->getTargetAccountId())
      $this->value = 'transfer';
    elseif ($object->getAmount() == '0.00' && $object->getId())
      $this->value = 'master';
    elseif ($object->getAmount() > 0)
      $this->value = 'income';
    else
      $this->value = 'spending';
  }

  /**
   * Saves input to a transaction object.
   *
   * @param  Transaction $object
   * @return void
   * @throws \LogicException
   */
  public function save($object)
  {
    if (!$object instanceof Transaction)
      throw new \LogicException('TransactionTypeInput may be used only with Transaction objects.');

    // Reset fields not applicable for this type, set up counter-transaction
    if ($this->value == 'transfer')
    {
      $object->setCounterPartyId(null);
      if (!$object->getCounterTransaction())
        $object->setCounterTransaction(new Transaction());
      $object->getCounterTransaction()->setUser($object->getUser());
      $object->getCounterTransaction()->setTitle($object->getTitle());
      $object->getCounterTransaction()->setAccount($object->getTargetAccount());
      $object->getCounterTransaction()->setTargetAccount($object->getAccount());
      $object->getCounterTransaction()->setCounterTransaction($object);
      $object->getCounterTransaction()->setCreatedAt($object->getCreatedAt());
      $object->getCounterTransaction()->save();
    }
    else
    {
      if ($object->getCounterTransactionId())
      {
        $object->getCounterTransaction()->delete();
        $object->setCounterTransaction(null);
      }
      $object->setTargetAccountId(null);
    }

    if ($this->value == 'master')
      $object->setAmount(0);
    else
    {
      // Normalize amount sign
      $object->setAmount(sprintf('%.2F', abs($object->getAmount())));
      if ($this->value == 'transfer' || $this->value == 'spending')
        $object->setAmount(sprintf('%.2F', -$object->getAmount()));
    }
  }

 /**
  * Returns template used.
  *
  * @return string
  */
  protected function getDefaultTemplate()
  {
    return 'Mockingbird:input/transaction_type.php';
  }
}