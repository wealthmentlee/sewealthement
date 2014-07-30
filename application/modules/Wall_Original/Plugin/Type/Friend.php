<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Friend.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Type_Friend extends Wall_Plugin_Type_Abstract
{
  public function getItems(User_Model_User $user)
  {
    $items = array();
    foreach ($user->membership()->getMembershipsOfIds() as $member){
      $items[] = array(
        'type' => 'user',
        'id' => $member
      );
    }

    return $items;

  }


}