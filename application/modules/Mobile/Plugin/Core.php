<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Mobile_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    if (Engine_Api::_()->mobile()->siteMode() !== 'mobile')
    {
      return;
    }

    $module = $request->getModuleName();
    $controller = $request->getControllerName();
		$action = $request->getActionName();

		if ($module == 'hecore' && $controller == 'module' && $action == 'license'){
			return;
		}
		
    if (preg_match('/^admin-/', $controller))
    {
      return;
    }

    // Social DNA
    if ($module == 'socialdna' && $controller == "auth"){
      $module = 'user';
      $request->setModuleName($module);
    }

    // ProfileUrlShortener
	  Zend_Registry::set('pus_redirect', false);

    // Mode Switch
    if (($module == 'mobile' && $controller == 'index' && $action == 'mode-switch')
        || ($module == 'touch' && $controller == 'index' && $action == 'touch-mode-switch')){
      return ;
    }

    // DashBoard
    if ($module == 'touch' && $controller == 'index' && $action == 'index'){
      $request->setModuleName('mobile');
    }

    $redirect_success = true;
    if ($module != 'checkin' && $module != 'touch' && $module != 'mobile' && $module != 'video' && $module != 'wall'&& $module != 'music'&& $module != 'forum') {
      $redirect_success = Engine_Api::_()->mobile()->redirectController($module);
      if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('mobile.integrations.only', false) && false === $redirect_success)
      {
        $request->setModuleName('mobile');
        $request->setControllerName('Error');
        $request->setActionName('notfound');
      }
    }

    Engine_Api::_()->mobile()->setLayout();
    if(!$redirect_success)
      $request->setParam('not_mobile_integrated', true);
  }

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
    if (Engine_Api::_()->mobile()->siteMode() !== 'mobile'){
      return ;
    }

    // Social DNA
    if ($request->getModuleName() == 'socialdna' && $request->getControllerName() == "auth"){
      $request->setModuleName('user');
    }

		if ($request->getControllerName() == 'error' && ($request->getModuleName() == 'core' || $request->getModuleName() == 'touch')){
      $request->setModuleName('mobile');
		}
	}
}