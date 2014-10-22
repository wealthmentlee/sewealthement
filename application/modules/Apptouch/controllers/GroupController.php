<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 07.06.12
 * Time: 18:06
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_GroupController
  extends Apptouch_Controller_Action_Bridge
{

//  Index Controller {
  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (!$this->_helper->requireAuth()->setAuthParams('group', null, 'view')->isValid())
      return;

    $id = $this->_getParam('group_id', $this->_getParam('id', null));
    if ($id) {
      $group = Engine_Api::_()->getItem('group', $id);
      if ($group) {
        Engine_Api::_()->core()->setSubject($group);
      }
    }
  }

  public function indexBrowseAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check create
    $canCreate = Engine_Api::_()->authorization()->isAllowed('group', null, 'create');

    // Form
    $formFilter = new Apptouch_Form_Search();
    $defaultValues = $formFilter->getValues();

    //    // Populate options
    //    $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
    //    $formFilter->category_id->addMultiOptions($categories);

    // Populate form data
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $values = array();
    }

    if( isset($values['search']) ) {
      $values['search_text'] = $values['search'];
    } else {
      $values['search_text'] = '';
    }

    if ($viewer->getIdentity() && @$values['view'] == 1) {
      $values['users'] = array();
      foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
        $values['users'][] = $memberinfo->user_id;
      }
    }

    $values['search'] = 1;

    // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if ($user_id) {
      $values['user_id'] = $user_id;
    }


    // Make paginator
    $select = Engine_Api::_()->getItemTable('group')
          ->getGroupSelect($values);
    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);

    $paginator->setCurrentPageNumber($this->_getParam('page'));

    $items_count = 20; // todo
    $paginator->setItemCountPerPage($items_count);

    $this->setFormat('browse')
      ->add($this->component()->itemSearch($formFilter));
    if ($paginator->getTotalItemCount()) {
      $this
        ->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($this->_getParam('search', false)) {
      if($canCreate){
        $this->add($this->component()->tip(
          $this->view->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->view->url(array('action'=>'create'), 'event_general').'">', '</a>'),
          $this->view->translate('APPTOUCH_Nobody has created a group with that criteria.')
        ));
      }
      else
        $this->add($this->component()->tip(
          $this->view->translate('APPTOUCH_Nobody has created a group with that criteria.')
        ));

    } else {
      $title = null;
      $message = null;
      if($canCreate){
        $message = $this->view->translate('Why don\'t you %1$screate one%2$s?',
                '<a href="'.$this->view->url(array('action' => 'create'), 'group_general').'">', '</a>');
        $title = $this->view->translate('There are no groups yet.');
      }
      else
        $message = $this->view->translate('There are no groups yet.');
      $this->add($this->component()->tip(
        $message,
        $title
      ));
    }
    $this->renderContent();



  }

  public function indexManageAction()
  {
    $values = $this->_getAllParams();

    $viewer = Engine_Api::_()->user()->getViewer();

    $formFilter = new Apptouch_Form_Search();

    $membership = Engine_Api::_()->getDbtable('membership', 'group');
    $select = $membership->getMembershipsOfSelect($viewer);
    $select->where('group_id IS NOT NULL');

    $table = Engine_Api::_()->getItemTable('group');
    $tName = $table->info('name');
    if (isset($values['view']) && $values['view'] == 2) { // todo isset($values['view']) is temp solution
      $select->where("`{$tName}`.`user_id` = ?", $viewer->getIdentity());
    }
    if (!empty($values['text'])) {
      $select->where(
        $table->getAdapter()->quoteInto("`{$tName}`.`title` LIKE ?", '%' . $values['text'] . '%') . ' OR ' .
          $table->getAdapter()->quoteInto("`{$tName}`.`description` LIKE ?", '%' . $values['text'] . '%')
      );
    }

    if ($this->_getParam('search', false)) {
      $select->where('`' . $tName . '`.title LIKE ? OR `' . $tName . '`.description LIKE ?', '%' . $this->_getParam('search') . '%');
    }
    $paginator = Zend_Paginator::factory($select);
    $items_count = 20; // todo
    $paginator->setItemCountPerPage($items_count);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    // Check create
    $canCreate = Engine_Api::_()->authorization()->isAllowed('group', null, 'create');
    $this->setFormat('manage')
      ->setPageTitle($this->view->translate('My Groups'))
      ->add($this->component()->itemSearch($formFilter));

    if ($paginator->getTotalItemCount()) {
      $this
        ->add($this->component()->itemList($paginator, "manageItemData", array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($this->_getParam('search', false)) {
      $this->add($this->component()->tip(
        $this->view->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->view->url(array('action'=>'create'), 'event_general').'">', '</a>'),
        $this->view->translate('APPTOUCH_Nobody has created an event with that criteria.')
      ));
    } else {
      $title = null;
      $message = null;
      if($canCreate){
        $message = $this->view->translate('Why don\'t you %1$screate one%2$s?',
                    '<a href="'.$this->view->url(array('action' => 'create'), 'event_general').'">', '</a>');
        $title = $this->view->translate('You have not joined any events yet.');
      }
      else
        $message = $this->view->translate('You have not joined any events yet.');
      $this->add($this->component()->tip(
        $message,
        $title
      ));
    }
    $this->renderContent();
  }

  public function indexCreateAction()
  {
    if (!$this->_helper->requireUser->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams('group', null, 'create')->isValid())
      return;

    // Create form
    $form = new Group_Form_Create();
    $form->removeElement('photo');
    $this->setFormat('create');
    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $k => $v) {
      $categoryOptions[$k] = $v;
    }
    $form->category_id->setMultiOptions($categoryOptions);

    if (count($form->category_id->getMultiOptions()) <= 1) {
      $form->removeElement('category_id');
    }

    // Check method/data validitiy
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $values = $form->getValues();
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();
    $db = Engine_Api::_()->getDbtable('groups', 'group')->getAdapter();
    $db->beginTransaction();

    try {
      // Create group
      $table = Engine_Api::_()->getDbtable('groups', 'group');
      $group = $table->createRow();
      $group->setFromArray($values);
      $group->save();

      // Add owner as member
      $group->membership()->addMember($viewer)
        ->setUserApproved($viewer)
        ->setResourceApproved($viewer);

      // Set photo
      if (!empty($values['photo'])) {
        $group->setPhoto($form->photo);
      } else if($picupFile = $this->getPicupFiles('photo')){
        $group->setPhoto($picupFile[0]);
      }

      // Process privacy
      $auth = Engine_Api::_()->authorization()->context;

      $roles = array('officer', 'member', 'registered', 'everyone');

      if (empty($values['auth_view'])) {
        $values['auth_view'] = 'everyone';
      }

      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $photoMax = array_search($values['auth_photo'], $roles);
      $eventMax = array_search($values['auth_event'], $roles);
      $inviteMax = array_search($values['auth_invite'], $roles);

      $officerList = $group->getOfficerList();

      foreach ($roles as $i => $role) {
        if ($role === 'officer') {
          $role = $officerList;
        }
        $auth->setAllowed($group, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($group, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($group, $role, 'photo', ($i <= $photoMax));
        $auth->setAllowed($group, $role, 'event', ($i <= $eventMax));
        $auth->setAllowed($group, $role, 'invite', ($i <= $inviteMax));
      }

      // Create some auth stuff for all officers
      $auth->setAllowed($group, $officerList, 'photo.edit', 1);
      $auth->setAllowed($group, $officerList, 'topic.edit', 1);

      // Add auth for invited users
      $auth->setAllowed($group, 'member_requested', 'view', 1);

      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $group, 'group_create', null, array('is_mobile' => true));
      if ($action) {
        $activityApi->attachActivity($action, $group);
      }

      // Commit
      $db->commit();

      // Redirect
      return $this->redirect($this->view->url(array('id' => $group->getIdentity()), 'group_profile', true));
    } catch (Engine_Image_Exception $e) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->add($this->component()->form($form))
      ->renderContent();
  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function browseItemData(Core_Model_Item_Abstract $item)
  {
    $owner = $item->getOwner();
    $customize_fields = array(
      'descriptions' => array(
        $this->view->translate('led by') . ' ' . $owner->getTitle(),
        $this->view->translate(array('%s member', '%s members', $item->membership()->getMemberCount()), $this->view->locale()->toNumber($item->membership()->getMemberCount()))
      ),
      'photo' => $item->getPhotoUrl('thumb.normal'),
      'creation_date' => null,
    );
    return $customize_fields;
  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function manageItemData(Core_Model_Item_Abstract $item)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $owner = $item->getOwner();

    $options = array();
    if ($viewer && $item->isOwner($viewer)) {
      $options[] = $this->getOption($item, 0);
      $options[] = $this->getOption($item, 1);
    }

    if ($viewer && !$item->membership()->isMember($viewer, null)) {
      $options[] = $this->getOption($item, 2);
    }
    if ($viewer && $item->membership()->isMember($viewer) && !$item->isOwner($viewer)) {
      $options[] = $this->getOption($item, 3);
    }
    $customize_fields = array(
      'descriptions' => array(
        $this->view->translate('led by') . ' ' . $owner->getTitle(),
        $this->view->translate(array('%s member', '%s members', $item->membership()->getMemberCount()), $this->view->locale()->toNumber($item->membership()->getMemberCount()))
      ),
      'creation_date' => null,
      'owner_id' => null,
      'owner' => null,
      'manage' => $options
    );
    return $customize_fields;
  }

//  Index Controller {


//  Member Controller {
  public function memberInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (0 !== ($group_id = (int)$this->_getParam('group_id')) &&
      null !== ($group = Engine_Api::_()->getItem('group', $group_id))
    ) {
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

  public function memberJoinAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject()->isValid()) return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    // Make form
    $form = new Group_Form_Member_Join();
    $this->add($this->component()->form($form))
      ->renderContent();

    // If member is already part of the group
    if ($subject->membership()->isMember($viewer)) {
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        // Set the request as handled
        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
          $viewer, $subject, 'group_invite');
        if ($notification) {
          $notification->mitigated = true;
          $notification->save();
        }
        $db->commit();
      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
      return $this->redirect($subject->getHref(), Zend_Registry::get('Zend_Translate')->_('You are already a member of this group.'));
    }

    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        $subject->membership()->addMember($viewer)->setUserApproved($viewer);

        // Set the request as handled
        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
          $viewer, $subject, 'group_invite');
        if ($notification) {
          $notification->mitigated = true;
          $notification->save();
        }

        // Add activity
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($viewer, $subject, 'group_join', null, array('is_mobile' => true));

        $db->commit();
      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
      return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('You are now a member of this group.'));
    }
  }

  public function memberRequestAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject()->isValid()) return;

    // Make form
    $form = new Group_Form_Member_Request();

    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $subject = Engine_Api::_()->core()->getSubject();
      $owner = $subject->getOwner();
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        $subject->membership()->addMember($viewer)->setUserApproved($viewer);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, 'group_approve');
        $db->commit();
      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
      return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Group membership request sent'));
    } else {
      $this->add($this->component()->form($form))
        ->renderContent();
    }
  }

  public function memberCancelAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject()->isValid()) return;

    // Make form
    $form = new Group_Form_Member_Cancel();

    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $subject = Engine_Api::_()->core()->getSubject();
      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();

      try
      {
        $subject->membership()->removeMember($viewer);

        // Remove the notification?
        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
          $subject->getOwner(), $subject, 'group_approve');
        if ($notification) {
          $notification->delete();
        }

        $db->commit();
      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
      return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Group membership request cancelled.'));
    } else
      $this->add($this->component()->form($form))
        ->renderContent();

  }

  public function memberLeaveAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject()->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if ($subject->isOwner($viewer)) return;

    // Make form
    $form = new Group_Form_Member_Leave();

    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

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
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
      return $this->redirect($subject->getHref(), Zend_Registry::get('Zend_Translate')->_('You have successfully left this group.'));
    } else
      $this->add($this->component()->form($form))
        ->renderContent();
  }

  public function memberAcceptAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('group')->isValid()) return;

    // Make form
    $form = new Group_Form_Member_Accept();

    // Process form
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $subject->membership()->setUserApproved($viewer);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
        $viewer, $subject, 'group_invite');
      if ($notification) {
        $notification->mitigated = true;
        $notification->save();
      }

      // Add activity
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $subject, 'group_join', null, array('is_mobile' => true));

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->error = false;

    $message = sprintf(Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the group %s'), $subject->__toString());
    return $this->redirect('refresh', $message, true);
  }

  public function memberRejectAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('group')->isValid()) return;

    // Get user
    if (0 === ($user_id = (int)$this->_getParam('user_id')) ||
      null === ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      $user = Engine_Api::_()->user()->getViewer();
      //return $this->_helper->requireSubject->forward();
    }

    // Make form
    $form = new Group_Form_Member_Reject();

    // Process form
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $subject->membership()->removeMember($user);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
        $user, $subject, 'group_invite');
      if ($notification) {
        $notification->mitigated = true;
        $notification->save();
      }

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->error = false;
    $message = sprintf(Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the group %s'), $subject->__toString());

    return $this->redirect('refresh', $message, true);
  }

  public function memberPromoteAction()
  {
    // Get user
    if (0 === ($user_id = (int)$this->_getParam('user_id')) ||
      null === ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      return $this->_helper->requireSubject->forward();
    }

    $group = Engine_Api::_()->core()->getSubject();
    $list = $group->getOfficerList();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$group->membership()->isMember($user)) {
      throw new Group_Model_Exception('Cannot add a non-member as an officer');
    }

    $form = new Group_Form_Member_Promote();

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
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

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Member Promoted'));
  }

  public function memberDemoteAction()
  {
    // Get user
    if (0 === ($user_id = (int)$this->_getParam('user_id')) ||
      null === ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      return $this->_helper->requireSubject->forward();
    }

    $group = Engine_Api::_()->core()->getSubject();
    $list = $group->getOfficerList();

    if (!$group->membership()->isMember($user)) {
      throw new Group_Model_Exception('Cannot remove a non-member as an officer');
    }

    $form = new Group_Form_Member_Demote();

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
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

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Member Demoted'));
  }

  public function memberRemoveAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject()->isValid()) return;

    // Get user
    if (0 === ($user_id = (int)$this->_getParam('user_id')) ||
      null === ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      return $this->_helper->requireSubject->forward();
    }

    $group = Engine_Api::_()->core()->getSubject();
    $list = $group->getOfficerList();

    if (!$group->membership()->isMember($user)) {
      throw new Group_Model_Exception('Cannot remove a non-member');
    }

    // Make form
    $form = new Group_Form_Member_Remove();

    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
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
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Group member removed.'));
    } else
      $this->add($this->component()->form($form))
        ->renderContent();

  }

  public function memberInviteAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('group')->isValid()) return;
    // @todo auth

    // Prepare data
    $viewer = Engine_Api::_()->user()->getViewer();
    $group = Engine_Api::_()->core()->getSubject();
    $friends = $viewer->membership()->getMembers();

    // Prepare form
    $form = new Group_Form_Invite();

    $count = 0;
    foreach ($friends as $friend)
    {
      if ($group->membership()->isMember($friend, null)) continue;
      $form->users->addMultiOption($friend->getIdentity(), $friend->getTitle());
      $count++;
    }

    // throw notice if count = 0
    if ($count == 0)
      return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('You have no friends you can invite.'));
    // Not posting
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }


    // Process
    $table = $group->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $usersIds = $form->getValue('users');

      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      foreach ($friends as $friend)
      {
        if (!in_array($friend->getIdentity(), $usersIds)) {
          continue;
        }

        $group->membership()->addMember($friend)
          ->setResourceApproved($friend);

        $notifyApi->addNotification($friend, $viewer, $group, 'group_invite');
      }


      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Members invited'));
  }

  public function memberApproveAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('group')->isValid()) return;

    // Get user
    if (0 === ($user_id = (int)$this->_getParam('user_id')) ||
      null === ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      return $this->_helper->requireSubject->forward();
    }

    // Make form
    $form = new Group_Form_Member_Approve();

    // Process form
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $subject = Engine_Api::_()->core()->getSubject();
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
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Group request approved'));

    } else
      $this->add($this->component()->form($form))
        ->renderContent();

  }

  public function memberEditAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('group')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid()) return;

    // Get user
    if (0 === ($user_id = (int)$this->_getParam('user_id')) ||
      null === ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      return $this->_helper->requireSubject->forward();
    }

    $group = Engine_Api::_()->core()->getSubject('group');
    $memberInfo = $group->membership()->getMemberInfo($user);

    // Make form
    $form = new Group_Form_Member_Edit();

    if (!$this->getRequest()->isPost()) {
      $form->populate(array(
        'title' => $memberInfo->title
      ));
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
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
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Member title changed'));
  }

//  } Member Controller


//  Profile Controller {
  public function profileInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      $id = $this->_getParam('id');
      if (null !== $id) {
        $subject = Engine_Api::_()->getItem('group', $id);
        if ($subject && $subject->getIdentity()) {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('group');
    $this->_helper->requireAuth()->setNoForward()->setAuthParams(
      $subject,
      Engine_Api::_()->user()->getViewer(),
      'view'
    );
  }

  public function profileIndexAction()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    // Increment view count
    if (!$subject->getOwner()->isSelf($viewer)) {
      $subject->view_count++;
      $subject->save();
    }
    $this->setFormat('profile');
//      ->add($this->component()->html($this->widgetProfileInfo($subject)), 3)
//      ->add($this->component()->html($this->widgetProfileRsvp($subject)), 4)
      if(Engine_Api::_()->getApi('core', 'apptouch')->isTabletMode()) {
          $this->addPageInfo('fields', $this->widgetProfileInfo($subject). '' );
      } else {
        $this->add($this->component()->html($this->widgetProfileInfo($subject)), 3);
      }
      $this->renderContent();
  }

  private function widgetProfileInfo($subject){

// Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return;
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('group');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return;
    }

      $collapsible = '';
              if(!Engine_Api::_()->getApi('core', 'apptouch')->isTabletMode())
                  $collapsible = 'collapsible';

    $groupInfo = $this->dom()->new_('div', array('style' => 'clear: left;', 'data-role' => "{$collapsible}", 'data-content-theme' => 'c'), '', array($this->dom()->new_('h3', array(), $this->view->translate('Group Info')))) ;
    $infoContent = $this->dom()->new_('p', array(), '');
    $groupInfo->append($infoContent);

    if( !empty($subject->category_id) &&
            ($category = $subject->getCategory()) instanceof Core_Model_Item_Abstract &&
            !empty($category->title)) {
      $infoContent->text = $this->view->htmlLink(array('route' => 'group_general', 'action' => 'browse', 'category_id' => $subject->category_id), $this->view->translate((string)$category->title), array('data-role' => 'button'));
    }

    if( !empty($subject->description) ){
      $infoContent
        ->append($this->dom()->new_('span', array(), nl2br($subject->description)));
    }
    // Get staff
    $ids = array();
    $ids[] = $subject->getOwner()->getIdentity();
    $list = $subject->getOfficerList();
    foreach( $list->getAll() as $listiteminfo )
    {
      $ids[] = $listiteminfo->child_id;

    }

    $staff = array();
    $staffUl = $this->dom()->new_('ul', array('data-role' => 'listview', 'data-inset' => true));
    foreach( $ids as $id )
    {
      $user = Engine_Api::_()->getItem('user', $id);
      $staff[] = $info = array(
        'membership' => $subject->membership()->getMemberInfo($user),
        'user' => $user,
    );

      $span = $this->dom()->new_('span', array('class' => 'ui-li-count'));
      if( $subject->isOwner($info['user']) ){
        $span->text = !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : $this->view->translate('owner');
      } else {
        $span->text = !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : $this->view->translate('officer');
      }
      $staffUl->append(
        $this->dom()->new_('li', array('data-role' => 'list-divider'), $info['user']->__toString(), array(
          $span
        ))
      );
    }
    $infoContent->append($staffUl);
    $infoContent->append($this->dom()->new_('ul', array('data-role' => 'listview', 'data-inset' => true), '', array(
            $this->dom()->new_('li', array(), $this->view->translate(array('%s total view', '%s total views', $subject->view_count), $this->view->locale()->toNumber($subject->view_count))),
            $this->dom()->new_('li', array(), $this->view->translate(array('%s total member', '%s total members', $subject->member_count), $this->view->locale()->toNumber($subject->member_count))),
            $this->dom()->new_('li', array(), $this->view->translate('Last updated %s', $this->view->timestamp($subject->modified_date)))
          )));
    return $groupInfo;
  }
  
//  Tabs {
  public function tabMembers($active = null)
  {
    // Get params
    $page = $this->_getParam('page', 1);
    $search = $this->_getParam('search');
    $waiting = $this->_getParam('waiting', false);

    // Prepare data
    $group = Engine_Api::_()->core()->getSubject();
    $list = $group->getOfficerList();

    // get viewer
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity() && ($group->isOwner($viewer) || $list->has($viewer))) {
      $waitingMembers = Zend_Paginator::factory($group->membership()->getMembersSelect(false));
    }

    // if not showing waiting members, get full members
    $select = $group->membership()->getMembersObjectSelect();
    if ($search) {
      $select->where('displayname LIKE ?', '%' . $search . '%');
    }
    $fullMembers = Zend_Paginator::factory($select);

    // if showing waiting members, or no full members
    if (($viewer->getIdentity() && ($group->isOwner($viewer) || $list->has($viewer))) && ($waiting || ($fullMembers->getTotalItemCount() <= 0 && $search == ''))) {
      $paginator = $waitingMembers;
      $waiting = true;
    } else {
      $paginator = $fullMembers;
      $waiting = false;
    }

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', $page));

    // Do not render if nothing to show and no search
    if ($paginator->getTotalItemCount() <= 0 && '' == $search) {
      return;
    }
    return $paginator;
  }

  public function tabPhotos($active = null)
  {
    // Get paginator
    $subject = Engine_Api::_()->core()->getSubject('group');
    $album = $subject->getSingletonAlbum();
    $paginator = $album->getCollectiblesPaginator();
    $canUpload = $subject->authorization()->isAllowed(null, 'photo'); // todo

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    if ($paginator->getTotalItemCount() <= 0 && !$canUpload) {
      return;
    }
    if ($active) {
      $html = '<br /><div data-role="controlgroup" data-type="horizontal" style="text-align: center">';
      $html .= $this->view->htmlLink(array(
        'route' => 'group_extended',
        'controller' => 'photo',
        'action' => 'list',
        'subject' => $this->view->subject()->getGuid(),
      ), $this->view->translate('View All Photos'), array(
        'data-role' => 'button',
        'data-icon' => 'grid'
      ));
      if ($canUpload) {
        $html .= $this->view->htmlLink(array(
          'route' => 'group_extended',
          'controller' => 'photo',
          'action' => 'upload',
          'subject' => $this->view->subject()->getGuid(),
        ), $this->view->translate('Upload Photos'), array(
          'data-role' => 'button',
          'data-icon' => 'photo'
        ));
      }
      $html .= '</div>';
      $this
        ->add($this->component()->html($html), 6);
      if ($paginator->getTotalItemCount()) {
        $this
          ->add($this->component()->gallery($paginator), 7);
      }

    }
    // Do not render if nothing to show and cannot upload
    return array(
      'showContent' => false,
      'response' => $paginator
    );

  }

  public function tabEvents($active = null)
  {
    if( !Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event') ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    // Get subject and check auth
    $group = Engine_Api::_()->core()->getSubject('group');
    if (!$group->authorization()->isAllowed($viewer, 'view')) {
      return;
    }

    $table = Engine_Api::_()->getDbtable('events', 'event');
    $select = $table->select()
      ->where('parent_type = ?', 'group')
      ->where('parent_id = ?', $group->getIdentity());

    if ($this->_hasParam('search'))
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');

    // Get paginator
    $paginator = Zend_Paginator::factory($select);

    $canAdd = $group->authorization()->isAllowed(null, 'event') && Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show and cannot upload
    if ($paginator->getTotalItemCount() <= 0 && !$canAdd) {
      return;
    }
    if ($active) {
      $this->add($this->component()->html($this->view->htmlLink(array(
        'route' => 'event_general',
        'controller' => 'event',
        'action' => 'create',
        'parent_type' => 'group',
        'subject_id' => $this->view->subject()->getIdentity(),
      ), $this->view->translate('Add Events'), array(
        'data-role' => 'button',
        'data-icon' => 'plus'
      ))), 7)
        ->add($this->component()->itemSearch($this->getSearchForm()), 6)
        ->add($this->component()->itemList($paginator, null, array('listPaginator' => true,)),  8)
//        ->add($this->component()->paginator($paginator))
      ;
    }
    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabDiscussions($active = null)
  {

    $viewer = Engine_Api::_()->user()->getViewer();

    // Get paginator
    $table = Engine_Api::_()->getItemTable('group_topic');
    $select = $table->select()
      ->where('group_id = ?', Engine_Api::_()->core()->getSubject()->getIdentity())
      ->order('sticky DESC')
      ->order('modified_date DESC');
    ;
    $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    if($active){
      if ($this->view->viewer()->getIdentity()) {
        $this
          ->add($this->component()->html($this->view->htmlLink(array(
          'route' => 'group_extended',
          'controller' => 'topic',
          'action' => 'create',
          'subject' => $this->view->subject()->getGuid(),
        ), $this->view->translate('Post New Topic'), array(
          'data-icon' => 'plus',
          'data-role' => 'button'
        ))), 7)
          ->add($this->component()->itemSearch(new Apptouch_Form_Search()), 7)
          ->add($this->component()->itemList($paginator, null, array('listPaginator' => true,)), 8)
//          ->add($this->component()->paginator($paginator), 9)
        ;
      }
    }

    // Do not render if nothing to show and not viewer
    if ($paginator->getTotalItemCount() <= 0 && !$viewer->getIdentity()) {
      return;
    }
    return array(
      'showContent' => 0,
      'response' => $paginator
    );
    // todo     $this->view->canPost = Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment');

  }

  //  } Tabs
  //  } Profile Controller


  //  Group Controller {
  public function groupInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (0 !== ($group_id = (int)$this->_getParam('group_id')) &&
      null !== ($group = Engine_Api::_()->getItem('group', $group_id))
    ) {
      Engine_Api::_()->core()->setSubject($group);
    }

    $this->_helper->requireUser();
    $this->_helper->requireSubject('group');
  }

  public function groupEditAction()
  {
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid()) {
      return;
    }

    $group = Engine_Api::_()->core()->getSubject();
    $officerList = $group->getOfficerList();
    $form = new Group_Form_Edit();

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $k => $v) {
      $categoryOptions[$k] = $v;
    }
    $form->category_id->setMultiOptions($categoryOptions);

    if (count($form->category_id->getMultiOptions()) <= 1) {
      $form->removeElement('category_id');
    }

    if (!$this->getRequest()->isPost()) {
      // Populate auth
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('officer', 'member', 'registered', 'everyone');
      $actions = array('view', 'comment', 'invite', 'photo', 'event');
      $perms = array();
      foreach ($roles as $roleString) {
        $role = $roleString;
        if ($role === 'officer') {
          $role = $officerList;
        }
        foreach ($actions as $action) {
          if ($auth->isAllowed($group, $role, $action)) {
            $perms['auth_' . $action] = $roleString;
          }
        }
      }

      $form->populate($group->toArray());
      $form->populate($perms);
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getItemTable('group')->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();

      // Set group info
      $group->setFromArray($values);
      $group->save();

      if (!empty($values['photo'])) {
        $group->setPhoto($form->photo);
      }

      // Process privacy
      $auth = Engine_Api::_()->authorization()->context;

      $roles = array('officer', 'member', 'registered', 'everyone');

      if (empty($values['auth_view'])) {
        $values['auth_view'] = 'everyone';
      }

      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $photoMax = array_search($values['auth_photo'], $roles);
      $eventMax = array_search($values['auth_event'], $roles);
      $inviteMax = array_search($values['auth_invite'], $roles);

      foreach ($roles as $i => $role) {
        if ($role === 'officer') {
          $role = $officerList;
        }
        $auth->setAllowed($group, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($group, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($group, $role, 'photo', ($i <= $photoMax));
        $auth->setAllowed($group, $role, 'event', ($i <= $eventMax));
        $auth->setAllowed($group, $role, 'invite', ($i <= $inviteMax));
      }

      // Create some auth stuff for all officers
      $auth->setAllowed($group, $officerList, 'photo.edit', 1);
      $auth->setAllowed($group, $officerList, 'topic.edit', 1);

      // Add auth for invited users
      $auth->setAllowed($group, 'member_requested', 'view', 1);

      // Commit
      $db->commit();
    } catch (Engine_Image_Exception $e) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }


    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($group) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }


    // Redirect
    if ($this->_getParam('ref') === 'profile') {
      $this->redirect($group);
    } else {
      $this->redirect($this->view->url(array('route' => 'group_general', 'action' => 'manage')));
    }
  }

  public function groupDeleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $group = Engine_Api::_()->getItem('group', $this->getRequest()->getParam('group_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'delete')->isValid()) return;

    // Make form
    $form = new Group_Form_Delete();

    if (!$group) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Group doesn't exists or not authorized to delete");
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $db = $group->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $group->delete();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'group_general', true), Zend_Registry::get('Zend_Translate')->_('The selected group has been deleted.'), true);
  }

  //  } Group Controller


  //  Photo Controller {
  public function photoInit()
  {
    $this->addPageInfo('contentTheme', 'd');
    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($photo_id = (int)$this->_getParam('photo_id')) &&
        null !== ($photo = Engine_Api::_()->getItem('group_photo', $photo_id))
      ) {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if (0 !== ($group_id = (int)$this->_getParam('group_id')) &&
        null !== ($group = Engine_Api::_()->getItem('group', $group_id))
      ) {
        Engine_Api::_()->core()->setSubject($group);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
      'photo-upload',
      'photo-edit',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'photo-list' => 'group',
      'photo-upload' => 'group',
      'photo-view' => 'group_photo',
      'photo-edit' => 'group_photo',
    ));
  }

  public function photoListAction()
  {
    $group = Engine_Api::_()->core()->getSubject();
    $album = $group->getSingletonAlbum();

    if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'view')->isValid()) {
      return;
    }

    $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $group->authorization()->isAllowed(null, 'photo');
    $canUpload = $group->authorization()->isAllowed(null, 'photo');
    if ($canUpload)
      $this->add($this->component()->html($this->dom()->new_('a', array(
        'data-role' => 'button',
        'data-icon' => 'photo',
        'href' => $this->view->url(array('controller' => 'photo', 'action' => 'upload', 'subject' => $this->view->subject()->getGuid()), 'group_extended', true),
      ), $this->view->translate('Upload Photos'))));
    $this
      ->add($this->component()->crumb(array(
      array(
        'label' => $group->getTitle(),
        'attrs' => array(
          'href' => $group->getHref()
        )
      ),
      array(
        'label' => $this->view->translate('Photos'),
        'attrs' => array(
          'data-icon' => 'arrow-d'
        ),
        'active' => true
      )
    )))
      ->add($this->component()->gallery($paginator))
      ->renderContent();
  }

  public function photoViewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->core()->getSubject();
    $album = $photo->getCollection();
    $group = $photo->getGroup();
    $canEdit = $photo->canEdit(Engine_Api::_()->user()->getViewer());

    if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'view')->isValid()) {
      return;
    }

    if (!$viewer || !$viewer->getIdentity() || $photo->user_id != $viewer->getIdentity()) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }
    $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this
      ->add($this->component()->crumb(array(
      array(
        'label' => $group->getTitle(),
        'attrs' => array(
          'href' => $group->getHref()
        )
      ),
      array(
        'label' => $this->view->translate('Photos'),
        'attrs' => array(
          'data-icon' => 'arrow-d'
        ),
        'active' => true
      ),
      array(
        'label' => $album->getTitle(),
        'attrs' => array(
          'href' => $album->getHref()
        )
      )
    )))
      ->add($this->component()->gallery($paginator, $photo))
      ->renderContent();

  }

  public function photoUploadAction()
  {
    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
      return $this->_forward('upload-photo', null, null, array('format' => 'json'));
    }

    $group = Engine_Api::_()->core()->getSubject();
    if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'photo')->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $album = $group->getSingletonAlbum();

    //    $this->view->group = $group;
    $form = new Group_Form_Photo_Upload();
    $form->removeElement('file');
    $form->addElement('File', 'files', array(
      'label' => 'APPTOUCH_Upload Photos',
      'order' => 0,
      'isArray' => true
    ));
    $form->files->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    $form->files->setAttrib('data', array('group_id' => $group->getIdentity()));
    $this->add($this->component()->crumb(array(
      array(
        'label' => $group->getTitle(),
        'attrs' => array(
          'href' => $group->getHref()
        )
      ),
      array(
        'label' => $this->view->translate('Photos'),
        'attrs' => array(
          'data-icon' => 'arrow-d'
        ),
        'active' => true
      )
    )));

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('group_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $photodb = Engine_Api::_()->getDbtable('photos', 'group')->getAdapter();
      $photodb->beginTransaction();

      // Do other stuff
      $photoTable = Engine_Api::_()->getItemTable('group_photo');
      $picupFiles = $this->getPicupFiles('files');
      if (empty($picupFiles))
        $photos = $form->files->getFileName();
      else
        $photos = $picupFiles;
      $count = 0;

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $group, 'group_photo_upload', null, array('is_mobile' => true,
        'count' => count($photos)
      ));

      if (is_array($photos))
        foreach ($photos as $photoPath) {
          $photo = $photoTable->createRow();
          $photo->setFromArray(array(
            // We can set them now since only one album is allowed
            'collection_id' => $album->getIdentity(),
            'album_id' => $album->getIdentity(),

            'group_id' => $group->getIdentity(),
            'user_id' => $viewer->getIdentity(),
          ));
          $photo->setPhoto($photoPath);
          $photo->save();
          if ($action instanceof Activity_Model_Action && $count < 8) {
            $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
          }
          $count++;

        } else {
        $photo = $photoTable->createRow();
        $photo->setFromArray(array(
          // We can set them now since only one album is allowed
          'collection_id' => $album->getIdentity(),
          'album_id' => $album->getIdentity(),

          'group_id' => $group->getIdentity(),
          'user_id' => $viewer->getIdentity(),
        ));

        $photo->setPhoto($photos);
        $photo->save();
      }
      $photodb->commit();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }


    $this->redirect($group);
  }

  public function photoEditAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();
    $group = $photo->getParent('group');
    if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'photo.edit')->isValid()) {
      return;
    }
    $form = new Group_Form_Photo_Edit();

    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'group')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->setFromArray($form->getValues())->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Changes saved'));
  }

  public function photoDeleteAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();
    $group = $photo->getParent('group');
    if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'photo.edit')->isValid()) {
      return;
    }

    $this->view->form = $form = new Group_Form_Photo_Delete();

    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'group')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->delete();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($group, Zend_Registry::get('Zend_Translate')->_('Photo deleted'));
  }

  //  } Photo Controller


  //  Post Controller {
  public function postInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (Engine_Api::_()->core()->hasSubject()) return;

    if (0 !== ($post_id = (int)$this->_getParam('post_id')) &&
      null !== ($post = Engine_Api::_()->getItem('group_post', $post_id))
    ) {
      Engine_Api::_()->core()->setSubject($post);
    }

    else if (0 !== ($topic_id = (int)$this->_getParam('topic_id')) &&
      null !== ($topic = Engine_Api::_()->getItem('group_topic', $topic_id))
    ) {
      Engine_Api::_()->core()->setSubject($topic);
    }

    $this->_helper->requireUser->addActionRequires(array(
      'post-edit',
      'post-delete',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'post-edit' => 'group_post',
      'post-delete' => 'group_post',
    ));
  }

  public function postEditAction()
  {
    $post = Engine_Api::_()->core()->getSubject('group_post');
    $group = $post->getParent('group');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$group->isOwner($viewer) && !$post->isOwner($viewer) && !$group->authorization()->isAllowed($viewer, 'topic.edit')) {
      return $this->_helper->requireAuth->forward();
    }

    $this->view->form = $form = new Group_Form_Post_Edit();

    if (!$this->getRequest()->isPost()) {
      $form->populate($post->toArray());
      $form->body->setValue(html_entity_decode($post->body));
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $table = $post->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $post->setFromArray($form->getValues());
      $post->modified_date = date('Y-m-d H:i:s');
      $post->body = htmlspecialchars($post->body, ENT_NOQUOTES, 'UTF-8');
      $post->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
    $topic = $post->getParentTopic();
    // Try to get topic
    return $this->redirect($topic, Zend_Registry::get('Zend_Translate')->_('The changes to your post have been saved.'));
  }

  public function postDeleteAction()
  {
    $post = Engine_Api::_()->core()->getSubject('group_post');
    $group = $post->getParent('group');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$group->isOwner($viewer) && !$post->isOwner($viewer) && !$group->authorization()->isAllowed($viewer, 'topic.edit')) {
      return $this->_helper->requireAuth->forward();
    }

    $form = new Group_Form_Post_Delete();

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $table = $post->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    $topic_id = $post->topic_id;

    try
    {
      $post->delete();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    // Try to get topic
    $topic = Engine_Api::_()->getItem('group_topic', $topic_id);
    $href = (null === $topic ? $group->getHref() : $topic->getHref());

    return $this->redirect($href, Zend_Registry::get('Zend_Translate')->_('Post deleted.'));
  }

  public function postCanEdit($user)
  {
    return $this->getParent()->getParent()->authorization()->isAllowed($user, 'edit') || $this->getParent()->getParent()->authorization()->isAllowed($user, 'topic.edit') || $this->isOwner($user);
  }

  //  } Post Controller


  //  Topic Controller {
  public function topicInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (Engine_Api::_()->core()->hasSubject()) return;

    /*
    if( 0 !== ($post_id = (int) $this->_getParam('post_id')) &&
        null !== ($post = Engine_Api::_()->getItem('group_post', $post_id)) )
    {
      Engine_Api::_()->core()->setSubject($post);
    }

    else */
    if (0 !== ($topic_id = (int)$this->_getParam('topic_id')) &&
      null !== ($topic = Engine_Api::_()->getItem('group_topic', $topic_id))
    ) {
      Engine_Api::_()->core()->setSubject($topic);
    }

    else if (0 !== ($group_id = (int)$this->_getParam('group_id')) &&
      null !== ($group = Engine_Api::_()->getItem('group', $group_id))
    ) {
      Engine_Api::_()->core()->setSubject($group);
    }
  }

  public function topicIndexAction()
  {
    if (!$this->_helper->requireSubject('group')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid()) return;

    $this->view->group = $group = Engine_Api::_()->core()->getSubject();

    $table = Engine_Api::_()->getDbtable('topics', 'group');
    $select = $table->select()
      ->where('group_id = ?', $group->getIdentity())
      ->order('sticky DESC')
      ->order('modified_date DESC');

    $paginator = Zend_Paginator::factory($select);
    $can_post = $this->_helper->requireAuth->setAuthParams(null, null, 'comment')->checkRequire();
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    $this->add($this->component()->itemList($paginator, null, array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function topicViewAction()
  {
    if (!$this->_helper->requireSubject('group_topic')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid()) return;

    $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->topic = $topic = Engine_Api::_()->core()->getSubject();
    $this->group = $group = $topic->getParentGroup();
    $this->canEdit = $topic->canEdit(Engine_Api::_()->user()->getViewer());
    $this->officerList = $group->getOfficerList();

    $this->canPost = $canPost = $group->authorization()->isAllowed($viewer, 'comment');

    if (!$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id) {
      $topic->view_count = new Zend_Db_Expr('view_count + 1');
      $topic->save();
    }

    // Check watching
    $isWatching = null;
    if ($viewer->getIdentity()) {
      $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'group');
      $isWatching = $topicWatchesTable
        ->select()
        ->from($topicWatchesTable->info('name'), 'watch')
        ->where('resource_id = ?', $group->getIdentity())
        ->where('topic_id = ?', $topic->getIdentity())
        ->where('user_id = ?', $viewer->getIdentity())
        ->limit(1)
        ->query()
        ->fetchColumn(0);
      if (false === $isWatching) {
        $isWatching = null;
      } else {
        $isWatching = (bool)$isWatching;
      }
    }
    $this->isWatching = $isWatching;

    // @todo implement scan to post
    $this->post_id = $post_id = (int)$this->_getParam('post');

    $table = Engine_Api::_()->getDbtable('posts', 'group');
    $select = $table->select()
      ->where('group_id = ?', $group->getIdentity())
      ->where('topic_id = ?', $topic->getIdentity())
      ->order('creation_date ASC');

    $paginator = Zend_Paginator::factory($select);

    // Skip to page of specified post
    if (0 !== ($post_id = (int)$this->_getParam('post_id')) &&
      null !== ($post = Engine_Api::_()->getItem('group_post', $post_id))
    ) {
      $icpp = $paginator->getItemCountPerPage();
      $page = ceil(($post->getPostIndex() + 1) / $icpp);
      $paginator->setCurrentPageNumber($page);
    }

    // Use specified page
    else if (0 !== ($page = (int)$this->_getParam('page'))) {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }

    if ($canPost && !$topic->closed) {
      $form = new Group_Form_Post_Create();
      $form->populate(array(
        'topic_id' => $topic->getIdentity(),
        'ref' => $topic->getHref(),
        'watch' => (false === $isWatching ? '0' : '1'),
      ));
    }

    $options = array();
    if ($canPost && !$topic->closed)
      $options[] = array(
        'label' => $this->view->translate('Post Reply'),
        'attrs' => array(
          'href' => $this->topic->getHref(array('action' => 'post')),
          'data-icon' => 'chat'
        )
      );

    if ($viewer->getIdentity()) {
      if (!$isWatching)
        $options[] = array(
          'label' => $this->view->translate('Watch Topic'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'watch', 'watch' => '1')),
            'data-icon' => 'wifi'
          )
        );
      else
        $options[] = array(
          'label' => $this->view->translate('Stop Watching Topic'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'watch', 'watch' => '0')),
            'data-icon' => 'wifi'
          )
        );
    }

    if ($this->canEdit || $this->canDelete) {
      if ($this->canEdit) {
        if (!$topic->sticky)
          $options[] = array(
            'label' => $this->view->translate('Make Sticky'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'sticky', 'sticky' => '1')),
              'data-icon' => 'page'
            )
          );
        else
          $options[] = array(
            'label' => $this->view->translate('Remove Sticky'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'sticky', 'sticky' => '0')),
              'data-icon' => 'delete'
            )
          );
        if (!$topic->closed)
          $options[] = array(
            'label' => $this->view->translate('Close'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'close', 'close' => '1')),
              'data-icon' => 'lock'
            )
          );
        else
          $options[] = array(
            'label' => $this->view->translate('Open'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'close', 'close' => '0')),
              'data-icon' => 'wifi'
            )
          );

        $options[] = array(
          'label' => $this->view->translate('Rename'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'rename')),
            'data-icon' => 'edit',
            'data-rel' => 'dialog'
          )
        );
        $options[] = array(
          'label' => $this->view->translate('Delete'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'delete')),
            'data-icon' => 'delete',
            'data-rel' => 'dialog'
          )
        );
      }
    }
    $links = array(
      array(
        'label' => $group->getTitle(),
        'attrs' => array(
          'href' => $group->getHref()
        )
      ),
      array(
        'label' => $this->view->translate("Discussions"),
        'attrs' => array(
          'href' => $this->view->url(array('controller' => 'topic',
            'action' => 'index',
            'group_id' => $group->getIdentity()), 'group_extended', true),
          'class' => 'ui-btn-active',
          'data-icon' => 'arrow-d'
        )
      )
    );

    $this
      ->add($this->component()->crumb($links))
      ->add($this->component()->discussion($topic, $paginator, array(
      'options' => $options,
      'postForm' => isset($form) ? $form : null
    ), 'topicPostCustomize'))
      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function topicPostCustomize($post)
  {
    $postFormat = array();
    $options = array();
    if ($this->canPost && !$this->topic->closed)
      $options[] = array(
        'label' => $this->view->translate('Quote'),
        'attrs' => array(
          'href' => $this->view->url(array(
            'controller' => 'topic',
            'action' => 'post',
            'group_id' => $this->group->getIdentity(),
            'topic_id' => $this->view->subject()->getIdentity(),
            'quote_id' => $post->getIdentity(),
          ), 'group_extended'),
          'data-icon' => 'chat'
        )
      );
    if ($post->user_id ==
      $this->view->viewer()->getIdentity() ||
      $this->group->getOwner()->getIdentity() == $this->view->viewer()->getIdentity()
    ) {

      $options[] = array(
        'label' => $this->view->translate('Edit'),
        'attrs' => array(
          'href' => $this->view->url(array('controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity()), 'group_extended'),
          'data-icon' => 'edit'
        )
      );

      $options[] = array(
        'label' => $this->view->translate('Delete'),
        'attrs' => array(
          'href' => $this->view->url(array('controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity()), 'group_extended'),
          'data-icon' => 'delete',
          'data-rel' => 'dialog'
        )
      );
    }

    $postFormat['options'] = $options;
    $postFormat['owner'] = array();

    $user = $this->view->item('user', $post->user_id);
    $isOwner = false;
    $isMember = false;
    if ($this->group->isOwner($user)) {
      $isOwner = true;
      $isMember = true;
    } else if ($this->group->membership()->isMember($user)) {
      $isMember = true;
    }

    $postFormat['owner']['postCount'] = $isOwner ? $this->view->translate('Host') : ($isMember ? $this->view->translate('Member') : false);

    return $postFormat;
  }

  public function topicCreateAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('group')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) return;

    $group = Engine_Api::_()->core()->getSubject('group');
    $viewer = Engine_Api::_()->user()->getViewer();

    // Make form
    $form = new Group_Form_Topic_Create();
    $links = array(
      array(
        'label' => $group->getTitle(),
        'attrs' => array(
          'href' => $group->getHref()
        )
      ),
      array(
        'label' => $this->view->translate("Discussions"),
        'attrs' => array(
          'class' => 'ui-btn-active',
          'data-icon' => 'arrow-d'
        )
      ),
    );
    $this
      ->add($this->component()->crumb($links));

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['group_id'] = $group->getIdentity();

    $topicTable = Engine_Api::_()->getDbtable('topics', 'group');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'group');
    $postTable = Engine_Api::_()->getDbtable('posts', 'group');

    $db = $group->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create topic
      $topic = $topicTable->createRow();
      $topic->setFromArray($values);
      $topic->save();

      // Create post
      $values['topic_id'] = $topic->topic_id;

      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      // Create topic watch
      $topicWatchesTable->insert(array(
        'resource_id' => $group->getIdentity(),
        'topic_id' => $topic->getIdentity(),
        'user_id' => $viewer->getIdentity(),
        'watch' => (bool)$values['watch'],
      ));

      // Add activity
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $topic, 'group_topic_create', null, array('is_mobile' => true));
      if ($action) {
        $action->attach($topic);
      }

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    // Redirect to the post
    $this->redirect($post);
  }

  public function topicPostAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('group_topic')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) return;

    $topic = Engine_Api::_()->core()->getSubject();
    $group = $topic->getParentGroup();

    if ($topic->closed) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This has been closed for posting.');
      return;
    }

    // Make form
    $form = new Group_Form_Post_Create();
    $form->removeAttrib('action');
    $quoteText = '';
    $quote_id = $this->getRequest()->getParam('quote_id');
    if (!empty($quote_id)) {
      $quote = Engine_Api::_()->getItem('group_post', $quote_id);
      if ($quote->user_id == 0) {
        $owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
      } else {
        $owner_name = $quote->getOwner()->__toString();
      }
      $quoteText = "<blockquote><strong>" . $this->view->translate('%1$s said:', $owner_name) . "</strong><br />" . $quote->body . "</blockquote><br />";
      $this->add($this->component()->html($quoteText));
    }

    $links = array(
      array(
        'label' => $group->getTitle(),
        'attrs' => array(
          'href' => $group->getHref()
        )
      ),
      array(
        'label' => $this->view->translate("Discussions"),
        'attrs' => array(
          'href' => $group->getHref(array('tab' => 'discussions'))
        )
      ),
      array(
        'label' => $topic->getTitle(),
        'attrs' => array(
          'href' => $topic->getHref(),
          'class' => 'ui-btn-active',
          'data-icon' => 'arrow-d'
        )
      ),
    );
    $this
      ->add($this->component()->crumb($links));

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();
    $topicOwner = $topic->getOwner();
    $isOwnTopic = $viewer->isSelf($topicOwner);

    $postTable = Engine_Api::_()->getDbtable('posts', 'group');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'group');
    $userTable = Engine_Api::_()->getItemTable('user');
    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

    $values = $form->getValues();
    $values['body'] = $quoteText . $values['body'];
    $values['user_id'] = $viewer->getIdentity();
    $values['group_id'] = $group->getIdentity();
    $values['topic_id'] = $topic->getIdentity();

    $watch = (bool)$values['watch'];
    $isWatching = $topicWatchesTable
      ->select()
      ->from($topicWatchesTable->info('name'), 'watch')
      ->where('resource_id = ?', $group->getIdentity())
      ->where('topic_id = ?', $topic->getIdentity())
      ->where('user_id = ?', $viewer->getIdentity())
      ->limit(1)
      ->query()
      ->fetchColumn(0);

    $db = $group->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create post
      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      // Watch
      if (false === $isWatching) {
        $topicWatchesTable->insert(array(
          'resource_id' => $group->getIdentity(),
          'topic_id' => $topic->getIdentity(),
          'user_id' => $viewer->getIdentity(),
          'watch' => (bool)$watch,
        ));
      } else if ($watch != $isWatching) {
        $topicWatchesTable->update(array(
          'watch' => (bool)$watch,
        ), array(
          'resource_id = ?' => $group->getIdentity(),
          'topic_id = ?' => $topic->getIdentity(),
          'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      // Activity
      $action = $activityApi->addActivity($viewer, $topic, 'group_topic_reply', null, array('is_mobile' => true));
      if ($action) {
        $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
      }

      // Notifications
      $notifyUserIds = $topicWatchesTable->select()
        ->from($topicWatchesTable->info('name'), 'user_id')
        ->where('resource_id = ?', $group->getIdentity())
        ->where('topic_id = ?', $topic->getIdentity())
        ->where('watch = ?', 1)
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

      foreach ($userTable->find($notifyUserIds) as $notifyUser) {
        // Don't notify self
        if ($notifyUser->isSelf($viewer)) {
          continue;
        }
        if ($notifyUser->isSelf($topicOwner)) {
          $type = 'group_discussion_response';
        } else {
          $type = 'group_discussion_reply';
        }
        $notifyApi->addNotification($notifyUser, $viewer, $topic, $type, array(
          'message' => $this->view->BBCode($post->body),
        ));
      }

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    // Redirect to the post
    $this->redirect($post);
  }

  public function topicStickyAction()
  {
    $topic = Engine_Api::_()->core()->getSubject('group_topic');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->_helper->requireSubject('group_topic')->isValid()) return;
    if ($viewer->getIdentity() != $topic->user_id) {
      if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'topic.edit')->isValid()) return;
    }

    $table = $topic->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $topic = Engine_Api::_()->core()->getSubject();
      $topic->sticky = (null === $this->_getParam('sticky') ? !$topic->sticky : (bool)$this->_getParam('sticky'));
      $topic->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($topic);
  }

  public function topicCloseAction()
  {
    $topic = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->_helper->requireSubject('group_topic')->isValid()) return;
    if ($viewer->getIdentity() != $topic->user_id) {
      if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'topic.edit')->isValid()) return;
    }

    $table = $topic->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $topic = Engine_Api::_()->core()->getSubject();
      $topic->closed = (null === $this->_getParam('closed') ? !$topic->closed : (bool)$this->_getParam('closed'));
      $topic->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $this->redirect($topic);
  }

  public function topicRenameAction()
  {
    $topic = Engine_Api::_()->core()->getSubject('group_topic');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->_helper->requireSubject('group_topic')->isValid()) return;
    if ($viewer->getIdentity() != $topic->user_id) {
      if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'topic.edit')->isValid()) return;
    }

    $form = new Group_Form_Topic_Rename();

    if (!$this->getRequest()->isPost()) {
      $form->title->setValue(htmlspecialchars_decode($topic->title));
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $table = $topic->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $title = htmlspecialchars($form->getValue('title'));

      $topic = Engine_Api::_()->core()->getSubject();
      $topic->title = $title;
      $topic->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Topic renamed.'));
  }

  public function topicDeleteAction()
  {
    $topic = Engine_Api::_()->core()->getSubject('group_topic');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->_helper->requireSubject('group_topic')->isValid()) return;
    if ($viewer->getIdentity() != $topic->user_id) {
      if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'topic.edit')->isValid()) return;
    }

    $form = new Group_Form_Topic_Delete();

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $table = $topic->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $topic = Engine_Api::_()->core()->getSubject();
      $group = $topic->getParent('group');
      $topic->delete();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($group, Zend_Registry::get('Zend_Translate')->_('Topic deleted.'));
  }

  public function topicWatchAction()
  {
    $topic = Engine_Api::_()->core()->getSubject();
    $group = Engine_Api::_()->getItem('group', $topic->group_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'view')->isValid()) {
      return;
    }

    $watch = $this->_getParam('watch', true);

    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'group');
    $db = $topicWatchesTable->getAdapter();
    $db->beginTransaction();

    try
    {
      $isWatching = $topicWatchesTable
        ->select()
        ->from($topicWatchesTable->info('name'), 'watch')
        ->where('resource_id = ?', $group->getIdentity())
        ->where('topic_id = ?', $topic->getIdentity())
        ->where('user_id = ?', $viewer->getIdentity())
        ->limit(1)
        ->query()
        ->fetchColumn(0);

      if (false === $isWatching) {
        $topicWatchesTable->insert(array(
          'resource_id' => $group->getIdentity(),
          'topic_id' => $topic->getIdentity(),
          'user_id' => $viewer->getIdentity(),
          'watch' => (bool)$watch,
        ));
      } else if ($watch != $isWatching) {
        $topicWatchesTable->update(array(
          'watch' => (bool)$watch,
        ), array(
          'resource_id = ?' => $group->getIdentity(),
          'topic_id = ?' => $topic->getIdentity(),
          'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($topic);
  }
  //  } Topic Controller




}
