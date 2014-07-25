<?php

namespace AnthemCM\UserProfile\EventHandler;

use Anthem\Auth\EventHandler\BaseUserAttachSocialHandler;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserSocialAccount;
use Anthem\Auth\Social\BaseSocialAuthProvider;
use AnthemCM\UserProfile\Model\UserProfile;

/**
 * AuthEvents::USER_ATTACH_SOCIAL handler.  Fill in empty profile fields from social accounts.
 */
class FillProfileFromSocialAccountHandler extends BaseUserAttachSocialHandler
{
  /**
   * The actual worker function.
   *
   * @param  \Anthem\Auth\Model\User                    $user
   * @param  \Anthem\Auth\Model\UserSocialAccount       $social_account
   * @param  \Anthem\Auth\Social\BaseSocialAuthProvider $provider
   * @return void
   */
  protected function handle(User $user, UserSocialAccount $social_account, BaseSocialAuthProvider $provider)
  {
    $profile = $user->getUserProfile();
    if (!$profile)
    {
      $profile = new UserProfile();
      $profile->setUser($user);
    }

    $firstname = $provider->getProperty('firstname');
    if ($firstname && !$profile->getFirstname()) $profile->setFirstname($firstname);
    $lastname  = $provider->getProperty('lastname');
    if ($lastname  && !$profile->getLastname())  $profile->setLastname($lastname);
    $nickname  = $provider->getProperty('nickname');
    if ($nickname  && !$profile->getNickname())  $profile->setNickname($nickname);
    $avatar    = $provider->getProperty('avatar');
    if ($avatar    && !$profile->getAvatar())    $profile->setAvatar($avatar);

    $profile->save();
  }
}