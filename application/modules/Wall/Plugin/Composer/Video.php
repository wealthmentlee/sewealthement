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


class Wall_Plugin_Composer_Video extends Core_Plugin_Abstract
{
  public function onAttachVideo($data)
  {
    if( !is_array($data) || empty($data['video_id']) ) {
      return;
    }

    $video = Engine_Api::_()->getItem('video', $data['video_id']);
    // update $video with new title and description
    $video->title = $data['title'];
    $video->description = $data['description'];

    // Set parents of the video
    if(Engine_Api::_()->core()->hasSubject()){
      $subject      = Engine_Api::_()->core()->getSubject();
      $subject_type = $subject->getType();
      $subject_id   = $subject->getIdentity();

      $video->parent_type = $subject_type;
      $video->parent_id = $subject_id;
    }
    $video->search = 1;
    $video->save();
    
    if( !($video instanceof Core_Model_Item_Abstract) || !$video->getIdentity() )
    {
      return;
    }

    return $video;
  }
}