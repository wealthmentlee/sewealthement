<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Event.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Type_Event extends Wall_Plugin_Type_Abstract
{

  public function getTypes(User_Model_User $user)
  {
    return array(
      'event_create',
      'event_join',
      'event_photo_upload',
      'event_topic_create',
      'event_topic_reply',
	  );
  }


}