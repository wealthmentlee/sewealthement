<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bolot
 * Date: 03.05.13
 * Time: 12:52
 * To change this template use File | Settings | File Templates.
 */

class Hashtag_AdminIndexController  extends Core_Controller_Action_Admin
{
  public function indexAction()
  {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->form = $form = new Hashtag_Form_Admin_Settings_Date();

    if ($this->_request->isPost()) {

      // Check license
      $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
      $product_result = $hecoreApi->checkProduct('hashtag');

      if (isset($product_result['result']) && !$product_result['result']) {
        $form->addError($product_result['message']);
        $this->view->headScript()->appendScript($product_result['script']);

        return;
      }
      $form->isValid($this->_getAllParams());
      $values = $form->getValues();
      if (preg_match("|^[\d]*$|", $values['period']) && preg_match("|^[\d]*$|", $values['count'])) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('hashtag.count', $values['count']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('hashtag.period', $values['period']);
        $form->addNotice('Your changes have been saved.');
      }else{
        return $form->addError('ERROR_HASHTAG_SETTINGS_SAVE');;
      }
    }

    $form->period->setValue($settings->getSetting('hashtag.period', 5));
    $form->count->setValue($settings->getSetting('hashtag.count', 5));



  }
  /*public function countAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('hashtag_admin_main');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->form = $form = new Hashtag_Form_Admin_Settings_Count();
    $form->count->setValue($settings->getSetting('hashtag.count', 1));
    if ($this->_request->isPost()) {
      $form->isValid($this->_getAllParams());
      $values = $form->getValues();
      Engine_Api::_()->getApi('settings', 'core')->setSetting('hashtag.count', $values['count']);
    }

  }*/

}