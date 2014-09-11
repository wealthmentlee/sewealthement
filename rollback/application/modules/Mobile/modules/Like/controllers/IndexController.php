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
    

class Like_IndexController extends Core_Controller_Action_Standard
{

	public function init()
	{
		$this->view->labels = Engine_Api::_()->like()->getSupportedModulesLabels();
		$this->view->icons = Engine_Api::_()->like()->getSupportedModulesIcons();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject($viewer);
    }

    $this->view->nophoto = Engine_Api::_()->like()->getNoPhotos();
	}
		
	public function likeAction() 
	{
		$api = Engine_Api::_()->like();
		$object = $this->_getParam('object', $this->_getParam('object_type', ''));
    $object_id = $this->_getParam('object_id', '');
    $return_url = $this->_getParam('return_url', '');

		$object = Engine_Api::_()->getItem($object, $object_id);
		$viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->view->error =  3;
      $this->view->html =  Zend_Registry::get('Zend_Translate')->_('LIKE_LOGIN_ERROR');
      return $this->_redirect($return_url, array('prependBase'=>0));
    }

    if (in_array($object, array('page', 'group', 'event', 'user'))) {
      if (!$api->isAllowed($object)){
        $this->view->error = 2;
        $this->view->html = Zend_Registry::get('Zend_Translate')->_('LIKE_AUTH_ERROR');
        return $this->_redirect($return_url, array('prependBase'=>0));
      }
    }

		if ($api->isLike($object)) {
			$this->view->error = 4;
      $this->view->html = Zend_Registry::get('Zend_Translate')->_('like_Already liked.');
			return $this->_redirect($return_url, array('prependBase'=>0));
		}

		$this->view->link = 'unlike';
		if ($api->like($object)) {
      $action = $api->addAction($object, $viewer);
		} else {
			$this->view->error = 1;
			$this->view->html = 'Error';
		}

    return $this->_redirect($return_url, array('prependBase'=>0));
	}
	
	public function unlikeAction()
	{
		$api = Engine_Api::_()->like();
		$object = $this->_getParam('object', $this->_getParam('object_type', ''));
    $object_id = $this->_getParam('object_id', '');
    $return_url = $this->_getParam('return_url', '');

		$object = Engine_Api::_()->getItem($object, $object_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if (!$viewer->getIdentity()){
      $this->view->error =  3;
      $this->view->html =  Zend_Registry::get('Zend_Translate')->_('LIKE_LOGIN_ERROR');
			return $this->_redirect($return_url, array('prependBase'=>0));
    }

    if ( in_array($object, array('page', 'group', 'event', 'user'))) {
      if (!$api->isAllowed($object)){
        $this->view->error = 2;
        $this->view->html = Zend_Registry::get('Zend_Translate')->_('LIKE_AUTH_ERROR');
        return $this->_redirect($return_url, array('prependBase'=>0));
      }
    }

		if (!$api->isLike($object)) {
			$this->view->error = 4;
      $this->view->html = Zend_Registry::get('Zend_Translate')->_('like_Already unliked.');
			return $this->_redirect($return_url, array('prependBase'=>0));
		}

		$this->view->link = 'like';
		if ($api->unlike($object)) {
      $action = $api->deleteAction($object, $viewer);
		} else {
			$this->view->error = 1;
			$this->view->html = Zend_Registry::get('Zend_Translate')->_('LIKE_UNDEFINED_ERROR');
		}

    return $this->_redirect($return_url, array('prependBase'=>0));
	}

}