<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: CarouselController.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Welcome_AdminSlideshowController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    /* Navigation is removed
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('welcome_admin_main', array(), 'welcome_admin_main_slideshow');
    */

    $page = $this->_getParam('page',1);
    $this->view->paginator = $paginator = Engine_Api::_()->welcome()->getSlideshowsPaginator();

    $this->view->paginator->setItemCountPerPage( 10 );
    $this->view->paginator->setCurrentPageNumber( $page );
  }


  public function createAction()
  {
    $this->view->form = $form = new Welcome_Form_Admin_CreateSlideshow();

    if( !$this->getRequest()->isPost() ) return;

    if( !$form->isValid( $this->getRequest()->getPost() ) ){
      return;
    }

// Check license
    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('welcome');

    if (isset($product_result['result']) && !$product_result['result']) {
      $form->addError($product_result['message']);
      $this->view->headScript()->appendScript($product_result['script']);

      return;
    }
    $table = Engine_Api::_()->getItemTable('welcome_slideshow');

    $db = $table->getAdapter();
    $db->beginTransaction();

    try{
      $row = $table->createRow();
      $values = $form->getValues();
      $row->setFromArray( $values );

      $animation = $form->getValue('animation');

      if( !empty( $animation ) ){
        $row->effect = $animation;
      }

      $row->save();
    }catch( Exception $e ){
      $db->rollBack();
      throw $e;
    }

    $db->commit();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The slideshow has been created.');
    return $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'welcome', 'controller' => 'slideshow' ), 'admin_default', true),
      'messages' => Array( $this->view->message )
    ));
  }

  public function settingsAction()
  {
    $slideshow_id = $this->_getParam( 'slideshow_id' );

    $slideshow = Engine_Api::_()->getItem( 'welcome_slideshow', $slideshow_id );

    $this->view->form = $form = new Welcome_Form_Admin_SlideshowSettings( $slideshow );

    if( !$this->getRequest()->isPost() || !$form->isValid( $this->getRequest()->getPost() ) ){
      return;
    }

// Check license
    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('welcome');

    if (isset($product_result['result']) && !$product_result['result']) {
      $form->addError($product_result['message']);
      $this->view->headScript()->appendScript($product_result['script']);

      return;
    }
    // Save new settings
    $form->saveSlideshowSettings();


    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.');
    return $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'welcome', 'controller' => 'slideshow', 'action' => 'index'), 'admin_default', true),
      'messages' => Array( $this->view->message )
    ));
  }

  public function editAction()
  {
    $this->view->form = $form = new Welcome_Form_Admin_CreateSlideshow();
    $form->setTitle('Edit Slideshow');

    $slideshow = Engine_Api::_()->getItem('welcome_slideshow', $this->_getParam('slideshow_id') );

    if( !$slideshow ){
      return;
    }

    if( !$this->getRequest()->isPost() ){
      $form->populate( $slideshow->toArray() );
      $form->animation->setValue( $slideshow->effect );
      return;
    }

    if( !$form->isValid( $this->getRequest()->getPost() ) ){
      return;
    }


    $table = Engine_Api::_()->getItemTable('welcome_slideshow');

    $db = $table->getAdapter();
    $db->beginTransaction();

    try{
      $values = $form->getValues();
      $slideshow->setFromArray( $values );

      $slideshow->effect = $form->getValue('animation');

      $slideshow->save();
    }catch( Exception $e ){
      $db->rollBack();
      throw $e;
    }

    $db->commit();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.');
    return $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'welcome', 'controller' => 'slideshow', 'action' => 'index'), 'admin_default', true),
      'messages' => Array( $this->view->message )
    ));
  }


  public function deleteAction()
  {
    $this->view->form = $form = new Welcome_Form_Admin_DeleteSlideshow();

    $slideshow = Engine_Api::_()->getItem('welcome_slideshow', $this->_getParam('slideshow_id') );

    if( !$this->getRequest()->isPost() ){
      return;
    }

    $slideshowsTable = Engine_Api::_()->getDbTable('slideshows','welcome');
    $stepsTable = Engine_Api::_()->getDbTable('steps','welcome');

    $select = $stepsTable->select()
      ->where( 'slideshow_id=?', $slideshow->slideshow_id );
    $steps = $stepsTable->fetchAll( $select );

    $db = $slideshowsTable->getAdapter();

    $db->beginTransaction();

    try{
      $slideshow->delete();
      foreach( $steps as $step ){
        $step->slideshow_id = 0;
        $step->save();
      }
    }catch( Exception $e ){
      $db->rollBack();
      throw $e;
    }

    $db->commit();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The slideshow has been deleted.');
    return $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'welcome', 'controller' => 'slideshow', 'action' => 'index'), 'admin_default', true),
      'messages' => Array( $this->view->message )
    ));
  }
}

?>