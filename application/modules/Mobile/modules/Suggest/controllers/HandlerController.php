<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: HandlerController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Suggest_HandlerController extends Core_Controller_Action_Standard
{
  public function requestAction()
  {
    $path = Engine_Api::_()->mobile()->getScriptPath('suggest');
    $this->view->addScriptPath($path);

    $this->view->notification = $notification = $this->_getParam('notification');
    $this->view->suggest = $suggest = $notification->getObject();
    $likeEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like');

    $this->view->assign(array(
      'object' => $suggest->getObject(),
      'action' => 'list',
      'thumb' => 'thumb.icon',
      'likeEnabled' => $likeEnabled
    ));
  }
}