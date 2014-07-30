<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: BlockController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class User_BlockController extends Core_Controller_Action_User
{
  public function init()
  {
    $this->_helper->requireUser();
  }
  
  public function addAction()
  {
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Block_Add();
		$hidden = new Engine_Form_Element_Hidden('return_url');
		$hidden->setValue($this->_getParam('return_url'));
		$form->addElement($hidden);
		$cancel = $form->getElement('cancel');
		$cancel->setOptions(array('link'=>true, 'href'=>urldecode($this->_getParam('return_url')), 'onclick'=>''));
		$form->removeElement('cancel');
		$group = $form->getDisplayGroup('buttons');
		$group->addElement($cancel);

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('block', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      
      $viewer->addBlock($user);
      if($user->membership()->isMember($viewer, null))$user->membership()->removeMember($viewer);

      $db->commit();

      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Member blocked');
      
      $this->_forward('success', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Member blocked')),
				'return_url' => $this->_getParam('return_url'),
      ));
    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function removeAction()
  {
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Block_Remove();
		$hidden = new Engine_Form_Element_Hidden('return_url');
		$hidden->setValue($this->_getParam('return_url'));
		$form->addElement($hidden);

		$cancel = $form->getElement('cancel');
		$cancel->setOptions(array('link'=>true, 'href'=>urldecode($this->_getParam('return_url')), 'onclick'=>''));
		$form->removeElement('cancel');
		$group = $form->getDisplayGroup('buttons');
		$group->addElement($cancel);
		
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('block', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);

      $viewer->removeBlock($user);

      $db->commit();

      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Member unblocked');

      $this->_forward('success', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Member unblocked')),
				'return_url' => $this->_getParam('return_url'),
      ));
    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }
  
  public function successAction()
  {
    // This is a smoothbox
    $this->_helper->layout->setLayout('default-simple');
    $this->view->messages = $this->_getParam('messages', array());
  }
}