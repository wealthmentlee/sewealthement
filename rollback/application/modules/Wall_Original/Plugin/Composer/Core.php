<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Composer_Core extends Core_Plugin_Abstract
{
  public function onAttachLink($data)
  {
    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      if( Engine_Api::_()->core()->hasSubject() ) {
        $subject = Engine_Api::_()->core()->getSubject();
        if( $subject->getType() != 'user' ) {
          $data['parent_type'] = $subject->getType();
          $data['parent_id'] = $subject->getIdentity();
        }
      }

      $link = Engine_Api::_()->getApi('links', 'core')->createLink($viewer, $data);
    } catch( Exception $e ) {
      throw $e;
      return;
    }
    return $link;
  }

  public function onComposerTag($data, $params)
  {
    $action = (empty($params)) ? null : $params['action'];
    if (!$action || empty($data['tags'])){
      return ;
    }

    $str = $data['tags'];

    $output = array();
    parse_str($str, $output);

    if (empty($output)){
      return ;
    }

    $viewer = Engine_Api::_()->_()->user()->getViewer();
    $tagTable = Engine_Api::_()->getDbTable('tags', 'wall');

    foreach ($output as $key => $value){

      try {
        $object = Engine_Api::_()->getItemByGuid($key);
      } catch (Exception $e){
        continue ;
      }


      if ($object->getType() == 'user' && !$object->isSelf($viewer)){
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($object, $viewer, $action, 'wall_tag', array(''));
      }

      $tag = $tagTable->createRow();
      $tag->setFromArray(array(
        'action_id' => $action->getIdentity(),
        'object_id' => $object->getIdentity(),
        'object_type' => $object->getType(),
        'user_id' => $viewer->getIdentity(),
        'value' => $value,
        'is_people' => 0
      ));


      $tag->save();


    }


  }

  public function onComposerPeople($data, $params)
  {
    $action = (empty($params)) ? null : $params['action'];
    if (!$action || empty($data['peoples'])){
      return ;
    }

    $str = $data['peoples'];

    $output = explode(',', $str);

    if (empty($output)){
      return ;
    }


    $viewer = Engine_Api::_()->_()->user()->getViewer();

    $tagTable = Engine_Api::_()->getDbTable('tags', 'wall');

    foreach ($output as $key => $value){
      try {
        $object = Engine_Api::_()->getItemByGuid($value);
      } catch (Exception $e){
        continue ;
      }

      if ($object->getType() == 'user' && !$object->isSelf($viewer)){
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($object, $viewer, $action, 'wall_tag', array(''));
      }

      $tag = $tagTable->createRow();
      $tag->setFromArray(array(
        'action_id' => $action->getIdentity(),
        'object_id' => $object->getIdentity(),
        'object_type' => $object->getType(),
        'user_id' => $viewer->getIdentity(),
        'is_people' => 1
      ));

      $tag->save();

    }



  }



}