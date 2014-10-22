<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2010-07-02 19:27 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('rate_admin_main', array(), 'rate_admin_main_settings');

    $this->view->form = $form = new Rate_Form_Admin_Settings();
    $form->getDecorator('description')->setOption('escape', false);

    if ($this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
        // Check license
      $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
      $product_result = $hecoreApi->checkProduct('rate');

      if (isset($product_result['result']) && !$product_result['result']) {
        $form->addError($product_result['message']);
        $this->view->headScript()->appendScript($product_result['script']);

        return;
      }

      $values = $form->getValues();

      $settings = Engine_Api::_()->getApi('settings', 'core');

      foreach ($values as $key => $value) {
        $settings->setSetting($key, $value);
      }
    }
  }
}