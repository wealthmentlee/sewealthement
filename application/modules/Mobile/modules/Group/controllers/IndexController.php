<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Group_IndexController extends Core_Controller_Action_Standard
{

  public function init()
  {

    if( !$this->_helper->requireAuth()->setAuthParams('group', null, 'view')->isValid() )
        return;

    $id = $this->_getParam('group_id', $this->_getParam('id', null));
    if( $id ) {
      $group = Engine_Api::_()->getItem('group', $id);
      if( $group ) {
        Engine_Api::_()->core()->setSubject($group);
      }
    }
  }

  public function browseAction()
  {
    // Navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
            ->getNavigation('group_main');

    // Form
    $this->view->formFilter = $formFilter = new Mobile_Form_Search();

		if ($this->getRequest()->isPost() && $formFilter->isValid($this->getRequest()->getPost())) {
      $this->_helper->redirector->gotoRouteAndExit(array(
        'page'   => 1,
        'search' => $this->getRequest()->getPost('search'),
      ));
    } else {
      $formFilter->getElement('search')->setValue($this->_getParam('search'));
    }

 		$table = Engine_Api::_()->getItemTable('group');
    $select = $table->select();

    // Search
    $select->where('search = ?', 1);

		if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%'.$this->_getParam('search').'%');
    }

    $this->view->userObj = ($this->_getParam('user')) ? Engine_Api::_()->user()->getUser($this->_getParam('user')) : null;

    if ($this->view->userObj){
      $select = Engine_Api::_()->getDbtable('membership', 'group')->getMembershipsOfSelect($this->view->userObj);
    }

		$select->order('creation_date DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
  }

  public function listAction()
  {
    
  }

  public function manageAction()
  {
    // Navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
          ->getNavigation('group_main');

    // Form
    $this->view->formFilter = $formFilter = new Mobile_Form_Search();

		if ($this->getRequest()->isPost() && $formFilter->isValid($this->getRequest()->getPost())) {
      $this->_helper->redirector->gotoRouteAndExit(array(
        'page'   => 1,
        'search' => $this->getRequest()->getPost('search'),
      ));
    } else {
      $formFilter->getElement('search')->setValue($this->_getParam('search'));
    }

    $viewer = $this->_helper->api()->user()->getViewer();
    $membership = Engine_Api::_()->getDbtable('membership', 'group');
    $select = $membership->getMembershipsOfSelect($viewer);

    $table = Engine_Api::_()->getItemTable('group');
    $tName = $table->info('name');

    if( $this->_getParam('search', false) ) {
      $select->where(
          $table->getAdapter()->quoteInto("`{$tName}`.`title` LIKE ?", '%' . $this->_getParam('search') . '%') . ' OR ' .
          $table->getAdapter()->quoteInto("`{$tName}`.`description` LIKE ?", '%' . $this->_getParam('search') . '%')
      );
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->search = $this->_getParam('search');
		$paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
  }
}