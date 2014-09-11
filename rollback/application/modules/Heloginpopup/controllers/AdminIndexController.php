<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heloginpopup
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminIndexController.php 24.09.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Heloginpopup
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heloginpopup_AdminIndexController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->form = $form = new Heloginpopup_Form_Admin_Settings();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $values = $form->getValues();

    $settings->setSetting('heloginpopup.max.day', $values['maxday']);

    $form->addNotice('Your changes have been saved.');
  }
}