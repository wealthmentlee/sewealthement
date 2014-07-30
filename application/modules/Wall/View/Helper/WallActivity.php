<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallActivity.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_View_Helper_WallActivity extends Zend_View_Helper_Abstract
{
  public function wallActivity(Activity_Model_Action $action = null, array $data = array())
  {
    if( null === $action ) {
      return '';
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $activity_moderate = "";
    
    if ($viewer->getIdentity()){
      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')
          ->getAllowed('user', $viewer->level_id, 'activity');
    }

    if (isset($data['checkin']) && $data['checkin']) {
      $checkinTable = Engine_Api::_()->getDbTable('checks', 'checkin');
      $action = $checkinTable->getActionById($action->action_id);
      $matchedCheckinsCount = array();
      if (isset($action->place_id) && $action->place_id) {
        $matchedCheckinsCount[$action->check_id] = $checkinTable->getPlaceVisitorCount($action->place_id);
      }
      $data = array_merge($data, array('matchedCheckinsCount' => $matchedCheckinsCount));
    }

    $privacy_list = Engine_Api::_()->getDbTable('privacy', 'wall')->getPrivacyList(array($action));

    $form = new Wall_Form_Comment();
    $data = array_merge($data, array(
      'actions' => array($action),
      'itemAction' => true,
      'commentForm' => $form,
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
      'activity_moderate' => $activity_moderate,
      'privacy_list' => $privacy_list
    ));

    if (isset($data['checkin']) && $data['checkin']) {
      return $this->view->partial(
        '_checkinWall.tpl',
        'checkin',
        $data
      );
    }

    $module = (!empty($data['module']) && $data['module'] == 'timeline')?'timeline':'wall';


    if($data['module'] == 'pinfeed'){
      $module = 'pinfeed';
    }

    return $this->view->partial(
      '_activityText.tpl',
      $module,
      $data
    );
  }

}
