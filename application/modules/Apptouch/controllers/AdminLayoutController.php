<?php
  /**
   * SocialEngine
   *
   * @category   Application_Extensions
   * @package    Apptouch
   * @copyright  Copyright Hire-Experts LLC
   * @license    http://www.hire-experts.com
   * @version    $Id: AdminLayoutController.php 2012-11-30 11:18:13 ulan t $
   * @author     Ulan T
   */

  /**
   * @category   Application_Extensions
   * @package    Apptouch
   * @copyright  Copyright Hire-Experts LLC
   * @license    http://www.hire-experts.com
   */

class Apptouch_AdminLayoutController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $page = $this->_getParam('page', 'core_index_index');

    $pageTable = Engine_Api::_()->getDbTable('pages', 'apptouch');
    $pageSelect = $pageTable->select();


    // Get current page
    $this->view->pageObject = $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('page_id = ?', $page));
    if( null === $pageObject ) {
      $page = 'core_index_index';
      $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page));
      if( null === $pageObject ) {
        throw new Engine_Exception('Home page is missing');
      }
    }

    $this->view->page = $page;
    $this->view->pageObject = $pageObject;
    $this->view->allPages = $allPages = $pageTable->fetchAll($pageSelect);

    $this->view->form = $form = new Apptouch_Form_Admin_Layout($pageObject);

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    /**
     * @var $contentTable Apptouch_Model_DbTable_Content
     */
    $contentTable = Engine_Api::_()->getDbTable('content', 'apptouch');

    $values = $form->getValues();
    unset($values['submit']);

    foreach( $values as $value) {
      $id = ($value < 0) ? -$value : $value;
      $enabled = ($value < 0) ? 0 : 1;
      $contentTable->update(array('enabled' => $enabled), 'content_id ='. $id);
    }
  }
}