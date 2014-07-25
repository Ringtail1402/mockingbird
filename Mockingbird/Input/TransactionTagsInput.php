<?php

namespace Mockingbird\Input;

use Anthem\Forms\Input\VirtualInputInterface;
use Anthem\Forms\Input\BaseInput;
use Silex\Application;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\TransactionTag;

/**
 * A TransactionForm field for tags column of a transaction.
 */
class TransactionTagsInput extends BaseInput
                            implements VirtualInputInterface
{
  static protected $saved_tags = array();

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
      throw new \LogicException('TransactionTagsInput may be used only with Transaction objects.');
    $this->value = array();
    foreach ($object->getRefTransactionTagsJoinTransactionTag() as $rtt)
      $this->value[] = array('id' => $rtt->getTagId(), 'title' => $rtt->getTransactionTag()->getTitle());
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
      throw new \LogicException('TransactionTagsInput may be used only with Transaction objects.');

    $tags = new \PropelCollection();
    $tags_saved = array();
    // Iterate over tags
    foreach ($this->value as $_value)
    {
      $tag = null;

      // Look up by id
      if (!empty($_value['id']))
        $tag = $this->app['mockingbird.model.tag']->find($_value['id']);

      if (!$tag || $tag->getUserId() != $object->getUser()->getId())
      {
        // Look up by title
        $title = trim($_value['title']);
        if ($title)
        {
          // Already saved for this object?
          if (isset($tags_saved[$title])) continue;

          // Already saved somewhere earier?
          // XXX This is necessary so that a new tag will not be added twice if it occurs twice
          // in different fields of the same form.
          if (isset(self::$saved_tags[$title]))
            $tag = self::$saved_tags[$title];
          else
          {
            $tag = $this->app['mockingbird.model.tag']->findOneByTitle($title);
            // Create if not found
            if (!$tag)
            {
              $tag = new TransactionTag();
              $tag->setUser($object->getUser());
              $tag->setTitle($title);
              self::$saved_tags[$title] = $tag;
            }
          }
        }
      }

      if ($tag)
      {
        $tags[] = $tag;
        $tags_saved[$tag->getTitle()] = $tag;
      }
    }

    $object->setTransactionTags($tags);
    if ($object->getCounterTransaction())
      $object->getCounterTransaction()->setTransactionTags($tags);
  }

  /**
   * Returns template used.
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return 'Mockingbird:input/transaction_tags.php';
  }
}