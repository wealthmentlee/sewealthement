<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: MobileActivityLoop.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_View_Helper_MobileActivityLoop extends Activity_View_Helper_Activity
{
  public function mobileActivityLoop($actions = null, $data = array())
  {
    if( null == $actions || (!is_array($actions) && !($actions instanceof Zend_Db_Table_Rowset_Abstract)) ) {
      return '';
    }


    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = "";
    if($viewer->getIdentity()){
      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
    }

    $data = array_merge($data, array(
      'actions' => $actions,
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
      'activity_moderate' =>$activity_moderate,
      'full_text' => false
    ));

    return $this->view->partial(
      '_mobileActivityText.tpl',
      'mobile',
      $data
    );
  }
}