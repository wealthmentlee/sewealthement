<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Page_PageController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$page_id = $this->_getParam('page_id');
		$this->view->page = $page = Engine_Api::_()->getItem('page', $page_id);

		if ($page == null) {
			$this->_redirectCustom(array('route' => 'page_browse'));			
			return ;
		}
		
		if( !$this->_helper->requireUser()->isValid() || !$page->isTeamMember()) {
			$this->_redirectCustom(array('route' => 'page_browse'));
  		return ;
  	}
	}
	
  public function deleteAction()
  {
  	$page_id = $this->_getParam('page_id');
 		$page = $this->view->page;
 		
  	$this->view->form = $form = new Page_Form_Delete();
    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }
  	
  	$form->setAction($this->view->url(array('action' => 'delete', 'page_id' => $page_id), 'page_team'));
  	$description = sprintf(Zend_Registry::get('Zend_Translate')
  	  ->_('PAGE_DELETE_DESC'), $this->view->htmlLink($page->getHref(), $page->getTitle()));
  	  
  	$form->setDescription($description);
  	
  	if (!$this->getRequest()->isPost()) {
  		return;
  	}
  	
  	$db = Engine_Api::_()->getDbtable('pages', 'page')->getAdapter();
    $db->beginTransaction();
    
    try {
    	$page->delete();
    	$db->commit();
    } catch (Exception $e) {
    	$db->rollBack();
    	throw $e;
    }
    
    $this->_redirectCustom(array('route' => 'page_manage'));
  }
    
	public function postNoteAction()
	{
    $page_id = $this->_getParam('page_id');

    $this->view->form = $form = new Engine_Form;
    $form
        ->addElement('Textarea', 'note', array('label' => 'MOBILE_PAGE_NOTE'))
        ->addElement('Hidden', 'page_id', array('value' => $page_id))
        ->addElement('Button', 'submit', array(
          'label' => 'MOBILE_PAGE_POST_SUBMIT',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array('ViewHelper')
        ))
        ->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'decorators' => array(
            'ViewHelper'
          ),
        ));

    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $form->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');

    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    $page = $this->view->page;

    $form->note->setValue($page->note);

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }

		$page->note = trim(Engine_String::strip_tags($form->getValue('note')));
		$page->save();

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('MOBILE_PAGE_NOTE_SUCCESS')),
      'return_url'=>urldecode($this->_getParam('return_url')),
    ));

	}
	
}