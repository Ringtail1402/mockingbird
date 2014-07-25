<?php

namespace Mockingbird\Input;

use Anthem\Forms\Input\VirtualInputInterface;
use Anthem\Forms\Input\BaseInput;
use Silex\Application;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\TransactionCategory;

/**
 * A TransactionForm field for category column of a transaction.
 */
class TransactionCategoryInput extends BaseInput
                               implements VirtualInputInterface
{
  static protected $saved_categories = array();

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
      throw new \LogicException('TransactionCategoryInput may be used only with Transaction objects.');
    $this->value = array('id' => $object->getCategoryId(), 'new' => '');
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
      throw new \LogicException('TransactionCategoryInput may be used only with Transaction objects.');

    // Default to no category
    $object->setCategoryId(null);

    $id = $this->value['id'];
    if ($id)
    {
      if ($id == -1)
      {
        $title = trim($this->value['new']);

        if ($title)
        {
          // Already saved somewhere earier?
          // XXX This is necessary so that a new category will not be added twice if it occurs twice
          // in different fields of the same form.
          if (isset(self::$saved_categories[$title]))
            $category = self::$saved_categories[$title];
          else
          {
            // Create new category
            $category = new TransactionCategory();
            $category->setUser($object->getUser());
            $category->setTitle($title);
            self::$saved_categories[$title] = $category;
          }
          $object->setCategory($category);
        }
      }
      else
        $object->setCategoryId($id);
    }

    if ($object->getCounterTransaction())
      $object->getCounterTransaction()->setCategory($object->getCategory());
  }

  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Mockingbird:input/transaction_category.php';
  }
}