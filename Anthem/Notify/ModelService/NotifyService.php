<?php

namespace Anthem\Notify\ModelService;

use Silex\Application;
use Anthem\Propel\ModelService\PropelModelService;
use Anthem\Notify\Model\Notification;
use Anthem\Notify\Model\NotificationQuery;

/**
 * Model service for Notification model.
 */
class NotifyService extends PropelModelService
{
  /**
   * @var Application
   */
  protected $app;

  public function __construct(Application $app)
  {
    $this->app = $app;
  }

  /**
   * Adds a transient (flash) notification.
   * These are stored in the session and are deleted on first retrieve.
   *
   * @param string $message
   * @param string $class
   * @return void
   */
  public function addTransient($message, $class = null)
  {
    $notices = $this->app['session']->get('anthem.notify.transient');
    if (!$notices) $notices = array();
    $notices[date('Y-m-d H:i:s')] = array('uniqid' => null, 'message' => $message, 'class' => $class, 'close_button' => true, 'ajax_dismiss' => false);
    $this->app['session']->set('anthem.notify.transient', $notices);
  }

  /**
   * Adds a persistent notification.  These are stored in database and deleted explicitly.
   * If uniqid is null, it will be generated randomly.
   * If a message with this uniqid already exists, it will get replaced.
   *
   * @param string  $message
   * @param string|null  $class
   * @param string|null $uniqid
   * @param boolean $can_close
   * @param \Anthem\Auth\Model\User $user
   * @return string
   * @throws \LogicException
   */
  public function addPersistent($message, $class = null, $uniqid = null, $can_close = true, $user = null)
  {
    if (!$user && $this->app['Auth']['enable'])
    {
      if ($this->app['auth']->isGuest())
        throw new \LogicException('Trying to create a persistent notification for a guest user.');
      $user = $this->app['auth']->getUser();
    }

    $notice = NotificationQuery::create()
                               ->filterByUniqid($uniqid)
                               ->_if($user)
                                 ->filterByUser($user)
                               ->_endif()
                               ->findOne();
    if (!$notice) $notice = new Notification();

    $notice->setUniqid($uniqid ? $uniqid : uniqid('', true));
    $notice->setUser($user);
    $notice->setMessage($message);
    $notice->setOutputClass($class);
    $notice->setNoDismiss(!$can_close);
    $notice->save();
    return $notice->getUniqid();
  }

  /**
   * Deletes a persistent notification.
   *
   * @param string $uniqid
   * @param \Anthem\Auth\Model\User $user
   * @return void
   * @throws \LogicException
   */
  public function removePersistent($uniqid, $user = null)
  {
    if (!$user && $this->app['Auth']['enable'])
    {
      if ($this->app['auth']->isGuest())
        throw new \LogicException('Trying to create a persistent notification for a guest user.');
      $user = $this->app['auth']->getUser();
    }

     NotificationQuery::create()
                      ->filterByUniqid($uniqid)
                      ->_if($user)
                        ->filterByUser($user)
                      ->_endif()
                      ->delete();
  }

  /**
   * Adds a global notification.  These are stored in database and deleted explicitly.
   * They will be shown to any user with a matching policy.
   * If uniqid is null, it will be generated randomly.
   * If a message with this uniqid already exists, it will get replaced.
   *
   * @param string  $message
   * @param string|null  $class
   * @param string|null $uniqid
   * @param boolean $can_close
   * @param string $policies
   * @return string
   * @throws \LogicException
   */
  public function addGlobal($message, $class = null, $uniqid = null, $can_close = true, $policies = '')
  {
    $notice = NotificationQuery::create()
                               ->filterByUniqid($uniqid)
                               ->filterByUserId(null, \Criteria::ISNULL)
                               ->findOne();
    if (!$notice) $notice = new Notification();

    $notice->setUniqid($uniqid ? $uniqid : uniqid('', true));
    $notice->setUser(null);
    $notice->setMessage($message);
    $notice->setOutputClass($class);
    $notice->setNoDismiss(!$can_close);
    $notice->setPolicies($policies);
    $notice->save();
    return $notice->getUniqid();
  }

  /**
   * Deletes a global notification.
   *
   * @param string $uniqid
   * @return void
   * @throws \LogicException
   */
  public function removeGlobal($uniqid)
  {
    NotificationQuery::create()
                     ->filterByUniqid($uniqid)
                     ->filterByUserId(null, \Criteria::ISNULL)
                     ->delete();
  }


  /**
   * Retrieves all, or optionally new, pending notifications.  Resets transient notifications.
   *
   * @param string|\DateTime|integer $only_from
   * @return array
   */
  public function getAll($only_from = null)
  {
    // Transient notifications
    $notices = $this->app['session']->get('anthem.notify.transient');
    if (!$notices) $notices = array();
    $this->app['session']->remove('anthem.notify.transient');

    // Global persistent notifications
    $global_notices = NotificationQuery::create()
                                       ->filterByUserId(null, \Criteria::ISNULL)
                                       ->_if($only_from)
                                         ->filterByCreatedAt($only_from, \Criteria::GREATER_THAN)
                                       ->_endif()
                                       ->firstCreatedFirst()
                                       ->find();
    foreach ($global_notices as $notice)
    {
      // Ignore notices which do not have a matching policy
      if ($notice->getPolicies() && !$this->app['auth']->hasPolicies(explode(',', $notice->getPolicies())))
        continue;

      $notices[$notice->getCreatedAt()] = array('uniqid'  => $notice->getUniqid(),
                                                'message' => $notice->getMessage(),
                                                'class'   => $notice->getOutputClass(),
                                                'close_button' => !$notice->getNoDismiss(),
                                                'ajax_dismiss' => true);
    }

    // User's persistent notifications
    if ($this->app['Auth']['enable'] && !$this->app['auth']->isGuest())
    {
      $user = $this->app['auth']->getUser();
      $persistent_notices = NotificationQuery::create()
                                             ->filterByUser($user)
                                             ->_if($only_from)
                                               ->filterByCreatedAt($only_from, \Criteria::GREATER_THAN)
                                             ->_endif()
                                             ->firstCreatedFirst()
                                             ->find();
      foreach ($persistent_notices as $notice)
        $notices[$notice->getCreatedAt()] = array('uniqid'  => $notice->getUniqid(),
                                                  'message' => $notice->getMessage(),
                                                  'class'   => $notice->getOutputClass(),
                                                  'close_button' => !$notice->getNoDismiss(),
                                                  'ajax_dismiss' => true);
    }

    ksort($notices);

    return array_reverse($notices);  // In reverse chronological order
  }

  /**
   * Returns underlying model class.
   *
   * @return string
   */
  public function getModelClass()
  {
    return 'Anthem\\Notify\\Model\\Notification';
  }
}
