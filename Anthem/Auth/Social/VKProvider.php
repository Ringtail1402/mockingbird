<?php

namespace Anthem\Auth\Social;

use Anthem\Auth\Social\OAuthProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * VK.com OAuth authentication.
 */
class VKProvider extends OAuthProvider
{
  /**
   * OAuth URLs.  Must include 'authorize' and 'access_token' entries.
   *
   * @var array
   */
  static protected $endpoints = array(
    'authorize'    => 'https://oauth.vk.com/authorize',
    'access_token' => 'https://oauth.vk.com/access_token',
    'api'          => 'https://api.vk.com/method/',
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
   * Finds out persistent remote user id.
   *
   * @param  array $data  Data retrieved by access_token OAuth API call.
   * @return string
   */
  protected function determineRemoteUserId($data)
  {
    return $data['user_id'];  // Trivial for VK.com
  }

  /**
   * Calls VK.com API.
   *
   * @param string  $method
   * @param array   $parameters
   * @return array
   * @throws \RuntimeException
   */
  public function callAPI($method, $parameters = array())
  {
    // Add access token to params
    $access_token = $this->getAccessToken();
    if (!$access_token) throw new \RuntimeException('VK.com API access token invalid or expired.');
    $parameters['access_token'] = $access_token;

    // Call method via curl
    $ch = curl_init($this->getEndpointURL('api') . $method);
    $this->setCurlRequestOptions($ch, $parameters);
    $result = curl_exec($ch);
    curl_close($ch);
    if (!$result)
      throw new \RuntimeException('VK.com API call to method ' . $method . ' failed.');

    // Handle failure
    $result = json_decode($result, true);
    if (!empty($result['error']))
      throw new \RuntimeException($result['error']['error_msg']);

    return isset($result['response']) ? $result['response'] : true;
  }

  /**
   * Returns a user information field.
   * Supported fields:
   * - email (returns fake email "idXXXXXX@vkontakte")
   * - firstname
   * - lastname
   * - avatar
   *
   * @param string $name
   * @return string
   */
  public function getProperty($name)
  {
    $user_id = $this->getRemoteUserId();

    // Cache user information
    if (!$this->userdata)
    {
      $result = $this->callAPI('users.get', array(
        'uids'   => $user_id,
        'fields' => $this->options['user_fields']
      ));
      $this->userdata = $result[0];
    }

    switch ($name)
    {
      case 'email':
        return 'id' . $user_id . '@vkontakte';
        break;

      case 'firstname':
        return $this->userdata['first_name'];
        break;

      case 'lastname':
        return $this->userdata['last_name'];
        break;

      case 'avatar':
        return $this->userdata['photo_max'];
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
    return _t('Auth.SOCIAL_VK');
  }

  /**
   * Returns a 16x16 icon of this social auth provider.
   *
   * @return string
   */
  public function getIconAsset()
  {
    return 'Anthem/Auth:social/vk.png';
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