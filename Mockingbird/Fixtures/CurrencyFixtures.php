<?php

namespace Mockingbird\Fixtures;

use Silex\Application;
use Anthem\Propel\Fixtures\FixtureInterface;
use Mockingbird\Model\Currency;

/**
 * Loads a few predefined currencies (a ruble, which is default, a dollar and a euro).
 */
class CurrencyFixtures implements FixtureInterface
{
  public function getPriority()
  {
    return 0;
  }

  public function load(Application $app, array &$references)
  {
    $rub = new Currency();
    $rub->setIsPrimary(true);
    $rub->setRateToPrimary(1);
    $rub->setTitle('RUB');
    $rub->setFormat('# р.');
    $rub->save();

    $usd = new Currency();
    $usd->setRateToPrimary(32.0);
    $usd->setTitle('USD');
    $usd->setFormat('$#');
    $usd->save();

    $eur = new Currency();
    $eur->setRateToPrimary(40.5);
    $eur->setTitle('EUR');
    $eur->setFormat('# €');
    $eur->save();
  }
}
