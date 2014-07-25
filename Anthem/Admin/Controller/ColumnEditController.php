<?php

namespace Anthem\Admin\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Main admin controller.
 */
class ColumnEditController
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
  * Updates a column.
  *
  * @param  Request $request
  * @return string
  */
  public function updateAction(Request $request, $page = null)
  {
    $id    = $request->request->get('id');
    $field = $request->request->get('field');
    $value = $request->request->get('value');

    $class = $this->app[$page]->getModel();
    $object = call_user_func(array($class . 'Peer', 'retrieveByPk'), $id);
    if (!$object)
      throw new NotFoundHttpException('Unknown object #' . $id . ' of class \'' . $class . '\'.');
    $object->setByName($field, $value, \BasePeer::TYPE_FIELDNAME);
    $object->save();

    return 'OK';
  }


}