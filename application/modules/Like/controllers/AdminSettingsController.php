<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('like_admin_main', array(), 'like_admin_main_settings');
  }
  
  public function indexAction() 
  {
    $this->view->form = $form = new Like_Form_Admin_Global(array('action' => $this->view->url(array('action'=> 'index', 'controller' => 'settings', 'module' => 'like'), 'admin_default')));

    if (!$this->getRequest()->isPost()){
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

    $api = Engine_Api::_()->like();
    $values = $form->getValues();

    $settings = Engine_Api::_()->getApi('settings', 'core');

    foreach($values as $key => $value)
    {
      if($key == 'logo')
      {
        continue;
      }
      $settings->setSetting($key,$value);
    }

    if ($form->logo->getValue()){
      $api->setPhoto($form->logo);
    }
    $form->addNotice('like_Your changes has been saved.');
  }
  
}