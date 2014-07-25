<?php

namespace Mockingbird\Form;

use Anthem\Forms\Form\Form;
use Anthem\Forms\Input\StringInput;
use Anthem\Forms\Input\CheckboxInput;
use Anthem\Forms\Validator\RequiredValidator;
use Anthem\Forms\Validator\UniqueValidator;
use Anthem\Forms\Validator\RegexpValidator;
use Anthem\Forms\Validator\NumberValidator;
use Mockingbird\Model\Currency;
use Mockingbird\Model\CurrencyQuery;

/**
 * Currency form.
 */
class CurrencyForm extends Form
{
  /**
   * Creates a form.
   *
   * @param \Silex\Application $app
   * @param Currency           $object
   */
  public function __construct($app, $object)
  {
    if ($object->isNew()) $object->setRateToPrimary(null);

    $options = array(
      'label'  => function () use ($object) { return ($object->getTitle() ? htmlspecialchars($object->getTitle()) : _t('CURRENCY_NEW')); },
      'fields' => array(
        'title'     => new StringInput($app, array(
          'label'        => _t('TITLE'),
          'help'         => _t('CURRENCY_TITLE_HELP'),
          'validator'    => array(
            new RequiredValidator(),
            new UniqueValidator(array('query' => function($title) use ($object) {
              return CurrencyQuery::create()
                                  ->filterByTitle($title)
                                  ->_if(!$object->isNew())
                                  ->filterById($object->getId(), \Criteria::NOT_EQUAL)
                                  ->_endif();
            })),
            new RegexpValidator(array('regexp' => '/^[a-z]{3}$/i', 'message' => _t('ERROR_CURRENCY_TITLE'))),
          ),
        )),
        'is_primary' => new CheckboxInput($app, array(
          'label'       => _t('ISPRIMARY'),
          'help'        => _t('CURRENCY_ISPRIMARY_HELP'),
          'readonly'    => $object->getIsPrimary(),  // Cannot uncheck
        )),
        'rate_to_primary' => new StringInput($app, array(
          'label'       => _t('RATE_TO_PRIMARY'),
          'help'        => _t('CURRENCY_RATE_TO_PRIMARY_HELP'),
          'format'      => '%.4F',
          'validator'   => new NumberValidator(),
        )),
        'format' => new StringInput($app, array(
          'label'       => _t('FORMAT'),
          'help'        => _t('CURRENCY_FORMAT_HELP'),
          'validator'    => array(
            new RequiredValidator(),
            new RegexpValidator(array('regexp' => '/^[^#]*#[^#]*$/', 'message' => _t('ERROR_CURRENCY_FORMAT'))),
          ),
        ))
      ),
    );

    return parent::__construct($app, $object, $options);
  }

  /**
   * Saves form into object.
   *
   * @return Currency
   */
  public function save()
  {
    // CurrencyModelService::getDefaultCurrency() caches primary currency -- ensure that it is cached.
    $old_primary = $this->app['mockingbird.model.currency']->getPrimaryCurrency();

    $object = parent::save();

    // Handle primary currency change properly
    if ($object->getIsPrimary())
      $this->app['mockingbird.model.currency']->setPrimaryCurrency($object);

    // Automatically load rate if necessary
    if (!$object->getRateToPrimary())
    {
      try
      {
        $this->app['mockingbird.model.currency']->loadRate($object);
      }
      catch (\Exception $e)
      {
        $this->app['notify']->addTransient($e->getMessage(), 'error');
      }
    }
  }
}
