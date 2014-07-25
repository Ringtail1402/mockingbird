<?php

namespace Mockingbird\Model;

use Mockingbird\Model\om\BaseTransaction;

/**
 * Transaction class.
 */
class Transaction extends BaseTransaction
{
  public function getCurrency()
  {
    return $this->getAccount()->getCurrency();
  }
}
