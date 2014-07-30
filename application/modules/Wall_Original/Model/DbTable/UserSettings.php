<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: UserSettings.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_DbTable_UserSettings extends Engine_Db_Table
{
  protected $_rowClass = 'Wall_Model_UserSetting';

  public function getUserSetting(User_Model_User $user)
  {
    if (!$user->getIdentity()){
      return ;
    }
    $select = $this->select()
        ->where('user_id = ?', $user->getIdentity());

    $setting = $this->fetchRow($select);

    if (!$setting){
      $setting = $this->createRow();
      $setting->user_id = $user->getIdentity();
    }

    return $setting;

  }

  public function saveLastPrivacy($action, $privacy, $viewer)
  {
    if (!$action || !$privacy){
      return ;
    }
    $privacy_type = $action->object_type;
    $privacy_list = Engine_Api::_()->wall()->getPrivacy($privacy_type);

    if (empty($privacy_list)){
      return ;
    }
    if (!in_array($privacy, $privacy_list)){
      return ;
    }
    
    $setting = $this->getUserSetting($viewer);
    if (!$setting){
      return ;
    }
    $setting_key = 'privacy_' . $action->object_type;
    $setting->setFromArray(array($setting_key => $privacy));
    $setting->save();
    
  }

  public function getLastPrivacy($subject, $viewer)
  {
    if (!$viewer->getIdentity()){
      return ;
    }
    $privacy_type = ($subject) ? $subject->getType() : 'user';
    $setting = $this->getUserSetting($viewer);

    if (!$setting){
      return ;
    }
    $setting_key = 'privacy_' . $privacy_type;

    if (!isset($setting->$setting_key)){
      return ;
    }
    $privacy_list = Engine_Api::_()->wall()->getPrivacy($privacy_type);
    if (empty($privacy_list)){
      return ;
    }
    if (!in_array($setting->$setting_key, $privacy_list)){
      return ;
    }
    return $setting->$setting_key;

  }



}