<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Hashtag_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
 public function onActivityActionCreateAfter($event)
  {

    $action = $event->getPayload();
    /**
  * @var $action Activity_Model_Action
  */
    $support_plugins = array(
      'blog' => 'blog_new',
      'page' =>  'page_create',
      'forum' =>  'forum_topic_create',
      'group' =>  'advgroup_create',
      'video' =>  'video_new',
      'store_product' =>  'page_product_new',
      'article_new' =>  'article_new',
      'music_playlist_new' =>  'music_playlist_new',

    );


    $object = $action->getObject();
    $body = $object->getTitle();
    $check_tit = strpos($body,'#');
        if($check_tit !== false){
          $this->createAtivityHashtag($body,$action);
        }

    $params = $action->params;
    //print_die($action->type.' - '.$params['tag']);
    $page_plugins = array(
      'pagealbum_photo_new' => 'pagealbum_photo_new',
      'pagemusic_playlist_new'=> 'pagemusic_playlist_new'
    );
    if( in_array($action->type,$page_plugins) ){
      $body = $params ['tag'];
      if($body){
        $this->createAtivityHashtag($body,$action,true);
        return;
      }
      return;
    }
    if($action->type == 'pagevent_create'){
      $body = $params ['tag'];
      $check_tit = strpos($body,'#');
      if($check_tit !== false){
        $this->createAtivityHashtag($body,$action);
      }
      return;
    }
    if($action->type == 'pagedocument_new'){
      $body = $params ['tag'];
      if($body){
        $this->createAtivityHashtag($body,$action,true);
        }
      $body = $params ['title_tag'];
      if($body){
        $check_tit = strpos($body,'#');
        if($check_tit !== false){
          $this->createAtivityHashtag($body,$action);
        }
      }
      return;
    }
    if($action->type == 'store_product_new'){
      $body = $params ['tag'];
      if($body){
        $this->createAtivityHashtag($body,$action,true);
      }
      $body = $params ['title_tag'];
      if($body){
        $check_tit = strpos($body,'#');
        if($check_tit !== false){
          $this->createAtivityHashtag($body,$action);
        }
      }
      return;
    }
    if($action->type == 'page_product_new'){
      $body = $params ['tag'];
      if($body){
        $this->createAtivityHashtag($body,$action,true);

      }
      $body = $params ['title_tag'];
      if($body){
        $check_tit = strpos($body,'#');
        if($check_tit !== false){
          $this->createAtivityHashtag($body,$action);
        }
      }
      return;
    }

   /* $ipnLogFile = APPLICATION_PATH . '/temporary/log/bolot.log';
    file_put_contents($ipnLogFile,
      print_r($body),
      FILE_APPEND);*/

    if(in_array($action->type,$support_plugins)){
      if($action->type == 'page_product_new'){
        $type = 'store_product';
        $attachTable = Engine_Api::_()->getDbTable('attachments', 'activity');
        $select = $attachTable->select()->where('action_id = ?', $action->action_id)->where('type = ?', $type);

        $attach = $attachTable->fetchRow($select);
        ///
        if($attach === null){
        return;
        }
        $id = $attach->id;

      }
      /*elseif($action->type == 'music_playlist_new'){

        return;
      }*/

      else{
        $id = $action->object_id;
        $type=$action->object_type;
      }

      $mapsTable = Engine_Api::_()->getDbTable('tagMaps', 'core');
      $select = $mapsTable->select()->where('resource_id = ?', $id)->where('resource_type = ?', $type);
      $mape = $mapsTable->fetchAll($select);




      foreach($mape as $core_map)
      {
      $mapsTable = Engine_Api::_()->getDbTable('maps', 'hashtag');
      $select = $mapsTable->select()->where('resource_id = ?', $action->action_id)->where('resource_type = ?', $type);
      $map = $mapsTable->fetchRow($select);

      if(!$map){
        $user = Engine_Api::_()->user()->getViewer();
        $map = $mapsTable->createRow();
        $map->resource_type = $action->object_type;
        $map->resource_id = $action->action_id;
        $map->hashtagger_type =  $user->getType();
        $map->hashtagger_id =  $user->getIdentity();
        $map->save();
      }
      $coreTagsTable = Engine_Api::_()->getDbTable('tags', 'core');
      $coreTag = $coreTagsTable->fetchRow($coreTagsTable->select()->where('tag_id = ?', $core_map['tag_id']));
      $hashTagTable = Engine_Api::_()->getDbTable('tags', 'hashtag');
      $tag = $hashTagTable->createRow();
      $tag->hashtag =  $coreTag->text;
      $tag->map_id =  $map->getIdentity();
      $tag->save();
      }

    }

  }
  public function onactivity_actionCreateAfter($event)
  {
    /**
     * @var $action Activity_Model_Action
     */
    $action = $event->getPayload();
    $body = $action->body;
    $check=strpos($action->body,'#');
    if($action->type == 'music_playlist_new'){
      $object = $action->getObject();
      $body = $object->getTitle();
        $check = strpos($body,'#');


    }

    $hoo='';
    if($check !== false){
      $enter = array("\n", "\r\n", "\n\r","&nbsp;");
      $body = str_replace($enter, " ", $body);


      $text = explode(' ',$body);
      $probel =  array( " ","&nbsp;");
      $text = str_replace($probel, "", $text);
      $user = Engine_Api::_()->user()->getViewer();
      $mapsTable = Engine_Api::_()->getDbTable('maps', 'hashtag');
      $map = $mapsTable->createRow();
      $map->resource_type = $action->getType();
      $map->resource_id = $action->getIdentity();
      $map->hashtagger_type =  $user->getType();
      $map->hashtagger_id =  $user->getIdentity();
      $map->save();
      $text= array_unique($text);
      foreach ($text as $str){
        $pos=strpos($str, '#');
        if( $pos!== false && $pos==0){
          $vowels = array(".", ",", ";", "!", "?", ":", "*", "#", "'");
          $item = str_replace($vowels, "", $str);
          $hashTagTable = Engine_Api::_()->getDbTable('tags', 'hashtag');
          $tag = $hashTagTable->createRow();
          $tag->hashtag =  $item;
          $tag->map_id =  $map->getIdentity();
          $tag->save();
        }
      }
    }





  }
  public  function createAtivityHashtag($body, $action, $page = false){
    if($page){
      $text = explode(',',$body);
      if(count($text)<=0){
        return;
      }
      $user = Engine_Api::_()->user()->getViewer();
      $mapsTable = Engine_Api::_()->getDbTable('maps', 'hashtag');
      $map = $mapsTable->createRow();
      $map->resource_type = $action->getType();
      $map->resource_id = $action->getIdentity();
      $map->hashtagger_type =  $user->getType();
      $map->hashtagger_id =  $user->getIdentity();
      $map->save();
      $text= array_unique($text);
      foreach ($text as $str){
          $vowels = array(".", ",", ";", "!", "?", ":", "*", "#", "'", " ");
          $item = str_replace($vowels, "", $str);
          $hashTagTable = Engine_Api::_()->getDbTable('tags', 'hashtag');
          $tag = $hashTagTable->createRow();
          $tag->hashtag =  $item;
          $tag->map_id =  $map->getIdentity();
          $tag->save();
      }
    }else{
    $text = explode(' ',$body);
    $user = Engine_Api::_()->user()->getViewer();
    $mapsTable = Engine_Api::_()->getDbTable('maps', 'hashtag');
    $map = $mapsTable->createRow();
    $map->resource_type = $action->getType();
    $map->resource_id = $action->getIdentity();
    $map->hashtagger_type =  $user->getType();
    $map->hashtagger_id =  $user->getIdentity();
    $map->save();
    $text= array_unique($text);
    foreach ($text as $str){
      $pos=strpos($str, '#');
      if( $pos!== false && $pos==0){
        $vowels = array(".", ",", ";", "!", "?", ":", "*", "#", "'"," ");
        $item = str_replace($vowels, "", $str);
        $hashTagTable = Engine_Api::_()->getDbTable('tags', 'hashtag');
        $tag = $hashTagTable->createRow();
        $tag->hashtag =  $item;
        $tag->map_id =  $map->getIdentity();
        $tag->save();
      }
    }
    }
  }
  public function onItemDeleteBefore($event)
  {

    $item = $event->getPayload();

   $activity =  Engine_Api::_()->getDbtable('actions', 'activity');
    $select = $activity-> select()->where('object_type = ?', $item->getType())->where('object_id = ?', $item->getIdentity());
    $act = $activity->fetchAll($select);
    foreach($act as $code){
      $new = Engine_Api::_()->getDbTable('maps', 'hashtag');
      $select_b = $new->select('resource_id = ?', $code['action_id']);
      $act_s = $new->fetchAll($select_b);
      foreach($act_s as $tag){
        $tags_hash = Engine_Api::_()->getDbTable('tags', 'hashtag');
        $tags_hash ->delete(array('map_id = ?' => $tag['map_id']));

      }
      $new->delete(array('resource_id = ?' => $code['action_id']));
    }

  }

  public function onActivityAttachmentCreateAfter($event)
  {
    print_die($event);
  }

}
