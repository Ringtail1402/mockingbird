<?php

namespace Mockingbird\Input;

use Anthem\Forms\Input\VirtualInputInterface;
use Anthem\Forms\Input\StringInput;
use Silex\Application;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\CounterParty;

/**
 * A TransactionForm field for counter party column of a transaction.
 */
class TransactionCounterPartyInput extends StringInput
                                   implements VirtualInputInterface
{
  /**
   * Loads a value from object.
   *
   * @param  Transaction $object
   * @return void
   * @throws \LogicException
   */
  public function load($object)
  {
    if (!$object instanceof Transaction)
      throw new \LogicException('TransactionCounterPartyInput may be used only with Transaction objects.');
    if ($object->getCounterPartyId())
      $this->setValue($object->getCounterParty()->getTitle());
    else
      $this->setValue('');
  }

  /**
   * Saves a value into object.
   *
   * @param  Transaction $object
   * @return void
   * @throws \LogicException
   */
  public function save($object)
  {
    if (!$object instanceof Transaction)
      throw new \LogicException('TransactionCounterPartyInput may be used only with Transaction objects.');

    // No counter party?
    $title = trim($this->value);
    if (!$title || $object->getTargetAccountId())
    {
      $object->setCounterPartyId(null);
      return;
    }

    // Look up counter party by title, create it if not found
    $counter_party = $this->app['mockingbird.model.counterparty']->findOneByTitle($title);
    if (!$counter_party)
    {
      $counter_party = new CounterParty();
      $counter_party->setUser($this->options['user']);
      $counter_party->setTitle($title);
      $counter_party->save();
    }

    $object->setCounterPartyId($counter_party->getId());
    if ($object->getCounterTransaction())
      $object->getCounterTransaction()->setCounterPartyId($counter_party->getId());
  }
}