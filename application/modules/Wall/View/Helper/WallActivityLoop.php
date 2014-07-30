<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallActivityLoop.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_View_Helper_WallActivityLoop extends Zend_View_Helper_Abstract
{

  public function wallActivityLoop($actions = null, array $data = array())
  {
    if( null == $actions || (!is_array($actions) && !($actions instanceof Zend_Db_Table_Rowset_Abstract)) ) {
      return '';
    }

    $form = new Wall_Form_Comment();
    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = "";
    if($viewer->getIdentity()){
      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
    }

    $privacy_list = Engine_Api::_()->getDbTable('privacy', 'wall')->getPrivacyList($actions);

    $data = array_merge($data, array(
      'actions' => $actions,
      'commentForm' => $form,
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
      'activity_moderate' => $activity_moderate,
      'privacy_list' => $privacy_list
    ));

    $module = (!empty($data['module']) && $data['module'] == 'timeline')?'timeline':'wall';
    if($data['module'] == 'pinfeed')
    {
      $module =  'pinfeed';
    }
    return $this->view->partial(
      '_activityText.tpl',
      $module,
      $data
    );
  }

}