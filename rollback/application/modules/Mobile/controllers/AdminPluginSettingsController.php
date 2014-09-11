<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminPluginSettingsController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_AdminPluginSettingsController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mobile_admin_main', array(), 'mobile_admin_main_plugin_settings');

    $this->view->form = $form = new Mobile_Form_Settings();

    $setting_api = Engine_Api::_()->getApi('settings', 'core');

    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('mobile');

    if (isset($product_result['result']) && !$product_result['result']) {
      $form->addError($product_result['message']);
      $this->view->headScript()->appendScript($product_result['script']);

      return;
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())){

      $values = $form->getValues();
      $setting_api->setSetting('mobile.show.rate-browse', $values['mobile_show_rate_browse']);
      $setting_api->setSetting('mobile.show.rate-widget', $values['mobile_show_rate_widget']);

      $form->addNotice('MOBILE_Your changes have been saved.');

    }

    $form->mobile_show_rate_browse
        ->setValue($setting_api->getSetting('mobile.show.rate-browse', 1));
    $form->mobile_show_rate_widget
        ->setValue($setting_api->getSetting('mobile.show.rate-widget', 1));


  }

}