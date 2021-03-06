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


class Wall_Plugin_Composer_Music extends Core_Plugin_Abstract
{
  public function onAttachMusic($data)
  {
    if( !is_array($data) || empty($data['song_id']) ) 
      return;

    $song = Engine_Api::_()->getItem('music_playlist_song', $data['song_id']);
    if( !($song instanceof Core_Model_Item_Abstract) || !$song->getIdentity() )
      return;
    
    return $song;
  }
}