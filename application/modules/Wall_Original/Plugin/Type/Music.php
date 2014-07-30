<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Music.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Type_Music extends Wall_Plugin_Type_Abstract
{

  public function getTypes(User_Model_User $user)
  {
    return array(
      // Music
      'music_playlist_new',
      'comment_playlist',
      // Mp3music
      'mp3music_playlist_new',
      'mp3comment_playlist',
      'mp3music_album_new',
      'mp3comment_album',
      // Ynmusic
      'ynmusic_playlist_new',
      'yncomment_playlist',
      'ynmusic_album_new',
      'yncomment_album'
	  );
  }


}