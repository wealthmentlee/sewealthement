<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: CommentController.php 7534 2010-10-04 00:24:25Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */

class Like_CommentController extends Core_Controller_Action_Standard
{
	public function init()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $type = $this->_getParam('type');
    $identity = $this->_getParam('id');
		if ($this->_getParam('action') !== 'hint'){
			if( $type && $identity ) {
				$item = Engine_Api::_()->getItem($type, $identity);
				if( $item instanceof Core_Model_Item_Abstract && method_exists($item, 'comments') )
				{
					if( !Engine_Api::_()->core()->hasSubject() )
					{
						Engine_Api::_()->core()->setSubject($item);
					}
					//$this->_helper->requireAuth()->setAuthParams($item, $viewer, 'comment');
				}
			}

			//$this->_helper->requireUser();
			$this->_helper->requireSubject();
			//$this->_helper->requireAuth();

			if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid() ) return;
		}
  }
	
  public function listAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();

    // Perms
    $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');
    $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');

    // Likes
    $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
    $this->view->likes = $likes = $subject->likes()->getLikePaginator();

    // Comments

    // If has a page, display oldest to newest
    if( null !== ( $page = $this->_getParam('page')) )
    {
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id ASC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber($page);
      $comments->setItemCountPerPage(10);
      $this->view->comments = $comments;
      $this->view->page = $page;
    }

    // If not has a page, show the
    else
    {
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id DESC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(4);
      $this->view->comments = $comments;
      $this->view->page = $page;
    }

    if( $viewer->getIdentity() )
    {
      $this->view->form = $form = new Core_Form_Comment_Create();
      $form->populate(array(
        'identity' => $subject->getIdentity(),
        'type' => $subject->getType(),
      ));
    }

		if ($this->_getParam('format') == 'json'){
			$this->view->body = $this->view->render('comment/list.tpl');
		}
  }
  
  public function createAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    $this->view->form = $form = new Core_Form_Comment_Create();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");;
      return;
    }

    if( !$form->isValid($this->_getAllParams()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
      return;
    }

    // Process
    $db = $subject->comments()->getCommentTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $subject->comments()->addComment($viewer, $form->getValue('body'));

      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      $subjectOwner = $subject->getOwner('user');

      $type = $subject->getType();

      if (substr($type, 0, 4) == 'page' || $type == 'playlist') {

        $action_type = ($type == 'playlist')
            ? 'pageplaylist'
            : $subject->getType();

        // Activity
        $action = $activityApi->addActivity($viewer, $subject->getPage(), 'comment_' . $action_type, '', array(
          'owner' => $subjectOwner->getGuid(),
          'body' => $form->getValue('body')
        ));

        if ($action) {
          $activityApi->attachActivity($action, $subject, Activity_Model_Action::ATTACH_DESCRIPTION);
        }

      } else {
      // Activity
      $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
        'owner' => $subjectOwner->getGuid(),
        'body' => $form->getValue('body')
      ));

      }


      //$activityApi->attachActivity($action, $subject);
      
      // Notifications

      // Add notification for owner (if user and not viewer)
      $this->view->subject = $subject->getGuid();
      $this->view->owner = $subjectOwner->getGuid();
      if( $subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity() )
      {
        $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
          'label' => $subject->getShortType()
        ));
      }

      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      $commentedUserNotifications = array();
      foreach( $subject->comments()->getAllCommentsUsers() as $notifyUser )
      {
        if( $notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity() ) continue;

        // Don't send a notification if the user both commented and liked this
        $commentedUserNotifications[] = $notifyUser->getIdentity();

        $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
          'label' => $subject->getShortType()
        ));
      }
      
      // Add a notification for all users that liked
      // @todo we should probably limit this
      foreach( $subject->likes()->getAllLikesUsers() as $notifyUser )
      {
        // Skip viewer and owner
        if( $notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity() ) continue;
        
        // Don't send a notification if the user both commented and liked this
        if( in_array($notifyUser->getIdentity(), $commentedUserNotifications) ) continue;
        
        $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
          'label' => $subject->getShortType()
        ));
      }

      // Increment comment count
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = 'Comment added';
    $this->view->body = $this->view->action('list', 'comment', 'like', array(
      'type' => $this->_getParam('type'),
      'id' => $this->_getParam('id'),
      'format' => 'html',
      'page' => 1,
    ));
    $this->_helper->contextSwitch->initContext();
  }

  public function likeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    
    // Process
    $db = $subject->likes()->getAdapter();
    $db->beginTransaction();

    try
    {
      $subject->likes()->addLike($viewer);
      
      // Add notification
      $subjectOwner = $subject->getOwner();
      if( $subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity() )
      {
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'liked', array(
          'label' => $subject->getShortType()
        ));
      }
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like added');
    $this->view->body = $this->view->action('list', 'comment', 'like', array(
      'type' => $this->_getParam('type'),
      'id' => $this->_getParam('id'),
      'format' => 'html',
      'page' => 1,
    ));
    $this->_helper->contextSwitch->initContext();
  }

  public function unlikeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    // Process
    $db = $subject->likes()->getAdapter();
    $db->beginTransaction();

    try
    {
      $subject->likes()->removeLike($viewer);

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like removed');
    $this->view->body = $this->view->action('list', 'comment', 'like', array(
      'type' => $this->_getParam('type'),
      'id' => $this->_getParam('id'),
      'format' => 'html',
      'page' => 1,
    ));

    $this->_helper->contextSwitch->initContext();
  }

	public function hintAction()
	{
		$id = $this->_getParam('id');
		if ($id){
			$username = array_pop(explode('_', $id));
		}else{
			$username = trim($this->_getParam('username'));
		}

		$UTable = Engine_Api::_()->getItemTable('user');
		$name = $UTable->info('name');

		$select = $UTable->select()->where('username = ?', $username);
		$this->view->user = $subject = $UTable->fetchRow($select);

		// Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->isSelf = $viewer->isSelf($subject);
		
    $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
    $friendsName = $friendsTable->info('name');

    $select = new Zend_Db_Select($friendsTable->getAdapter());
    $select
      ->from($friendsName, 'user_id')
      ->join($friendsName, "`{$friendsName}`.`user_id`=`{$friendsName}_2`.user_id", null)
      ->where("`{$friendsName}`.resource_id = ?", $viewer->getIdentity())
      ->where("`{$friendsName}_2`.resource_id = ?", $subject->getIdentity())
      ->where("`{$friendsName}`.active = ?", 1)
      ->where("`{$friendsName}_2`.active = ?", 1);

    $uids = array();
    foreach( $select->query()->fetchAll() as $data ) {
      $uids[] = $data['user_id'];
    }

    if( count($uids) > 0 ) {
			$select = $UTable->select()
				->where('user_id IN(?)', $uids);
			$this->view->paginator = $paginator = Zend_Paginator::factory($select);
    }else{
			$this->view->paginator = $paginator = Zend_Paginator::factory(array());
		}

		$this->view->isFriended = $viewer->membership()->isMember($subject, 1);
		$this->view->paginator->setItemCountPerPage(5);
		$this->view->html = $this->view->render('comment/hint.tpl');
	}
}