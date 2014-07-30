<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Video.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Type_Video extends Wall_Plugin_Type_Abstract
{

  public function getTypes(User_Model_User $user)
  {
    return array(
      // Video
      'comment_video',
      'video_new',
      // Avp
      'avp_video_new_import',
      'avp_video_new_upload',
      'comment_avp_video',
	  );
  }


}