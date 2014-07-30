<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Forum.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Type_Forum extends Wall_Plugin_Type_Abstract
{

  public function getTypes(User_Model_User $user)
  {
    return array(
      'forum_promote',
      'forum_topic_create',
      'forum_topic_reply'
	  );
  }


}