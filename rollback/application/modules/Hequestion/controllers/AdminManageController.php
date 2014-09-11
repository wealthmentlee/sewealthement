<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminManageController.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hequestion_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hequestion_admin_main', array(), 'hequestion_admin_main_manage');

    if ($this->getRequest()->isPost())
    {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key=>$value) {
        if ($key == 'delete_' . $value)
        {
          $hequestion = Engine_Api::_()->getItem('hequestion', $value);
          $hequestion->delete();
        }
      }
    }

    $page = $this->_getParam('page',1);
    $this->view->paginator = Engine_Api::_()->getItemTable('hequestion')->getQuestionsPaginator(array(
    ));
    $this->view->paginator->setItemCountPerPage(25);
    $this->view->paginator->setCurrentPageNumber($page);

  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->hequestion_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $hequestion = Engine_Api::_()->getItem('hequestion', $id);
        $hequestion->delete();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    // Output
    $this->renderScript('admin-manage/delete.tpl');
  }

  public function suggestAction()
  {
    $page = $this->_getParam('page');
    $query = $this->_getParam('query');
    
    $hequestionTable = Engine_Api::_()->getItemTable('hequestion');
    $hequestionSelect = $hequestionTable->select()
      ->where('title LIKE ?', '%' . $query . '%');
    $paginator = Zend_Paginator::factory($hequestionSelect);
    $paginator->setCurrentPageNumber($page);

    $data = array();
    foreach( $paginator as $hequestion ) {
      $data[$hequestion->hequestion_id] = $hequestion->getTitle();
    }
    $this->view->status = true;
    $this->view->data = $data;
  }

  public function infoAction()
  {
    $hequestionIdentity = $this->_getParam('hequestion_id');
    if( !$hequestionIdentity ) {
      $this->view->status = false;
      return;
    }

    $hequestion = Engine_Api::_()->getItem('hequestion', $hequestionIdentity);
    if( !$hequestion ) {
      $this->view->status = false;
      return;
    }

    $this->view->status = true;
    $this->view->identity = $hequestion->getIdentity();
    $this->view->title = $hequestion->getTitle();
    $this->view->description = $hequestion->getDescription();
    $this->view->href = $hequestion->getHref();
  }
}