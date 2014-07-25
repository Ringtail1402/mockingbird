<?php

namespace Anthem\Core;

use Silex\Application;
use Anthem\Settings\SettingsInterface;
use Anthem\Forms\Input\SelectInput;
use Anthem\Forms\Validator\RequiredValidator;

/**
 * Core module settings.
 */
class CoreSettings implements SettingsInterface
{
  /**
   * Returns Core module settings.
   *
   * @return array
   */
  public function getSettings(Application $app)
  {
    return array(
      // Language for L10n
      'core.l10n.language' => array(
        'title'   => 'Core.UI_LANGUAGE',
        'default' => $app['Core']['l10n.default_language'],
        'global'  => $app['Core']['l10n.allow_user_setting'] ? null : true,
        'input'   => function () use ($app) {
          return new SelectInput($app, array(
            'values'    => $app['Core']['l10n.languages'],
            'validator' => new RequiredValidator(),
          ));
        }
      )
    );
  }
}
