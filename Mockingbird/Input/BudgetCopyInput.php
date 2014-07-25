<?php

namespace Mockingbird\Input;

use Anthem\Forms\Input\VirtualInputInterface;
use Anthem\Forms\Input\SelectInput;
use Silex\Application;
use Mockingbird\Model\Budget;

/**
 * A BudgetForm field which allows setting BudgetEntries from another Budget object.
 */
class BudgetCopyInput extends SelectInput
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
    $options['values'] = array(null => '');

    foreach ($app['mockingbird.model.budget']->getAll() as $budget)
    {
      if ($options['except'] && $budget->getId() == $options['except']) continue;

      if ($budget->getMonth())
        $options['values'][$budget->getId()] = strftime('%B %Y', mktime(0, 0, 0, $budget->getMonth(), 1, $budget->getYear()));
      else
        $options['values'][$budget->getId()] = $budget->getYear();
    }

    parent::__construct($app, $options);
  }

  /**
   * Loads a value from object.
   *
   * @param  Budget $object
   * @return void
   * @throws \LogicException
   */
  public function load($object)
  {
    if (!$object instanceof Budget)
      throw new \LogicException('BudgetCopyInput may be used only with Budget objects.');
    $this->value = null;  // always default to null
  }

  /**
   * Saves a value into object.
   *
   * @param  Budget $object
   * @return void
   * @throws \LogicException
   */
  public function save($object)
  {
    if (!$object instanceof Budget)
      throw new \LogicException('BudgetCopyInput may be used only with Budget objects.');

    if ($this->value)
    {
      /** @var Budget $origin */
      $origin = $this->app['mockingbird.model.budget']
                     ->filterByUser($this->options['user'])
                     ->find($this->value);
      if ($origin)
      {
        foreach ($origin->getEntrys() as $entry)
        {
          $entry = $entry->copy();
          $object->addEntry($entry);
        }
      }
    }
  }
}
