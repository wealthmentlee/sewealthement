<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Core_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    if( Engine_Api::_()->user()->getViewer()->getIdentity() )
    {
      return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }
		
		// check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_portal;
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

		$content = Engine_Content::getInstance();
		$table = Engine_Api::_()->getDbtable('pages', 'mobile');
		$content->setStorage($table);
		$this->_helper->content->setContent($content);

    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
  }
}