<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WidgetController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Group_WidgetController extends Core_Controller_Action_Standard
{
  public function requestGroupAction()
  {
    $path = Engine_Api::_()->mobile()->getScriptPath('group');
    $this->view->addScriptPath($path);


    $this->view->notification = $notification = $this->_getParam('notification');
  }
}