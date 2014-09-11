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
    

class Pageevent_WidgetController extends Core_Controller_Action_Standard
{
  public function requestAction()
  {
    $path = Engine_Api::_()->mobile()->getScriptPath('pageevent');
    $this->view->addScriptPath($path);

    $this->view->notification = $notification = $this->_getParam('notification');
  }
}