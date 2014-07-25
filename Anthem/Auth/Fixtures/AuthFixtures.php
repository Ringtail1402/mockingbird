<?php

namespace Anthem\Auth\Fixtures;

use Silex\Application;
use Anthem\Propel\Fixtures\FixtureInterface;
use Anthem\Auth\Model\User;

class AuthFixtures implements FixtureInterface
{
  /**
   * Returns fixture priority (fixtures with higher priorities get loaded first).
   *
   * @param  none
   * @return integer
   */
  public function getPriority()
  {
    return 100;
  }

  /**
   * Actually loads the fixtures.
   *
   * @param  Application    $app
   * @param  object[string] &$references  References for other fixtures may be set here.
   * @return void
   */
  public function load(Application $app, array &$references)
  {
    // Root
    $root = new User();
    $root->setEmail('root@localhost');
    $root->setIsSuperuser(true);
    $app['auth']->changePassword($root, 'admin');
    $root->save();

    echo "Note: default user is 'root@localhost' and default password is 'admin'." . PHP_EOL;
  }
}