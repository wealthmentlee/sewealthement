<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: AdminSettingsController.php 10192 2014-05-01 13:16:24Z lucas $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Blog_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('blog_admin_main', array(), 'blog_admin_main_settings');

    $this->view->form  = $form = new Blog_Form_Admin_Global();
    
    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  }

  public function categoriesAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('blog_admin_main', array(), 'blog_admin_main_categories');

    $this->view->categories = Engine_Api::_()->getItemTable('blog_category')->fetchAll();
  }

  
  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Blog_Form_Admin_Category();
    $form->setAction($this->view->url(array()));
    
    
    
    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    
    
    // Process
    $values = $form->getValues();

    $categoryTable = Engine_Api::_()->getItemTable('blog_category');
    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    $viewer = Engine_Api::_()->user()->getViewer();
    
    try {
      $categoryTable->insert(array(
        'user_id' => $viewer->getIdentity(),
        'category_name' => $values['label'],
      ));

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array('')
    ));
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $category_id = $this->_getParam('id');
    $this->view->blog_id = $this->view->category_id = $category_id;
    $categoriesTable = Engine_Api::_()->getDbtable('categories', 'blog');
    $category = $categoriesTable->find($category_id)->current();
    
    if( !$category ) {
      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('')
      ));
    } else {
      $category_id = $category->getIdentity();
    }
    
    if( !$this->getRequest()->isPost() ) {
      // Output
      $this->renderScript('admin-settings/delete.tpl');
      return;
    }
    
    // Process
    $db = $categoriesTable->getAdapter();
    $db->beginTransaction();
    
    try {
      
      $category->delete();
      
      $blogTable = Engine_Api::_()->getDbtable('blogs', 'blog');
      $blogTable->update(array(
        'category_id' => 0,
      ), array(
        'category_id = ?' => $category_id,
      ));
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    
    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array('')
    ));
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $category_id = $this->_getParam('id');
    $this->view->blog_id = $this->view->category_id = $id;
    $categoriesTable = Engine_Api::_()->getDbtable('categories', 'blog');
    $category = $categoriesTable->find($category_id)->current();
    
    if( !$category ) {
      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('')
      ));
    } else {
      $category_id = $category->getIdentity();
    }
    
    $form = $this->view->form = new Blog_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setField($category);
    
    if( !$this->getRequest()->isPost() ) {
      // Output
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      // Output
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    
    // Process
    $values = $form->getValues();
    
    $db = $categoriesTable->getAdapter();
    $db->beginTransaction();
    
    try {
      $category->category_name = $values['label'];
      $category->save();
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array('')
    ));
  }
}