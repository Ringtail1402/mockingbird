<?php

namespace Anthem\Auth\Social;

use Anthem\Auth\Social\OAuthProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * Google OAuth authentication.
 */
class GoogleProvider extends OAuthProvider
{
  /**
   * OAuth URLs.
   *
   * @var array
   */
  static protected $endpoints = array(
    'authorize'    => 'https://accounts.google.com/o/oauth2/auth',
    'access_token' => 'https://accounts.google.com/o/oauth2/token',
    'userinfo'     => 'https://www.googleapis.com/oauth2/v1/userinfo',
  );

  /**
   * @var array User data.
   */
  protected $userdata = null;

  /**
   * Returns OAuth endpoint URL.
   *
   * @param  string $endpoint 'authorize', 'access_token', optionally provider-specific.
   * @return string
   */
  protected function getEndpointURL($endpoint)
  {
    return self::$endpoints[$endpoint];
  }

  /**
   * Finds out persistent remote user id.  This requires one more API call to Google.
   *
   * @param  array $data  Data retrieved by access_token OAuth API call.
   * @return string
   */
  protected function determineRemoteUserId($data)
  {
    return $this->loadUserData();
  }

  /**
   * Loads user data via a Google API call.  Returns Google's user internal id.
   *
   * @return string
   * @throws \RuntimeException
   */
  protected function loadUserData()
  {
    // Call userinfo API via curl
    $ch = curl_init($this->getEndpointURL('userinfo') . '?access_token=' . $this->getAccessToken());
    $this->setCurlRequestOptions($ch);
    curl_setopt($ch, CURLOPT_POST, false);
    $result = curl_exec($ch);
    curl_close($ch);
    if (!$result)
      throw new \RuntimeException('Google userinfo API call failed.');

    // Handle failure
    $result = json_decode($result, true);
    if (!empty($result['error']))
      throw new \RuntimeException($result['error']['message']);

    $this->userdata = $result;
    return $result['id'];
  }

  /**
   * Returns a user information field.
   * Supported fields:
   * - email
   * - firstname
   * - lastname
   * - avatar
   *
   * @param string $name
   * @return string
   */
  public function getProperty($name)
  {
    if (!$this->userdata) $this->loadUserData();

    switch ($name)
    {
      case 'email':
        return $this->userdata['email'];
        break;

      case 'firstname':
        return $this->userdata['given_name'];
        break;

      case 'lastname':
        return $this->userdata['family_name'];
        break;

      case 'avatar':
        return isset($this->userdata['picture']) ? $this->userdata['picture'] : null;
        break;

      default:
        return isset($this->userdata[$name]) ? $this->userdata[$name] : null;
    }
  }


  /**
   * Returns display name of this social auth provider (e.g. "Google").
   *
   * @return string
   */
  public function getTitle()
  {
    return 'Google';
  }

  /**
   * Returns a 16x16 icon of this social auth provider.
   *
   * @return string
   */
  public function getIconAsset()
  {
    return 'Anthem/Auth:social/google.png';
  }

  /**
   * Returns display name of logged-in user in arbitrary form.
   *
   * @return string
   */
  public function getUserDisplayName()
  {
    return $this->getProperty('firstname') . ' ' . $this->getProperty('lastname');
  }
}