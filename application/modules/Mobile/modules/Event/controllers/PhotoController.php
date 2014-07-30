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
    
class Event_PhotoController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('event_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($event_id = (int) $this->_getParam('event_id')) &&
          null !== ($event = Engine_Api::_()->getItem('event', $event_id)) )
      {
        Engine_Api::_()->core()->setSubject($event);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
      'upload',
      'upload-photo', // Not sure if this is the right
      'edit',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'list' => 'event',
      'view' => 'event_photo',
      'edit' => 'event_photo',
    ));
  }

  public function listAction()
  {
    $this->view->event = $event = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $event->getSingletonAlbum();

    if( !$this->_helper->requireAuth()->setAuthParams($event, null, 'view')->isValid() ) {
      return;
    }

    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->canUpload = $event->authorization()->isAllowed(null, 'photo');
  }
  
  public function viewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->event = $event = $photo->getEvent();
    $this->view->canEdit = $photo->authorization()->isAllowed(null, 'edit');

    if( !$this->_helper->requireAuth()->setAuthParams($event, null, 'view')->isValid() ) {
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
    $event = $photo->getParent('event');

    if( !$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid() ) {
      return;
    }

    $this->view->form = $form = new Event_Form_Photo_Delete();
    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => $photo->getHref(),
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
    $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
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
      'return_url'=>$event->getHref(),
    ));
  }
}