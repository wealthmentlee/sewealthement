<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: GroupController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Group_GroupController extends Core_Controller_Action_Standard
{

  public function init()
  {
    if( 0 !== ($group_id = (int) $this->_getParam('group_id')) &&
        null !== ($group = Engine_Api::_()->getItem('group', $group_id)) ) {
      Engine_Api::_()->core()->setSubject($group);
    }

    $this->_helper->requireUser();
    $this->_helper->requireSubject('group');
  }

  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $group = Engine_Api::_()->getItem('group', $this->getRequest()->getParam('group_id'));
    if( !$this->_helper->requireAuth()->setAuthParams($group, null, 'delete')->isValid()) return;

    // Make form
    $this->view->form = $form = new Group_Form_Delete();
		$element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    if( !$group )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Group doesn't exists or not authorized to delete");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $group->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      //$group->delete();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected group has been deleted.');

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array($this->view->message),
      'return_url'=>urldecode($this->_getParam('return_url')),
    ));

  }

}