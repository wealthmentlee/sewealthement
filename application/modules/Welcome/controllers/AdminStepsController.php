<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminManageController.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Welcome_AdminStepsController extends Core_Controller_Action_Admin
{
  public function createAction()
  {
    $this->view->form = $form = new Welcome_Form_Admin_Create();

    if( !$this->getRequest()->isPost() && $this->_getParam( 'slideshow_id', -1 ) != -1 ){
      $form->slideshow_id->setValue( $this->_getParam( 'slideshow_id' ) );
    }

    if ($this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      // Check license
      $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
      $product_result = $hecoreApi->checkProduct('welcome');

      if (isset($product_result['result']) && !$product_result['result']) {
        $form->addError($product_result['message']);
        $this->view->headScript()->appendScript($product_result['script']);

        return;
      }

      $table = Engine_Api::_()->getItemTable('welcome_step');
      $values = $form->getValues();

      $db = $table->getAdapter();
      $db->beginTransaction();

      try
      {
        $row = $table->createRow();
        $row->setFromArray($values);

        if( $form->getValue('slideshow_id') != null ){
          $row->slideshow_id = $form->getValue('slideshow_id');
        }

        $row->save();

        // Add the photo
        if (!empty($values['filedata'])) {
          $fileElement = $form->filedata;
          $row->setPhoto($fileElement);
          $row->save();
        }

        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'controller' => 'slideshow','module' => 'welcome'), 'admin_default', true );
    }
  }

  public function editAction()
  {
    if ( !$this->_helper->requireUser()->isValid() ) return;
    
    $this->view->step = $step = Engine_Api::_()->getItem('welcome_step', $this->_getParam('step'));
    $this->view->form = $form = new Welcome_Form_Admin_Edit();
        
    if (!$this->getRequest()->isPost())
    {
      $form->title->setValue($step->title);
      $form->body->setValue($step->body);
      $form->link->setValue( $step->link );
      $form->step->setValue($step->getIdentity());
      $form->slideshow_id->setValue( $step->slideshow_id );
    }
    
    if ( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      // Check license
      $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
      $product_result = $hecoreApi->checkProduct('welcome');

      if (isset($product_result['result']) && !$product_result['result']) {
        $form->addError($product_result['message']);
        $this->view->headScript()->appendScript($product_result['script']);
        
        return;
      }

      $db = $step->getTable()->getAdapter();
      $db->beginTransaction();
      
      try
      {
        $values = $form->getValues();
        $step->setFromArray($values);

        if( $form->getValue('slideshow_id') != null ){
          $step->slideshow_id = $form->getValue('slideshow_id');
        }

        $step->save();
            
        // Add the photo
        if ( !empty($values['filedata']) ) {
          $fileElement = $form->filedata;
          $step->setPhoto($fileElement);
        }
        
        $db->commit();
      }
      catch ( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Changes have been saved.');
      return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'controller' => 'slideshow', 'module' => 'welcome'), 'admin_default', true);
    }
  }
  
  public function deleteAction()
  {
    if ( !$this->_helper->requireUser()->isValid() ) return;
    
    $step = Engine_Api::_()->getItem('welcome_step', $this->_getParam('step'));

    $db = Engine_Api::_()->getDbtable('steps', 'welcome')->getAdapter();
    $db->beginTransaction();
    
    try
    {
      $step->deleteStep();
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }



    return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'controller' => 'slideshow', 'module' => 'welcome'), 'admin_default', true );
  }
}