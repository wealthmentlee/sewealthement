<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: MemberController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Group_MemberController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( 0 !== ($group_id = (int) $this->_getParam('group_id')) &&
        null !== ($group = Engine_Api::_()->getItem('group', $group_id)) )
    {
      Engine_Api::_()->core()->setSubject($group);
    }

    $this->_helper->requireUser();
    $this->_helper->requireSubject('group');
    /*
    $this->_helper->requireAuth()->setAuthParams(
      null,
      null,
      null
      //'edit'
    );
     *
     */
  }

  public function joinAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject()->isValid() ) return;

    // Make form
    $this->view->form = $form = new Group_Form_Member_Join();

    $form->addElement('Hidden', 'return_url', array(
      'value' => $this->_getParam('return_url')
    ));

    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    // Process form
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $subject = $this->_helper->api()->core()->getSubject();
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        $subject->membership()->addMember($viewer)->setUserApproved($viewer);

        // Set the request as handled
        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
          $viewer, $subject, 'group_invite');
        if( $notification )
        {
          $notification->mitigated = true;
          $notification->save();
        }

        // Add activity
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($viewer, $subject, 'group_join', null, array('is_mobile' => true));

        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are now a member of this group.')),
        'return_url'=>urldecode($this->_getParam('return_url')),
      ));
    }
  }

  public function requestAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject()->isValid() ) return;

    // Make form
    $this->view->form = $form = new Group_Form_Member_Request();

    $form->addElement('Hidden', 'return_url', array(
      'value' => $this->_getParam('return_url')
    ));


    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    // Process form
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $subject = $this->_helper->api()->core()->getSubject();
      $owner = $subject->getOwner();
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        $subject->membership()->addMember($viewer)->setUserApproved($viewer);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, 'group_approve');
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Group membership request sent')),
        'return_url'=>urldecode($this->_getParam('return_url')),
      ));
    }
  }

  public function cancelAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject()->isValid() ) return;

    // Make form
    $this->view->form = $form = new Group_Form_Member_Cancel();

    $form->addElement('Hidden', 'return_url', array(
      'value' => $this->_getParam('return_url')
    ));


    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    // Process form
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $subject = $this->_helper->api()->core()->getSubject();
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        $subject->membership()->removeMember($viewer);
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Group membership request cancelled.')),
        'return_url'=>urldecode($this->_getParam('return_url')),
      ));
    }
  }

  public function leaveAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject()->isValid() ) return;
    
    $viewer = $this->_helper->api()->user()->getViewer();
    $subject = $this->_helper->api()->core()->getSubject();

    if( $subject->isOwner($viewer) ) return;

    // Make form
    $this->view->form = $form = new Group_Form_Member_Leave();

    $form->addElement('Hidden', 'return_url', array(
      'value' => $this->_getParam('return_url')
    ));


    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    // Process form
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {

      $list = $subject->getOfficerList();
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        // remove from officer list
        $list->remove($viewer);
        
        $subject->membership()->removeMember($viewer);
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully left this group.')),
        'return_url'=>urldecode($this->_getParam('return_url')),
      ));
    }
  }
  
  public function acceptAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject('group')->isValid() ) return;

    // Make form
    $this->view->form = $form = new Group_Form_Member_Accept();

    $form->addElement('Hidden', 'return_url', array(
      'value' => $this->_getParam('return_url')
    ));


    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    // Process form
    if( !$this->getRequest()->isPost() && !$this->getRequest()->isGet())
    {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    if( !$form->isValid($this->getRequest()->getParams()) )
    {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }

    // Process 
    $viewer = $this->_helper->api()->user()->getViewer();
    $subject = $this->_helper->api()->core()->getSubject();
    $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $subject->membership()->setUserApproved($viewer);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
        $viewer, $subject, 'group_invite');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->save();
      }

      // Add activity
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $subject, 'group_join', null, array('is_mobile' => true));

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->error = false;
    
    $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the group %s');
    $message = sprintf($message, $subject->__toString());
    $this->view->message = $message;

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array($message),
      'return_url'=>urldecode($this->_getParam('return_url')),
    ));
  }

  public function rejectAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject('group')->isValid() ) return;

    // Get user
    if( 0 === ($user_id = (int) $this->_getParam('user_id')) ||
        null === ($user = Engine_Api::_()->getItem('user', $user_id)) )
    {
      $user = $this->_helper->api()->user()->getViewer();
      //return $this->_helper->requireSubject->forward();
    }

    // Make form
    $this->view->form = $form = new Group_Form_Member_Reject();

    $form->addElement('Hidden', 'return_url', array(
      'value' => $this->_getParam('return_url')
    ));


    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    // Process form
    if( !$this->getRequest()->isPost() && !$this->getRequest()->isGet())
    {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    if( !$form->isValid($this->getRequest()->getParams()) )
    {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }

    // Process
    $viewer = $this->_helper->api()->user()->getViewer();
    $subject = $this->_helper->api()->core()->getSubject();
    $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $subject->membership()->removeMember($user);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
        $user, $subject, 'group_invite');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->save();
      }

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->error = false;
    $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the group %s');
    $message = sprintf($message, $subject->__toString());
    $this->view->message = $message;

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array($message),
      'return_url'=>urldecode($this->_getParam('return_url')),
    ));
  }






  
  public function promoteAction()
  {
    // Get user
    if( 0 === ($user_id = (int) $this->_getParam('user_id')) ||
        null === ($user = Engine_Api::_()->getItem('user', $user_id)) )
    {
      return $this->_helper->requireSubject->forward();
    }

    $group = Engine_Api::_()->core()->getSubject();
    $list = $group->getOfficerList();
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$group->membership()->isMember($user) ) {
      throw new Group_Model_Exception('Cannot add a non-member as an officer');
    }

    $this->view->form = $form = new Group_Form_Member_Promote();

    $form->addElement('Hidden', 'return_url', array(
      'value' => $this->_getParam('return_url')
    ));


    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    $table = $list->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $list->add($user);

      // Add notification
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      $notifyApi->addNotification($user, $viewer, $group, 'group_promote');

      // Add activity
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($user, $group, 'group_promote', null, array('is_mobile' => true));

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Member Promoted')),
      'return_url'=>urldecode($this->_getParam('return_url')),
    ));
  }

  public function demoteAction()
  {
    // Get user
    if( 0 === ($user_id = (int) $this->_getParam('user_id')) ||
        null === ($user = Engine_Api::_()->getItem('user', $user_id)) )
    {
      return $this->_helper->requireSubject->forward();
    }

    $group = Engine_Api::_()->core()->getSubject();
    $list = $group->getOfficerList();

    if( !$group->membership()->isMember($user) ) {
      throw new Group_Model_Exception('Cannot remove a non-member as an officer');
    }

    $this->view->form = $form = new Group_Form_Member_Demote();

    $form->addElement('Hidden', 'return_url', array(
      'value' => $this->_getParam('return_url')
    ));


    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    $table = $list->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $list->remove($user);

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Member Demoted')),
      'return_url'=>urldecode($this->_getParam('return_url')),
    ));
  }

  public function removeAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject()->isValid() ) return;
    
    // Get user
    if( 0 === ($user_id = (int) $this->_getParam('user_id')) ||
        null === ($user = Engine_Api::_()->getItem('user', $user_id)) )
    {
      return $this->_helper->requireSubject->forward();
    }

    $group = Engine_Api::_()->core()->getSubject();
    $list = $group->getOfficerList();

    if( !$group->membership()->isMember($user) ) {
      throw new Group_Model_Exception('Cannot remove a non-member');
    }

    // Make form
    $this->view->form = $form = new Group_Form_Member_Remove();
    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    // Process form
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $db = $group->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        // Remove as officer first (if necessary)
        $list->remove($user);

        // Remove membership
        $group->membership()->removeMember($user);

        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Group member removed.')),
        'return_url'=>urldecode($this->_getParam('return_url')),
      ));
    }
  }

  public function approveAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject('group')->isValid() ) return;

    // Get user
    if( 0 === ($user_id = (int) $this->_getParam('user_id')) ||
        null === ($user = Engine_Api::_()->getItem('user', $user_id)) )
    {
      return $this->_helper->requireSubject->forward();
    }
    
    // Make form
    $this->view->form = $form = new Group_Form_Member_Approve();

    $form->addElement('Hidden', 'return_url', array(
      'value' => $this->_getParam('return_url')
    ));


    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    // Process form
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $subject = $this->_helper->api()->core()->getSubject();
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        $subject->membership()->setResourceApproved($user);

        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, 'group_accepted');

        // Add activity
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($user, $subject, 'group_join', null, array('is_mobile' => true));

        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Group request approved')),
        'return_url'=>urldecode($this->_getParam('return_url')),
      ));
    }
  }

  public function editAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject('group')->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) return;

    // Get user
    if( 0 === ($user_id = (int) $this->_getParam('user_id')) ||
        null === ($user = Engine_Api::_()->getItem('user', $user_id)) )
    {
      return $this->_helper->requireSubject->forward();
    }

    $group = Engine_Api::_()->core()->getSubject('group');
    $memberInfo = $group->membership()->getMemberInfo($user);

    // Make form
    $this->view->form = $form = new Group_Form_Member_Edit();

    $form->addElement('Hidden', 'return_url', array(
      'value' => $this->_getParam('return_url')
    ));


    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    if( !$this->getRequest()->isPost() )
    {
      $form->populate(array(
        'title' => $memberInfo->title
      ));
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }
    
    $db = $group->membership()->getReceiver()->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $memberInfo->setFromArray($form->getValues());
      $memberInfo->save();
      
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Member title changed')),
      'return_url'=>urldecode($this->_getParam('return_url')),
    ));
  }
}