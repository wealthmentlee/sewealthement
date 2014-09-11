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
    
class Album_IndexController extends Core_Controller_Action_Standard
{
  public function browseAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid() ) return;

    $form = $this->view->form = new Mobile_Form_Search();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $this->_helper->redirector->gotoRouteAndExit(array(
        'page'   => 1,
        'search' => $this->getRequest()->getPost('search'),
      ));
    } else {
      $form->getElement('search')->setValue($this->_getParam('search'));
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
      ->getNavigation('album_main');
		
    // Prepare data
    $table = Engine_Api::_()->getItemTable('album');
    $order = 'modified_date';

    $select = $table->select()
      ->where("search = 1")
      ->order($order . ' DESC');

    $user_id = $this->_getParam('user');
    if ($user_id) $select->where("owner_id = ?", $user_id);

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%'.$this->_getParam('search').'%');
    }

    $this->view->userObj = ($user_id) ? Engine_Api::_()->user()->getUser($user_id) : null;

    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
    
    $paginator = $this->view->paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber( $this->_getParam('page') );
  }

  public function manageAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;

		$search_form = $this->view->search_form = new Mobile_Form_Search();
    if ($this->getRequest()->isPost() && $search_form->isValid($this->getRequest()->getPost())) {
      $this->_helper->redirector->gotoRouteAndExit(array(
        'page'   => 1,
        'search' => $this->getRequest()->getPost('search'),
      ));
    } else {
      $search_form->getElement('search')->setValue($this->_getParam('search'));
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
      ->getNavigation('album_main');

    // Get params
    $this->view->page = $page = $this->_getParam('page');
    
    // Prepare data
    $user = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getItemTable('album');

    $select = $table->select()
      ->where('owner_id = ?', $user->getIdentity())->order('modified_date DESC');

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%'.$this->_getParam('search').'%');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($page);
  }
}