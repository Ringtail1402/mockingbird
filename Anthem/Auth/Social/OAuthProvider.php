<?php

namespace Anthem\Auth\Social;

use Anthem\Auth\Social\BaseSocialAuthProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Base class for OAuth 2.0 social auth providers.
 */
abstract class OAuthProvider extends BaseSocialAuthProvider
{
  /**
   * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
   */
  protected $url_generator;

  /**
   * The constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Session                  $session
   * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $url_generator
   * @param string                                                     $provider_id
   * @param array                                                      $options
   */
  public function __construct(Session $session, UrlGeneratorInterface $url_generator, $provider_id, array $options = array())
  {
    $this->session       = $session;
    $this->url_generator = $url_generator;
    parent::__construct($provider_id, $options);
  }

  /**
   * Prompts user for authorization.  Should typically redirect to social network's auth URL.
   *
   * @param  \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function prompt(Request $request)
  {
    return new RedirectResponse($this->getEndpointURL('authorize') . '?' . http_build_query(array(
      'client_id'     => $this->options['client_id'],
      'scope'         => $this->options['scope'],
      'response_type' => 'code',
      'redirect_uri'  => $this->getRedirectURI(),
    )));
  }

  /**
   * Checks authorization data provided by social network.  Returns persistent remote user id,
   * which is also stored in session.
   *
   * @param  \Symfony\Component\HttpFoundation\Request $request
   * @return string
   * @throws \RuntimeException
   */
  public function auth(Request $request)
  {
    // Handle failure
    if ($request->get('error'))
      throw new \RuntimeException($request->get('error_description') ? $request->get('error_description') : $request->get('error'));

    $code = $request->get('code');
    if (!$code) throw new \RuntimeException('Missing code parameter for auth callback.');

    // Request access token via curl
    $ch = curl_init($this->getEndpointURL('access_token'));
    $this->setCurlRequestOptions($ch, array(
      'code'          => $code,
      'client_id'     => $this->options['client_id'],
      'client_secret' => $this->options['client_secret'],
      'redirect_uri'  => $this->getRedirectURI(),
      'grant_type'    => 'authorization_code',
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    if (!$result)
      throw new \RuntimeException('access_token call failed.');

    // Handle failure
    $result = json_decode($result, true);
    if (!empty($result['error']))
      throw new \RuntimeException(!empty($result['error_description']) ? $result['error_description'] : $result['error']);

    $this->setAccessToken($result['access_token'], $result['expires_in']);

    $remote_user_id = $this->determineRemoteUserId($result);

    $this->setRemoteUserId($remote_user_id);

    return $remote_user_id;
  }

  /**
   * Finds out persistent remote user id.  Must be implemented by subclasses,
   * as methods of determining persistent id are provider-dependent.
   *
   * @param  array $data  Data retrieved by access_token OAuth API call.
   * @return string
   */
  abstract protected function determineRemoteUserId($data);

  /**
   * Returns OAuth endpoint URL.
   *
   * @param  string $endpoint 'authorize', 'access_token', optionally provider-specific.
   * @return string
   */
  abstract protected function getEndpointURL($endpoint);

  /**
   * Sets curl options for POST to access_token endpoint.
   *
   * @param resource $ch
   * @param array    $postdata
   * @return void
   */
  protected function setCurlRequestOptions($ch, $postdata = array())
  {
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
  }

  /**
   * Generates redirect_uri parameter for this provider.
   *
   * @return string
   */
  protected function getRedirectURI()
  {
    return $this->url_generator->generate('auth.social.auth', array('provider' => $this->provider_id), true);
  }

  /**
   * Stores remote user id for this provider in session.
   *
   * @param  string  $remote_user_id
   * @return void
   */
  protected function setRemoteUserId($remote_user_id)
  {
    $this->session->set($this->provider_id . '.remote_user_id', $remote_user_id);
  }

  /**
   * Stores access token for this provider in session.
   *
   * @param  string  $access_token
   * @param  integer $expires_in
   * @return void
   */
  protected function setAccessToken($access_token, $expires_in)
  {
    $this->session->set($this->provider_id . '.access_token',      $access_token);
    $this->session->set($this->provider_id . '.access_token_time', time());
    $this->session->set($this->provider_id . '.expires_in',        $expires_in);
  }

  /**
   * Retrieves access token for this provider from session.  Checks that the token is not expired.
   *
   * @return string
   */
  public function getAccessToken()
  {
    $access_token = $this->session->get($this->provider_id . '.access_token');

    // No access token
    if (!$access_token) return null;

    // Check expiration
    if (!$this->checkAccessTokenExpiration()) return null;

    // Success
    return $access_token;
  }

  /**
   * Retrieves remote user id for this provider from session.  Checks that the access token is not expired.
   *
   * @return string
   */
  public function getRemoteUserId()
  {
    $remote_user_id = $this->session->get($this->provider_id . '.remote_user_id');

    // No access token
    if (!$remote_user_id) return null;

    // Check expiration
    if (!$this->checkAccessTokenExpiration()) return null;

    // Success
    return $remote_user_id;
  }

  /**
   * Checks that access token has not expired.  Returns true if okay, false if expired.
   *
   * @return boolean
   */
  protected function checkAccessTokenExpiration()
  {
    $access_token_time = $this->session->get($this->provider_id . '.access_token_time');
    $expires_in        = $this->session->get($this->provider_id . '.expires_in');

    if ($access_token_time + $expires_in < time())
    {
      $this->session->remove($this->provider_id . '.remote_user_id');
      $this->session->remove($this->provider_id . '.access_token');
      $this->session->remove($this->provider_id . '.access_token_time');
      $this->session->remove($this->provider_id . '.expires_in');
      return false;
    }

    return true;
  }
}