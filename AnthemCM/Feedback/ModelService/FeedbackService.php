<?php

namespace AnthemCM\Feedback\ModelService;

use Anthem\Propel\ModelService\PropelModelService;

/**
 * Model service for Feedback model.
 */
class FeedbackService extends PropelModelService
{
  public function getModelClass()
  {
    return 'AnthemCM\\Feedback\\Model\\Feedback';
  }

  /**
   * Counts existing messages.
   *
   * @return integer
   */
  public function countFeedbacks()
  {
    return $this->createQuery()
                ->count();
  }
}
