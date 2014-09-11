<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
	public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();
  }

  public function _bootstrap()
  {
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Mobile_Plugin_Core, 200);
  }
}