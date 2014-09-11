<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ReviewController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Rate_ReviewController extends Core_Controller_Action_Standard
{

  public function createAction(){

    $result = 0;
    $page_id = (int)$this->_getParam('page_id');

    $tbl_page = Engine_Api::_()->getDbTable('pages', 'page');
    $page = $tbl_page->findRow($page_id);

    $viewer = Engine_Api::_()->user()->getViewer();

    // if page and viewer exists
    if (!$page || !$viewer->getIdentity()){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $this->view->form = $form = new Rate_Form_Review_Create;

    $form->removeAttrib('onsubmit');

    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => $this->view->url(array('action' => 'index', 'page_id' => $page_id), 'page_review', true),
        'onclick' => ''
      ));
    }

    $types = Engine_Api::_()->getApi('core', 'rate')->getPageTypes($page->getIdentity());

    $module_path = Engine_Api::_()->getModuleBootstrap('mobile')->getModulePath();
    $form->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $counter = 0;
    foreach ($types as $type){
      $name  = 'rate_'.$type->getIdentity();
      $form->addElement('Hidden', $name, array(
        'label' => $type->label,
        'value' => $type->value,
        'order' => $counter
      ));
      $form->getElement($name)->addDecorator('MobileFormRate');
      $counter++;
    }

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');

    if (!$tbl->isAllowedPost($page->getIdentity(), $viewer)){
      return ;
    }

    $values = $form->getValues();
    $values['page_id'] = $page_id;
    $values['user_id'] = $viewer->getIdentity();
    $values['creation_date'] = date('Y-m-d H:i:s');
    $values['modified_date'] = date('Y-m-d H:i:s');

    $row = $tbl->createRow($values);
    $result = (bool)$row->save();

    if ($result){

      // Add Votes
      $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
      $type_keys = array();
      foreach ($types as $type){
        $type_keys[$type->type_id] = 'rate_'.$type->type_id;
      }

      foreach ($values as $key=>$value){

        if ($type_id = array_search($key, $type_keys)){

          $tbl_vote->createRow(array(
            'type_id' => $type_id,
            'review_id' => $row->getIdentity(),
            'page_id' => $page->getIdentity(),
            'rating' => ($value <= 5 || $value >= 0) ? (int)$value : 0,
            'creation_date' => date('Y-m-d H:i:s')
          ))->save();

        }
      }

      // Add Search
      Engine_Api::_()->getDbTable('search', 'page')->saveData($row);

      // Add Action
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $link = $row->getLink();
      $page = $row->getPage();

      $action = $api->addActivity($viewer, $page, 'pagereview_new', null, array('link' => $link, 'is_mobile' => true));
      $api->attachActivity($action, $row, Activity_Model_Action::ATTACH_DESCRIPTION);

      // Get Page Teams
      $admins = $page->getAdmins();

      foreach ($admins as $admin){

        // if owner
        if ($admin->getIdentity() == $viewer->getIdentity()){
          continue;
        }
        // Send Notify
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($admin, $viewer, $row, 'post_pagereview', array(
          'label' => $row->getShortType()
        ));

      }
      $this->view->id = $row->getIdentity();

    }

    $this->view->result = $result;

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('RATE_REVIEW_CREATE_SUCCESS')),
      'return_url'=> $this->view->url(array('action' => 'view', 'review_id' => $this->view->id), 'page_review', true),
    ));

  }

  public function viewAction(){

    $result = false;
    $row = Engine_Api::_()->getDbTable('pagereviews', 'rate')
      ->findRow((int)$this->_getParam('review_id'));

    if (!$row){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    Engine_Api::_()->core()->setSubject($row);

    $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
    $select = $tbl_vote->select()
        ->where('review_id = ?', $row->getIdentity());
    $votes = $tbl_vote->fetchAll($select);

    $vote_list = array();
    foreach ($votes as $vote){
      $vote_list[$vote->type_id] = $vote->rating;
    }
    $types = Engine_Api::_()->getApi('core', 'rate')->getPageTypes($row->page_id);
    foreach ($types as $key => $type){
      if (isset($vote_list[$type->type_id])){
        $types[$key]->value = $vote_list[$type->type_id];
      }
    }
    $this->view->types = $types;

    $this->view->owner = $row->getOwner();
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $this->view->review = $row;

    $this->view->subject = $subject = Engine_Api::_()->getDbTable('pages', 'page')->findRow($row->page_id);

    if (!$subject){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }



  }

  public function editAction(){

    $review_id = (int)$this->_getParam('pagereview_id');

    $viewer = Engine_Api::_()->user()->getViewer();

    $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');
    $row = $tbl->findRow($review_id);

    if (!$row || !$viewer->isOwner($row->getOwner())){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
    $select = $tbl_vote->select()
        ->where('review_id = ?', $row->getIdentity());
    $votes = $tbl_vote->fetchAll($select);

    $vote_list = array();
    foreach ($votes as $vote){
      $vote_list[$vote->type_id] = $vote->rating;
    }

    $types = Engine_Api::_()->getApi('core', 'rate')->getPageTypes($row->page_id);
    foreach ($types as $key => $type){
      if (isset($vote_list[$type->type_id])){
        $types[$key]->value = $vote_list[$type->type_id];
      }
    }

    $this->view->form = $form = new Rate_Form_Review_Edit;

    $form->populate($row->toArray());

    $form->removeAttrib('onsubmit');

    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => $this->view->url(array('action' => 'view', 'review_id' => $review_id), 'page_review', true),
        'onclick' => ''
      ));
    }

    $module_path = Engine_Api::_()->getModuleBootstrap('mobile')->getModulePath();
    $form->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $counter = 0;
    foreach ($types as $type){
      $name  = 'rate_'.$type->getIdentity();
      $form->addElement('Hidden', $name, array(
        'label' => $type->label,
        'value' => $type->value,
        'order' => $counter
      ));
      $form->getElement($name)->addDecorator('MobileFormRate');
      $counter++;
    }


    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    $values = $form->getValues();
    $values['modified_date'] = date('Y-m-d H:i:s');
    $row->setFromArray($values);
    $result = (bool)$row->save();

    if ($result){

      // Delete Old Votes
      $tbl_vote->delete(array(
        'review_id = ?' => $row->getIdentity()
      ));

      $type_keys = array();
      foreach ($types as $type){
        $type_keys[$type->type_id] = 'rate_'.$type->type_id;
      }
      foreach ($values as $key=>$value){
        if ($type_id = array_search($key, $type_keys)){
          $tbl_vote->createRow(array(
            'type_id' => $type_id,
            'review_id' => $row->getIdentity(),
            'page_id' => $row->page_id,
            'rating' => ($value <= 5 || $value >= 0) ? (int)$value : 0,
            'creation_date' => date('Y-m-d H:i:s')
          ))->save();
        }
      }

      // Delete and Create Search
      $tbl_search = Engine_Api::_()->getDbTable('search', 'page');
      $tbl_search->saveData($row);

    }

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('RATE_REVIEW_EDIT_SUCCESS')),
      'return_url'=> $this->view->url(array('action' => 'view', 'review_id' => $review_id), 'page_review', true),
    ));


  }

  public function removeAction(){

    $result = false;
    $review_id = $this->_getParam('review_id');

    $viewer = Engine_Api::_()->user()->getViewer();

    $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');
    $row = $tbl->findRow($review_id);

    if (!$row){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $this->view->form = $form = new Engine_Form;

    $form->setTitle('RATE_REVIEW_DELETE')
      ->setDescription('RATE_REVIEW_DELETEDESC')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');

    $form->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $form->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => urldecode($this->_getParam('return_url')),
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');

    $form->setAction($this->view->url(array(
      'action' => 'remove',
      'review_id' => $review_id,
      'return_url' => $this->_getParam('return_url')
    ), 'page_review'));

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }
    $page_id = $row->page_id;

    if (!$viewer->isOwner($row->getOwner()) && !Engine_Api::_()->getApi('core', 'rate')
        ->isAllowRemoveReview($page_id, $viewer)){
      return ;
    }

    $result = (bool)$row->delete();

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('RATE_REVIEW_DELETE_SUCCESS')),
      'return_url'=> $this->view->url(array('action' => 'index', 'page_id' => $page_id), 'page_review', true),
    ));


  }

  public function indexAction(){

    $this->view->result = true;
    $page_id = $this->_getParam('page_id');

    $this->view->subject = $subject = Engine_Api::_()->getDbTable('pages', 'page')->findRow($page_id);

    if (!$subject || !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $types = Engine_Api::_()->getApi('core', 'rate')->getPageTypes($page_id);

    // get paginator
    $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');
    $this->view->paginator = $tbl->getPaginator($page_id, $viewer->getIdentity(), $this->_getParam('page'));

    $this->view->isAllowedPost = $tbl->isAllowedPost($page_id, $viewer);

    // is allowed remove
    $this->view->isAllowedRemove = Engine_Api::_()->getApi('core', 'rate')
            ->isAllowRemoveReview($page_id, $viewer);
    $this->view->countOptions = count($types);
    $this->view->count = $this->view->paginator->getCurrentItemCount();

  }


}