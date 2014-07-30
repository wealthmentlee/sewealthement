<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Album.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Composer_Album extends Core_Plugin_Abstract
{
  public function onAttachPhoto($data)
  {
    if( !is_array($data) || empty($data['photo_id']) ) {
      return;
    }

    $photo = Engine_Api::_()->getItem('album_photo', $data['photo_id']);

    // make the image public

    // CREATE AUTH STUFF HERE
    /*
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
    foreach( $roles as $i=>$role )
    {
      $auth->setAllowed($photo, $role, 'view', ($i <= $roles));
      $auth->setAllowed($photo, $role, 'comment', ($i <= $roles));
    }*/

    if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() )
    {
      return;
    }

    return $photo;
  }
}