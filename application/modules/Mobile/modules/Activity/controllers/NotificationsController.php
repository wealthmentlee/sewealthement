<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: NotificationsController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Activity_NotificationsController extends Core_Controller_Action_Standard
{

  public function init()
  {
    $this->_helper->requireUser();
  }

  public function indexAction()
  {

		// Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
      ->getNavigation('activity_main');

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->notifications = $notifications = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationsPaginator($viewer);
		$notifications->setItemCountPerPage(10);
    $notifications->setCurrentPageNumber($this->_getParam('page', 1));

    // Force rendering now
    $this->_helper->viewRenderer->postDispatch();
    $this->_helper->viewRenderer->setNoRender(true);

    $this->view->hasunread = false;

    // Now mark them all as read
    $ids = array();
    foreach( $notifications as $notification ) {
			try{
				$this->markreadAction($notification->notification_id);
			} catch (Exception $e ){

			}

      $ids[] = $notification->notification_id;
    }
    //Engine_Api::_()->getDbtable('notifications', 'activity')->markNotificationsAsRead($viewer, $ids);
  }

	public function requestsAction()
  {
		$viewer = Engine_Api::_()->user()->getViewer();
    $this->view->requests = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestsPaginator($viewer);
		// Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
      ->getNavigation('activity_main');

		// Force rendering now
    $this->_helper->viewRenderer->postDispatch();
    $this->_helper->viewRenderer->setNoRender(true);
	}

  protected function markreadAction($action_id = 0)
  {
    $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $db = $notificationsTable->getAdapter();
    $db->beginTransaction();

    try {
      $notification = Engine_Api::_()->getItem('activity_notification', $action_id);
      $notification->read = 1;
      $notification->save();
      // Commit
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }
}