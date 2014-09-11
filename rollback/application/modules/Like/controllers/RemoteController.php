<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: RemoteController.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_RemoteController extends Core_Controller_Action_Standard
{
  
	public function init() 
	{
    $this->_helper->layout->disableLayout();
    $this->view->error = 0;
    $this->view->html = '';

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->subject = $subject = Engine_Api::_()->getItem((string)$this->_getParam('object'), (int)$this->_getParam('object_id'));
    
    if (!$viewer->getIdentity()){
      $this->view->error =  3;
      $this->view->html =  Zend_Registry::get('Zend_Translate')->_('like_LIKE_LOGIN_ERROR');
      return ;
    }

		if (!$this->requireAuth()) {
			$this->view->error =  4;
      $this->view->html =  Zend_Registry::get('Zend_Translate')->_('LIKE_AUTH_ERROR');
			return ;
		}

    $this->view->viewer_url = $viewer->getHref();
	}

	public function requireAuth()
	{
		$subject = $this->view->subject;
		return (Engine_Api::_()->like()->isAllowed($subject));
	}
	
	public function likeAction() 
	{
		$api = Engine_Api::_()->like();
    if ($this->view->error){
      return ;
    }
    
    $subject = $this->view->subject;
    $viewer = $this->view->viewer;

		if ($api->like($subject)){
      $action = $api->addAction($subject, $viewer);
		}else{
			$this->view->error = 5;
			$this->view->html = Zend_Registry::get('Zend_Translate')->_('like_LIKE_UNDEFINED_ERROR');
		}

    $this->view->viewer = $viewer->toArray();
    $this->view->subject = $subject->toArray();
	}
	
	public function unlikeAction() 
	{
		$api = Engine_Api::_()->like();
    if ($this->view->error){
      return ;
    }
    
    $subject = $this->view->subject;
    $viewer = $this->view->viewer;
    
		if ($api->unlike($subject)){
      $api->deleteAction($subject, $viewer);
		}else{
			$this->view->error = 5;
			$this->view->html = Zend_Registry::get('Zend_Translate')->_('like_UNLIKE_UNDEFINED_ERROR');
		}

    $this->view->viewer = $viewer->toArray();
    $this->view->subject = $subject->toArray();
	}

}