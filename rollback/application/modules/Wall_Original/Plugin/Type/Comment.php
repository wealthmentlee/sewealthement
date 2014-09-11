<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Comment.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Type_Comment extends Wall_Plugin_Type_Abstract
{

  public function getTypes(User_Model_User $user)
  {
    return array(
      'comment_album',
      'comment_album_photo',
      'comment_blog',
      'comment_classified',
      'comment_pagealbumphoto',
      'comment_pageblog',
      'comment_pageevent',
      'comment_pageplaylist',
      'comment_pagevideo',
      'comment_playlist',
      'comment_poll',
      'comment_video'
	  );
  }


}