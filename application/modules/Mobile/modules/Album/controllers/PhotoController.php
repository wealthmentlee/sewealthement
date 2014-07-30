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
    
class Album_PhotoController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid() ) return;
    
    if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
        null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id)) )
    {
      Engine_Api::_()->core()->setSubject($photo);
    }

    /*
    else if( 0 !== ($album_id = (int) $this->_getParam('album_id')) &&
        null !== ($album = Engine_Api::_()->getItem('album', $album_id)) )
    {
      Engine_Api::_()->core()->setSubject($album);
    }
     */
  }
   
  public function viewAction()
  {
    if( !$this->_helper->requireSubject('album_photo')->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();

    if (method_exists($photo, 'getCollection')) {
      $this->view->album = $album = $photo->getCollection();
    } else {
      $this->view->album = $album = $photo->getAlbum();
    }
		
    if( !$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer) ) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }

    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id){
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) $message_view = true;
    }
    $this->view->message_view = $message_view;

    //if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() ) return;
    if(!$message_view && !$this->_helper->requireAuth()->setAuthParams($photo, null, 'view')->isValid() ) return;

    $album_id = isset($photo->collection_id) ? $photo->collection_id : $photo->album_id;

    $checkAlbum = Engine_Api::_()->getItem('album', $this->_getParam('album_id'));
    if( !($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum->getIdentity() || $checkAlbum->album_id != $album_id )
    {
      $this->_forward('requiresubject', 'error', 'core');
      return;
    }

    $this->view->photoIndex = (method_exists($photo, 'getCollectionIndex')) ? $photo->getCollectionIndex() : $photo->getPhotoIndex();
    $this->view->nextPhoto = (method_exists($photo, 'getNextCollectible')) ? $photo->getNextCollectible() : $photo->getNextPhoto();
    $this->view->previousPhoto = (method_exists($photo, 'getPrevCollectible')) ? $photo->getPrevCollectible() : $photo->getPreviousPhoto();

    $this->view->canTag = $canTag = $album->authorization()->isAllowed($viewer, 'tag');
    $this->view->canUntagGlobal = $canUntag = $album->isOwner($viewer);
  }

  public function deleteAction()
  {
    if( !$this->_helper->requireSubject('album_photo')->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'delete')->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->core()->getSubject('album_photo');

    $db = $photo->getTable()->getAdapter();
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
  }
}