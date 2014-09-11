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
    
class Mobile_IndexController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
    if (Engine_Api::_()->mobile()->siteMode() !== 'mobile'){
      $this->_redirect($this->view->url(array(), 'default', true), array('prependBase' => false));
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
	
  public function modeSwitchAction()
  {
		$mode = $this->_getParam('mode');

		if ($mode === 'touch' || $mode === 'mobile' || $mode === 'standard')
		{
			$session = new Zend_Session_Namespace('standard-mobile-mode');
			$session->__set('mode', $mode);
		}
		
		$return_url = urldecode($this->_getParam('return_url'));
		$this->_redirect($return_url, array('prependBase'=>0));
  }
}