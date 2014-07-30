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
    
class User_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {

  }

  public function homeAction()
  {
    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

    if( !Engine_Api::_()->user()->getViewer()->getIdentity() )
    {
      return $this->_helper->redirector->gotoRoute(array(), 'home', true);
    }

		$content = Engine_Content::getInstance();
		$table = Engine_Api::_()->getDbtable('pages', 'mobile');
		$content->setStorage($table);
		$this->_helper->content->setContent($content);

    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;

		}

  public function browseAction()
  {
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }
		
    if( !$this->_executeSearch() ) {
      throw new Exception('error');
    }

    if( $this->_getParam('ajax') ) {
      $this->renderScript('_browseUsers.tpl');
    }
  }

  protected function _executeSearch()
  {
    // Check form
    $form = new Mobile_Form_Search();

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $this->_helper->redirector->gotoRouteAndExit(array(
        'page'   => 1,
        'search' => $this->getRequest()->getPost('search'),
      ));
    } else {
      $form->getElement('search')->setValue($this->_getParam('search'));
    }

    if( !$form->isValid($this->_getAllParams()) ) {
      $this->view->error = true;
      return false;
    }

    $this->view->form = $form;

    // Get search params
    $page = (int)  $this->_getParam('page', 1);
    $ajax = (bool) $this->_getParam('ajax', false);
    $options = $form->getValues();

    // Get table info
    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
    $searchTableName = $searchTable->info('name');

    $displayname = @$options['search'];
    // Contruct query
    $select = $table->select()
      //->setIntegrityCheck(false)
      ->from($userTableName)
      ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
      //->group("{$userTableName}.user_id")
      ->where("{$userTableName}.search = ?", 1)
      ->where("{$userTableName}.enabled = ?", 1)
		  ->where("{$userTableName}.verified = ?", 1)
      ->order("{$userTableName}.displayname ASC");

    // Add displayname
    if( !empty($displayname) ) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
    }

    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
    foreach( $searchParts as $k => $v ) {
      $select->where("`{$searchTableName}`.{$k}", $v);
    }

    // Build paginator
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($page);

		$this->view->paginator = $paginator;
    $this->view->page = $page;
    $this->view->ajax = $ajax;
    $this->view->users = $paginator;
    $this->view->totalUsers = $paginator->getTotalItemCount();
    $this->view->userCount = $paginator->getCurrentItemCount();
    return true;
  }
}