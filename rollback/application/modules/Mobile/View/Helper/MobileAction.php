<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: MobileAction.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
 
class Mobile_View_Helper_MobileAction extends Zend_View_Helper_Action
{

	public function mobileAction($action, $controller, $module = null, array $params = array())
  {

			$this->resetObjects();
			if (null === $module) {
					$module = $this->defaultModule;
			}

			//Engine_Api::_()->mobile()->redirectController($module);

			// clone the view object to prevent over-writing of view variables
			$viewRendererObj = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			Zend_Controller_Action_HelperBroker::addHelper(clone $viewRendererObj);

			$this->request->setParams($params)
										->setModuleName($module)
										->setControllerName($controller)
										->setActionName($action)
										->setDispatched(true);

			$moduleDir = Engine_Api::_()->mobile()->getPath($module);
			
			if ( is_dir($moduleDir) ) {
				$moduleDir .= DIRECTORY_SEPARATOR . Zend_Controller_Front::getInstance()->getModuleControllerDirectoryName();
				$this->dispatcher->setControllerDirectory($moduleDir, $module);
			} else {
				return false;
			}



			$this->dispatcher->dispatch($this->request, $this->response);

			// reset the viewRenderer object to it's original state
			Zend_Controller_Action_HelperBroker::addHelper($viewRendererObj);


			if (!$this->request->isDispatched()
					|| $this->response->isRedirect())
			{
					// forwards and redirects render nothing
					return '';
			}

			$return = $this->response->getBody();
			$this->resetObjects();
			return $return;
  }
}