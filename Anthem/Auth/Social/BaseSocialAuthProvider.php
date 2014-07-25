<?php

namespace Anthem\Auth\Social;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base class for authentication through social networks.
 */
abstract class BaseSocialAuthProvider
{
  /**
   * @var string
   */
  protected $provider_id;

  /**
   * @var array
   */
  protected $options;

  public function __construct($provider_id, array $options = array())
  {
    $this->provider_id = $provider_id;
    $this->options     = $options;
  }

  /**
   * Prompts user for authorization.  Should typically redirect to social network's auth URL.
   *
   * @param  \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  abstract public function prompt(Request $request);

  /**
   * Checks authorization data provided by social network.  Must return a unique user id
   * in that social network, or throw an exception if login fails for any reason.
   *
   * @param  \Symfony\Component\HttpFoundation\Request $request
   * @return string
   * @throws \RuntimeException
   */
  abstract public function auth(Request $request);

  /**
   * Returns a user information field.  Some of the common fields are "email", "firstname",
   * "lastname" and "avatar".  Should return null if social network doesn't support this field.
   * "email" must always be supported.  If the provider does not actually provide user email,
   * a fake unique email should be generated.
   *
   * @param string $name
   * @return mixed
   */
  public function getProperty($name)
  {
    return null;
  }

  /**
   * Returns provider id (which must match service name).
   *
   * @return string
   */
  public function getProviderId()
  {
    return $this->provider_id;
  }

  /**
   * Returns a 16x16 icon of this social auth provider.
   *
   * @return string
   */
  abstract public function getIconAsset();

  /**
   * Returns display name of this social auth provider (e.g. "Google").
   *
   * @return string
   */
  abstract public function getTitle();

  /**
   * Returns display name of user in arbitrary form.
   *
   * @return string
   */
  abstract public function getUserDisplayName();
}