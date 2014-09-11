<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PhotoController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Group_PhotoController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('group_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($group_id = (int) $this->_getParam('group_id')) &&
          null !== ($group = Engine_Api::_()->getItem('group', $group_id)) )
      {
        Engine_Api::_()->core()->setSubject($group);
      }
    }
    
    $this->_helper->requireUser->addActionRequires(array(
      'upload',
      'upload-photo', // Not sure if this is the right
      'edit',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'list' => 'group',
      'upload' => 'group',
      'view' => 'group_photo',
      'edit' => 'group_photo',
    ));
  }

  public function listAction()
  {
    $this->view->group = $group = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $group->getSingletonAlbum();

    if( !$this->_helper->requireAuth()->setAuthParams($group, null, 'view')->isValid() ) {
      return;
    }

    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->canUpload = $group->authorization()->isAllowed(null, 'photo');
  }
  
  public function viewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->group = $group = $photo->getGroup();
    $this->view->canEdit = $photo->canEdit(Engine_Api::_()->user()->getViewer());

    if( !$this->_helper->requireAuth()->setAuthParams($group, null, 'view')->isValid() ) {
      return;
    }

    if( !$viewer || !$viewer->getIdentity() || $photo->user_id != $viewer->getIdentity() ) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }
  }


  public function deleteAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();
    $group = $photo->getParent('group');
    if( !$this->_helper->requireAuth()->setAuthParams($group, null, 'photo.edit')->isValid() ) {
      return;
    }

    $this->view->form = $form = new Group_Form_Photo_Delete();
    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'group')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->delete();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted')),
      'return_url'=>urldecode($group->getHref()),
    ));

  }
}