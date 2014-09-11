<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminLevelController.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_AdminLevelController extends Core_Controller_Action_Admin
{
	public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('like_admin_main', array(), 'like_admin_main_level');
  }
  
  public function indexAction() 
  {
  	if( null !== ($id = $this->_getParam('level_id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }
    
  	if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }
    
    $id = $level->level_id;
    
  	$this->view->form = $form = new Like_Form_Admin_Level();
  	$form->level_id->setValue($id);
  	
  	$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
  	if (!$this->getRequest()->isPost()) {

  		$form->populate($permissionsTable->getAllowed('user', $id, array_keys($form->getValues())));
  		return ;
  	}
  	
  	if (!$form->isValid($this->getRequest()->getPost())){
  		return ;
 		}
 		
			// Check license
    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('likes');

    if (isset($product_result['result']) && !$product_result['result']) {
      $form->addError($product_result['message']);
      $this->view->headScript()->appendScript($product_result['script']);

      return;
    }
 		
 		$values = $form->getValues();
 		unset($values['level_id']);

 		$db = $permissionsTable->getAdapter();
    $db->beginTransaction();

  	try
    {
      $permissionsTable->setAllowed('user', $id, $values);
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('like_Your changes has been saved.');
  }
  
}