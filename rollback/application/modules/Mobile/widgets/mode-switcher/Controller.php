<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Widget_ModeSwitcherController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		$this->view->standard = $this->_getParam('standard', 'Standard');
		$this->view->touch = $this->_getParam('touch', 'Touch');
		$this->view->mobile = $this->_getParam('mobile', 'Mobile');

		$this->view->isTouchEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('touch');
  }
}