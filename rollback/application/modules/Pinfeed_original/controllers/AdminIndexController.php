<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bolot
 * Date: 03.05.13
 * Time: 12:52
 * To change this template use File | Settings | File Templates.
 */

class Pinfeed_AdminIndexController  extends Core_Controller_Action_Admin
{
  public function indexAction()
  {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->form = $form = new Pinfeed_Form_Admin_Settings_Global();

    if ($this->_request->isPost()) {
      $form->isValid($this->_getAllParams());
      $values = $form->getValues();
      if (preg_match("|^[\d]*$|", $values['period']) && preg_match("|^[\d]*$|", $values['usage'])) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('Pinfeed.use_homepage', $values['usage']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('Pinfeed.width', $values['width']);
        $form->addNotice('Your changes have been saved.');
      }else{
        return $form->addError('Check your form and try again');
      }
    }

    $form->usage->setValue($settings->getSetting('Pinfeed.use_homepage', 0));
    $form->width->setValue($settings->getSetting('Pinfeed.width', 0));



  }

}