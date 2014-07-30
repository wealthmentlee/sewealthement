<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Photo.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Type_Photo extends Wall_Plugin_Type_Abstract
{

  public function getTypes(User_Model_User $user)
  {
    return array(
      'album_photo_new',
      'event_photo_upload',
      'group_photo_upload',
      'profile_photo_update',
      'pagealbum_photo_new',
      'advalbum_photo_new'
	  );
  }


}