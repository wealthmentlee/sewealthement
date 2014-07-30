<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: MobileActivity.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_View_Helper_MobileActivity extends Zend_View_Helper_Abstract
{
  public function mobileActivity(Activity_Model_Action $action = null, array $data = array(), $task = null)
  {
    if( null === $action )
    {
      return '';
    }
		if ($task == 'comment'){
			$form = new Activity_Form_Comment();
			$form->removeAttrib('style');
			$data = array_merge($data, array('commentForm' => $form));
		}

    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');

    $data = array_merge($data, array(
      'actions' => array($action),
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
      'activity_moderate' =>$activity_moderate,
      'full_text' => true
    ));

		return $this->view->partial(
      '_mobileActivityText.tpl',
      'activity',
      $data
    );
  }
}