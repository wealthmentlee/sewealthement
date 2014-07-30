<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: FriendsController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class User_FriendsController extends Core_Controller_Action_User
{
  var $friends_enabled = false;

  public function init()
  {

    // Try to set subject
    $user_id = $this->_getParam('user_id', null);

		if( $user_id && !Engine_Api::_()->core()->hasSubject() )
    {
      $user = Engine_Api::_()->getItem('user', $user_id);
      if( $user )
      {
        Engine_Api::_()->core()->setSubject($user);
      }
    }


    //(new Request({url:'http://10.0.0.11/engine4/index.php/members/friends/cancel/id/2',data:{format:'json'}})).send();
    $this->eligible = Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible;
    if (!$this->eligible)
    {
      //  die();
    }
  }
  
  public function friendsAction()
	{
		if (Engine_Api::_()->mobile()->siteMode() !== 'mobile') return;

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Multiple friend mode
    $select = $subject->membership()->getMembersSelect();
    $this->view->friends = $friends = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Get stuff
    $ids = array();
    foreach( $friends as $friend )
    {
      $ids[] = $friend->user_id;
    }
    $this->view->friendIds = $ids;

    // Get the items
    $friendUsers = array();
    foreach( Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser )
    {
      $friendUsers[$friendUser->getIdentity()] = $friendUser;
    }
    $this->view->friendUsers = $friendUsers;

    // Get lists if viewing own profile
    if( $viewer->isSelf($subject) ) {
      // Get lists
      $listTable = Engine_Api::_()->getItemTable('user_list');
      $this->view->lists = $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));

      $listIds = array();
      foreach( $lists as $list ) {
        $listIds[] = $list->list_id;
      }

      // Build lists by user
      $listItems = array();
      $listsByUser = array();
      if( !empty($listIds) ) {
        $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
        $listItemSelect = $listItemTable->select()
          ->where('list_id IN(?)', $listIds)
          ->where('child_id IN(?)', $ids);
        $listItems = $listItemTable->fetchAll($listItemSelect);
        foreach( $listItems as $listItem ) {
          //$list = $lists->getRowMatching('list_id', $listItem->list_id);
          //$listsByUser[$listItem->child_id][] = $list;
          $listsByUser[$listItem->child_id][] = $listItem->list_id;
        }
      }
      $this->view->listItems = $listItems;
      $this->view->listsByUser = $listsByUser;
    }

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
	}

	public function listAddAction()
  {
    $list_id = (int) $this->_getParam('list_id');
    $friend_id = (int) $this->_getParam('friend_id');

    $user = Engine_Api::_()->user()->getViewer();
    $friend = Engine_Api::_()->getItem('user', $friend_id);

    // Check params
    if( !$user->getIdentity() || !$friend || !$list_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check list
    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->find($list_id)->current();
    if( !$list || $list->owner_id != $user->getIdentity() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Missing list/not authorized');
    }

    // Check if already target status
    if( $list->has($friend) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already in list');
      return;
    }

    $list->add($friend);
    
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Member added to list.');
    Engine_Api::_()->core()->setSubject($user);
  }

  public function listRemoveAction()
  {
    $list_id = (int) $this->_getParam('list_id');
    $friend_id = (int) $this->_getParam('friend_id');
    
    $user = Engine_Api::_()->user()->getViewer();
    $friend = Engine_Api::_()->getItem('user', $friend_id);

    // Check params
    if( !$user->getIdentity() || !$friend || !$list_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check list
    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->find($list_id)->current();
    if( !$list || $list->owner_id != $user->getIdentity() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Missing list/not authorized');
    }

    // Check if already target status
    if( !$list->has($friend) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already not in list');
      return;
    }

    $list->remove($friend);

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Member removed from list.');
    Engine_Api::_()->core()->setSubject($user);
  }

  public function listCreateAction()
  {
    $title = (string) $this->_getParam('title');
    $friend_id = (int) $this->_getParam('friend_id');
    $user = Engine_Api::_()->user()->getViewer();
    $friend = Engine_Api::_()->getItem('user', $friend_id);

    if( !$user->getIdentity() || !$title )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->createRow();
    $list->owner_id = $user->getIdentity();
    $list->title = $title;
    $list->save();

    if( $friend && $friend->getIdentity() )
    {
      $list->add($friend);
    }

    $this->view->status = true;
    $this->view->message = 'List created.';
    $this->view->list_id = $list->list_id;
    Engine_Api::_()->core()->setSubject($user);
  }

  public function listDeleteAction()
  {
    $list_id = (int) $this->_getParam('list_id');
    $user = Engine_Api::_()->user()->getViewer();

    // Check params
    if( !$user->getIdentity() || !$list_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check list
    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->find($list_id)->current();
    if( !$list || $list->owner_id != $user->getIdentity() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Missing list/not authorized');
    }

    $list->delete();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('List deleted');
  }
  
  public function addAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
		$return_url = $this->_getParam('return_url', '');
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }
    
    $viewer = $this->_helper->api()->user()->getViewer();
    $user = $this->_helper->api()->user()->getUser($user_id);

    // check that user is not trying to befriend 'self'
    if( $viewer->isSelf($user) ){
      $this->_forward('success', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You cannot befriend yourself.')),
				'return_url' => $return_url,
      ));
    }

    // check that user is already friends with the member
    if( $viewer->membership()->isMember($user)){
      $this->_forward('success', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are already friends with this member.')),
				'return_url' => $return_url,
      ));
    }

    // check that user has not blocked the member
    if( $viewer->isBlocked($user)){
      $this->_forward('success', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Friendship request was not sent because you blocked this member.')),
				'return_url' => $return_url,
      ));
    }
    

    // Make form
    $this->view->form = $form = new User_Form_Friends_Add();
		$cancel = $form->getElement('cancel');
		$cancel->setOptions(array('link'=>true, 'href'=>urldecode($this->_getParam('return_url')), 'onclick'=>''));
		$form->removeElement('cancel');
		$group = $form->getDisplayGroup('buttons');
		$group->addElement($cancel);

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {

      // check friendship verification settings
      // add membership if allowed to have unverified friendships
      //$user->membership()->setUserApproved($viewer);

      // else send request
      $user->membership()->addMember($viewer)->setUserApproved($viewer);


      // send out different notification depending on what kind of friendship setting admin has set
      /*('friend_accepted', 'user', 'You and {item:$subject} are now friends.', 0, ''),
        ('friend_request', 'user', '{item:$subject} has requested to be your friend.', 1, 'user.friends.request-friend'),
        ('friend_follow_request', 'user', '{item:$subject} has requested to add you as a friend.', 1, 'user.friends.request-friend'),
        ('friend_follow', 'user', '{item:$subject} has added you as a friend.', 1, 'user.friends.request-friend'),
       */
      

      // if one way friendship and verification not required
      if(!$user->membership()->isUserApprovalRequired()&&!$user->membership()->isReciprocal()){
        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends_follow', '{item:$object} is now following {item:$subject}.', array('is_mobile' => true));

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow');

        $message = Zend_Registry::get('Zend_Translate')->_("You are now following this member.");
      }

      // if two way friendship and verification not required
      else if(!$user->membership()->isUserApprovalRequired()&&$user->membership()->isReciprocal()){
        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');
        $message = Zend_Registry::get('Zend_Translate')->_("You are now friends with this member.");
      }

      // if one way friendship and verification required
      else if(!$user->membership()->isReciprocal()){
        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow_request');
        $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");
      }

      // if two way friendship and verification required
      else if($user->membership()->isReciprocal())
      {
        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_request');
        $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");
      }


      $this->view->status = true;

      $db->commit();

      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your friend request has been sent.');
      $this->_forward('success', 'utility', 'mobile', array(
          'messages' => array($message),
					'return_url' => $return_url,
      ));
    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function cancelAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }
    
    // Make form
    $this->view->form = $form = new User_Form_Friends_Cancel();
		$cancel = $form->getElement('cancel');
		$cancel->setOptions(array('link'=>true, 'href'=>urldecode($this->_getParam('return_url')), 'onclick'=>''));
		$form->removeElement('cancel');
		$group = $form->getDisplayGroup('buttons');
		$group->addElement($cancel);
		

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

		$viewer = $this->_helper->api()->user()->getViewer();
    $user = $this->_helper->api()->user()->getUser($user_id);

    $friendship = $viewer->membership()->getRow($user);
    if ($friendship->active == 1){
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already friends');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      $user->membership()->removeMember($viewer);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $user, $viewer, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      // Set the request as handled if it was a follow request
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $user, $viewer, 'friend_follow_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();

			$this->_forward('success', 'utility', 'mobile', array(
        'messages'=>Array(Zend_Registry::get('Zend_Translate')->_('Your friend request has been cancelled.')),
				'return_url' => $this->_getParam('return_url'),
      ));
    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function confirmAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Confirm();
		$cancel = $form->getElement('cancel');
		$cancel->setOptions(array('link'=>true, 'href'=>urldecode($this->_getParam('return_url')), 'onclick'=>''));
		$form->removeElement('cancel');
		$group = $form->getDisplayGroup('buttons');
		$group->addElement($cancel);
		

    if( !$this->getRequest()->isPost() && !$this->getRequest()->isGet())
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getParams()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      
      $user->membership()->setUserApproved($viewer);

      // Add activity
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));
      
      // Add notification
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $viewer, $user, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      
      // Increment friends counter
      // @todo make sure this works fine for following
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.friendships');

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('You are now friends with %s');
      $message = sprintf($message, $user->__toString());

      $this->view->message = $message;

			$this->_forward('success', 'utility', 'mobile', array(
        'messages' => array($message),
				'return_url' => $this->_getParam('return_url'),
      ));
    }
		
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }
  
  public function followAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Confirm();
		$cancel = $form->getElement('cancel');
		$cancel->setOptions(array('link'=>true, 'href'=>urldecode($this->_getParam('return_url')), 'onclick'=>''));
		$form->removeElement('cancel');
		$group = $form->getDisplayGroup('buttons');
		$group->addElement($cancel);
		
    if( !$this->getRequest()->isPost() && !$this->getRequest()->isGet()  )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getParams()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);

      $user->membership()->setFollowApproved($viewer);

      // Add activity
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends_follow', '{item:$object} is now following {item:$subject}.', array('is_mobile' => true));

      // Add notification
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow_accepted');

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $viewer, $user, 'friend_follow_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('%s is now following you');
      $message = sprintf($message, $user->__toString());

      $this->view->status = true;
      $this->view->message = $message;
			$this->_forward('success', 'utility', 'mobile', array(
        'messages' => array($message),
				'return_url' => $this->_getParam('return_url'),
      ));
    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function rejectAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Reject();
		$cancel = $form->getElement('cancel');
		$cancel->setOptions(array('link'=>true, 'href'=>urldecode($this->_getParam('return_url')), 'onclick'=>''));
		$form->removeElement('cancel');
		$group = $form->getDisplayGroup('buttons');
		$group->addElement($cancel);

    if( !$this->getRequest()->isPost() && !$this->getRequest()->isGet() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getParams()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      
      $user->membership()->removeMember($viewer);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $viewer, $user, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      
      $db->commit();
      $message = Zend_Registry::get('Zend_Translate')->_('You ignored a friend request from %s');
      $message = sprintf($message, $user->__toString());

      $this->view->status = true;
      $this->view->message = $message;

			$this->_forward('success', 'utility', 'mobile', array(
        'messages' => array($message),
				'return_url' => $this->_getParam('return_url'),
      ));
    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }
  
  public function ignoreAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject()->isValid() ) return;

    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Reject();
		$cancel = $form->getElement('cancel');
		$cancel->setOptions(array('link'=>true, 'href'=>urldecode($this->_getParam('return_url')), 'onclick'=>''));
		$form->removeElement('cancel');
		$group = $form->getDisplayGroup('buttons');
		$group->addElement($cancel);
		
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);

      $user->membership()->removeFollow($viewer);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $viewer, $user, 'friend_follow_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('You ignored %s\'s request to follow you');
      $message = sprintf($message, $user->__toString());

      $this->view->status = true;
      $this->view->message = $message;
			
			$this->_forward('success', 'utility', 'mobile', array(
        'messages' => array($message),
				'return_url' => $this->_getParam('return_url'),
      ));
    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }
  public function removeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);

    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }


    // Make form
    $this->view->form = $form = new User_Form_Friends_Remove();
		$cancel = $form->getElement('cancel');
		$cancel->setOptions(array('link'=>true, 'href'=>urldecode($this->_getParam('return_url')), 'onclick'=>''));
		$form->removeElement('cancel');
		$group = $form->getDisplayGroup('buttons');
		$group->addElement($cancel);
		
    if( !$this->getRequest()->isPost() )
      {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      
      $user->membership()->removeMember($viewer);
      $user->lists()->removeFriendFromLists($viewer);

      // Set the request as handled - this may not be neccesary here
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $user, $viewer, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      
      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('This person has been removed from your friends.');
			$this->_forward('success', 'utility', 'mobile', array(
        'messages' => array($message),
				'return_url' => $this->_getParam('return_url'),
      ));
    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function suggestAction()
  {
    $data = array();
    if( $this->_helper->requireUser()->checkRequire() )
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $table = Engine_Api::_()->getItemTable('user');
      $select = $this->_helper->api()->user()->getViewer()->membership()->getMembersObjectSelect();

      if( $this->_getParam('includeSelf', false) ) {
        $data[] = array(
          'type' => 'user',
          'id' => $viewer->getIdentity(),
          'guid' => $viewer->getGuid(),
          'label' => $viewer->getTitle() . ' (you)',
          'photo' => $this->view->itemPhoto($viewer, 'thumb.icon'),
          'url' => $viewer->getHref(),
        );
      }

      if( 0 < ($limit = (int) $this->_getParam('limit', 10)) )
      {
        $select->limit($limit);
      }

      if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))))
      {
        $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
      }
      $ids = array();
      foreach( $select->getTable()->fetchAll($select) as $friend )
      {
        $data[] = array(
          'type'  => 'user',
          'id'    => $friend->getIdentity(),
          'guid'  => $friend->getGuid(),
          'label' => $friend->getTitle(),
          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
          'url'   => $friend->getHref(),
        );
        $ids[] = $friend->getIdentity();
        $friend_data[$friend->getIdentity()] = $friend->getTitle();
      }

      // first get friend lists created by the user
      $listTable = Engine_Api::_()->getItemTable('user_list');
      $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
      $listIds = array();
      foreach( $lists as $list ) {
        $listIds[] = $list->list_id;
        $listArray[$list->list_id] = $list->title;
      }

      // check if user has friend lists
      if($listIds){
        // get list of friend list + friends in the list
        $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
        $uName = Engine_Api::_()->getDbtable('users')->info('name');
        $iName  = $listItemTable->info('name');

        $listItemSelect = $listItemTable->select()
          ->setIntegrityCheck(false)
          ->from($iName, array($iName.'.listitem_id', $iName.'.list_id', $iName.'.child_id',$uName.'.displayname'))
          ->joinLeft($uName, "$iName.child_id = $uName.user_id")
          //->group("$iName.child_id")
          ->where('list_id IN(?)', $listIds);

        $listItems = $listItemTable->fetchAll($listItemSelect);

        $listsByUser = array();
        foreach( $listItems as $listItem ) {
          $listsByUser[$listItem->list_id][$listItem->user_id]= $listItem->displayname ;
        }
        
        foreach ($listArray as $key => $value){
          if (!empty($listsByUser[$key])){
            $data[] = array(
              'type' => 'list',
              'friends' => $listsByUser[$key],
              'label' => $value,
            );
          }
        }
      }
      
    }

    if( $this->_getParam('sendNow', true) )
    {
      return $this->_helper->json($data);
    }
    else
    {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  
  public function requestFriendAction()
  {
    $path = Engine_Api::_()->mobile()->getScriptPath('user');
    $this->view->addScriptPath($path);

    $this->view->notification = $notification = $this->_getParam('notification');
  }

  public function requestFollowAction()
  {
    $path = Engine_Api::_()->mobile()->getScriptPath('user');
    $this->view->addScriptPath($path);

    $this->view->notification = $notification = $this->_getParam('notification');
  }

}
