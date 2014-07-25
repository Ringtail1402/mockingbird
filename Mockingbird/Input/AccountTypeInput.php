<?php

namespace Mockingbird\Input;

use Anthem\Forms\Input\VirtualInputInterface;
use Anthem\Forms\Input\SelectInput;
use Silex\Application;
use Mockingbird\Model\Account;

/**
 * An AccountForm field for isdebt/iscredit columns of an account.
 */
class AccountTypeInput extends SelectInput
                       implements VirtualInputInterface
{
  /**
   * The constructor.
   *
   * @param \Silex\Application $app
   * @param array              $options
   */
  public function __construct(Application $app, array $options = array())
  {
    $options['values'] = array(
      'normal' => _t('NORMAL_ACCOUNT_FULL'),
      'debit'  => _t('DEBIT_ACCOUNT_FULL'),
      'credit' => _t('CREDIT_ACCOUNT_FULL')
    );
    parent::__construct($app, $options);
  }

  /**
   * Loads a value from object.
   *
   * @param  Account $object
   * @return void
   * @throws \LogicException
   */
  public function load($object)
  {
    if (!$object instanceof Account)
      throw new \LogicException('AccountTypeInput may be used only with Account objects.');
    if (!$object->getIsdebt())
      $this->value = 'normal';
    elseif (!$object->getIscredit())
      $this->value = 'debit';
    else
      $this->value = 'credit';
  }

  /**
   * Saves a value into object.
   *
   * @param  Account $object
   * @return void
   * @throws \LogicException
   */
  public function save($object)
  {
    if (!$object instanceof Account)
      throw new \LogicException('AccountTypeInput may be used only with Account objects.');

    switch ($this->value)
    {
      case 'normal':
        $object->setIsdebt(false);
        $object->setIscredit(false);
        break;

      case 'debit':
        $object->setIsdebt(true);
        $object->setIscredit(false);
        break;

      case 'credit':
        $object->setIsdebt(true);
        $object->setIscredit(true);
        break;
    }
  }
}