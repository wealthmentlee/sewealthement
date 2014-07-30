<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Privacy.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Wall_Model_DbTable_Privacy extends Engine_Db_Table
{

  public function addPrivacy(Activity_Model_Action $action, $privacy)
  {
    $privacy_type = $action->object_type;

    $privacy_list = array();
    $privacy_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.privacy.disabled', ''));
    foreach (Engine_Api::_()->wall()->getPrivacy($privacy_type) as $item){
      if (in_array($privacy_type.'_'.$item, $privacy_disabled)){
        continue ;
      }
      $privacy_list[] = $item;
    }
    $active = (empty($privacy_list[0])) ? null : $privacy_list[0];

    if (empty($active)){
      return ;
    }

    if (empty($privacy_list)){
      return ;
    }
    if (!in_array($privacy, $privacy_list)){
      $privacy = $active; // set default
    }

    try {
      
      return $this->createRow(array(
        'action_id' => $action->getIdentity(),
        'privacy' => $privacy)
      )->save();

    } catch (Exception $e){
      
    }
  }

  public function getPrivacyList($activity)
  {
    if (!$activity){
      return array();
    }
    $action_ids = array();
    foreach ($activity as $action){
      $action_ids[] = $action->action_id;
    }

    $select = $this->select()
        ->where('action_id IN (?)', $action_ids);

    $privacy_list = $this->fetchAll($select);

    $privacy = array();
    foreach ($privacy_list as $item){
      $privacy[$item->action_id] = $item->privacy;
    }

    return $privacy;

  }


}