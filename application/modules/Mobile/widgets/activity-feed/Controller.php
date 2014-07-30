<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Widget_ActivityFeedController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
	{
    
    // TODO Replace Helper Body

    $path = Engine_Api::_()->getModuleBootstrap('mobile')->getModulePath();

    $loader = new Zend_Loader_PluginLoader(array(
      'Mobile_Model_Helper_' => $path . '/Model/Helper'
    ));

    $api = Engine_Api::_()->activity();

    $helper = $api->getHelper('body');
    if ($helper){
      $new_helper = $loader->load('Body');
      $api->_helpers['Body'] = new $new_helper;
    }


		// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = null;
		if( Engine_Api::_()->core()->hasSubject() ) {
			// Get subject
			$subject = Engine_Api::_()->core()->getSubject();
			if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
				return $this->setNoRender();
			}
		}

		$request = Zend_Controller_Front::getInstance()->getRequest();
		$mobileCore = Engine_Api::_()->mobile();

		// Get some options
		$this->view->feedOnly         = $feedOnly = $request->getParam('feedOnly', false);
		$this->view->length           = $length = $request->getParam('limit', 10);
		$this->view->itemActionLimit  = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);

		$this->view->updateSettings   = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
		$this->view->viewAllLikes     = $request->getParam('viewAllLikes',    $request->getParam('show_likes',    false));
		$this->view->viewAllComments  = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
		$this->view->getUpdate        = $request->getParam('getUpdate');
		$this->view->checkUpdate      = $request->getParam('checkUpdate');
		$this->view->action_id        = (int) $request->getParam('action_id');
		$this->view->post_failed      = (int) $request->getParam('pf');


		if( $feedOnly ) {
			$this->getElement()->removeDecorator('Title');
			$this->getElement()->removeDecorator('Container');
		}
		if( $length > 30 ) {
			$this->view->length = $length = 30;
		}

		// Get config options for activity
		$config = array(
			'action_id' => (int) $request->getParam('action_id'),
			'max_id'    => (int) $request->getParam('maxid'),
			'min_id'    => (int) $request->getParam('minid'),
			'limit'     => (int) $length,
		);
		
		// Pre-process feed items
		$selectCount = 0;
		$nextid = null;
		$firstid = null;
		$previd = 0;
		$tmpConfig = $config;
		$activity = array();
		$endOfFeed = false;

		$friendRequests = array();
		$itemActionCounts = array();

		$activitySelected = false;
		$previousSelected = true;

		if ($tmpConfig['max_id'] >0 ){
			$previd= 0;
			$previousSelected = false;
		}

		$min_id = 0;
		if($tmpConfig['min_id'] >0 && $tmpConfig['max_id'] == 0){
			$tmpConfig['order'] = 'ASC';
			$min_id = $tmpConfig['min_id'];
			$previd= 0;
			$previousSelected = false;
		}
		
		do {

			if ($activitySelected && !$previousSelected){
				$tmpConfig = array();
				$tmpConfig['limit'] = 1;
				if ($min_id > 0){
					$tmpConfig['min_id'] =(int)($firstid + 1);
				} else {
					$tmpConfig['min_id'] = $config['max_id'];
				}
			}

			// Get current batch
			$actions = null;
			if( !empty($subject) ) {
				$actions = $mobileCore->getActivityAbout($subject, $viewer, $tmpConfig);
			} else {
				$actions = $mobileCore->getActivity($viewer, $tmpConfig);
			}

			// Are we at the end?
			if(!$activitySelected && (count($actions) < $length || count($actions) <= 0 ) ) {
				$endOfFeed = true;
			}

			if ($endOfFeed && $min_id > 0){
				$endOfFeed = false;
				$tmpConfig['max_id'] = 0;
				$tmpConfig['min_id'] = 0;
				$tmpConfig['order'] = null;

				// Get current batch
				$actions = null;
				if( !empty($subject) ) {
					$actions = $mobileCore->getActivityAbout($subject, $viewer, $tmpConfig);
				} else {
					$actions = $mobileCore->getActivity($viewer, $tmpConfig);
				}
			}

			$selectCount++;

			// Pre-process
			if( count($actions) > 0 ) {
				foreach( $actions as $action ) {

					if (!$activitySelected){
						// get next id
						if( null === $nextid || $action->action_id <= $nextid ) {
							$nextid = $action->action_id - 1;
						}
						// get first id
						if( null === $firstid || $action->action_id > $firstid ) {
							$firstid = $action->action_id;
						}

					}

					// skip disabled actions
					if( !$action->getTypeInfo()->enabled ) continue;
					// skip items with missing items
					if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
					if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;

					// remove duplicate friend requests
					if( $action->type == 'friends' ) {
						$id = $action->subject_id . '_' . $action->object_id;
						$rev_id = $action->object_id . '_' . $action->subject_id;
						if( in_array($id, $friendRequests) || in_array($rev_id, $friendRequests) ) {
							continue;
						} else {
							$friendRequests[] = $id;
							$friendRequests[] = $rev_id;
						}
					}

					if (!$activitySelected){
						// remove items with disabled module attachments
						try {
							$attachments = $action->getAttachments();
						} catch (Exception $e) {
							// if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
							continue;
						}
					}

					// add to list
					if( count($activity) < $length && !$activitySelected) {
						$activity[$action->action_id] = $action;
						if( count($activity) == $length ) {
							$actions = array();
						}
					}

					if ($activitySelected && !$previousSelected && (method_exists($action, 'getIdentity') && $action->getIdentity()))
					{
						$previd = $action->getIdentity();
						$previousSelected = true;
					}
				}
			}

			if( $activitySelected && !$previousSelected && $selectCount >= 3) {
				$previousSelected = true;
			}
			
			// Set next tmp max_id
			if( $nextid && !$activitySelected) {
				$tmpConfig['max_id'] = $nextid;
			}

			if( !empty($tmpConfig['action_id']) && !$activitySelected) {
				$actions = array();
			}

			if ((count($activity) >= $length || $selectCount >= 3 || $endOfFeed) && !$activitySelected)
			{
				$activitySelected = true;
				$selectCount = 0;
			}

		} while( !$activitySelected || !$previousSelected );

		$this->view->activity = $activity;
		$this->view->activityCount = count($activity);
		$this->view->firstid = $firstid;
		$this->view->nextid = $nextid;
		$this->view->previd = $previd;
		$this->view->endOfFeed = $endOfFeed;
		// Get some other info
		if( !empty($subject) ) {
			$this->view->subjectGuid = $subject->getGuid(false);
		}

		$this->view->enableComposer = false;
		if( $viewer->getIdentity() && !$this->_getParam('action_id') ) {
			if( !$subject || $subject->authorization()->isAllowed($viewer, 'comment') ) {
				$this->view->enableComposer = true;
			}
		}

	}
}
