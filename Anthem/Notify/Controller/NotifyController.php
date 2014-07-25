<?php

namespace Anthem\Notify\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notify poll/delete controller.
 */
class NotifyController
{
  /**
   * @var \Silex\Application
   */
  protected $app;

  /**
   * The constructor.
   *
   * @param \Silex\Application $app
   */
  public function __construct(Application $app)
  {
    $this->app = $app;
  }

  /**
   * Polls notices.
   *
   * @param  Request $request
   * @return string
   */
  public function updateAction(Request $request)
  {
    $notices = $this->app['notify']->getAll();
    $ids = $request->get('existing_ids');
    if (!$ids)
      $ids = array();
    else
      $ids = array_combine($ids, $ids);
    $result = array('new' => array(), 'updated' => array(), 'deleted' => $ids);
    foreach ($notices as $notice)
    {
      $uniqid = $notice['uniqid'];
      $html = $this->app['core.view']->render('Anthem/Notify:notification.php', array('hidden' => !isset($ids[$uniqid]), 'notice' => $notice));
      if (!$uniqid)
        $result['new'][] = $html;
      else
      {
        unset($result['deleted'][$uniqid]);
        if (isset($ids[$uniqid]))
          $result['updated'][$uniqid] = $html;
        else
          $result['new'][$uniqid] = $html;
      }
    }
    return new Response(json_encode($result), 200, array('Content-Type' => 'application/json'));
  }

  /**
   * Dismisses a notice.
   *
   * @param  Request $request
   * @param  string $uniqid
   */
  public function dismissAction(Request $request, $uniqid)
  {
    // Require authorization
    if (!empty($app['Auth']['enable'])) $this->app['auth']->checkAuthorization();

    $this->app['notify']->removePersistent($uniqid);
    return '';
  }
}