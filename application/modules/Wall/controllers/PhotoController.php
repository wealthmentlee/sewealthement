<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PhotoController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_PhotoController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
        // Timeline Page
        $page_id = $this->_getParam('page_id', false);
        if ($page_id)
            $this->view->page_id = $page_id;
        // Timeline Page
    if (!$subject){
      return die;
    }
    if (!Engine_Api::_()->wall()->isPhotoType($subject->getType())){
      return die;
    }

    $authSubject = null;
    if ($subject->getType() == 'pagealbumphoto'){
      $authSubject = $subject->getPage();
    } else {
      $authSubject = $subject;
    }

    if (!$authSubject->authorization()->isAllowed($viewer, 'view')){
      return die;
    }

    $this->view->subject_id = $subject->getIdentity();


    if (method_exists($subject, 'getAlbum')){

      $collection = $subject->getAlbum();
      $collection_key = 'album_id';

    } else if (isset($subject->album_id)){
      
      $collection = $subject->getCollection();
      $collection_key = 'album_id';

    } else if (isset($subject->collection_id)) {
      
      $collection = $subject->getCollection();
      $collection_key = 'collection_id';
      
    } else {
      return ;
    }

    $table = $subject->getTable();

    $matches = $table->info('primary');
    $primary = array_pop($matches);
    
    $is_order = isset($subject->order);

    $select = $table->select()
        ->where($collection_key.' = ?', $collection->getIdentity());

    if ($is_order){
      $select->order('order ASC');
    }
    $select->order(''.$primary.' ASC');


    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(14);

    $page = $this->_getParam('p');

    if (!$page){

      $sort_where = '';
      if ($is_order) {
        $sort_where .= '`order` < '.$subject->order.' OR (`order` = 0 AND '.$primary.' < '.$subject->getIdentity().')';
      } else {
        $sort_where .= '('.$primary.' < '.$subject->getIdentity().')';
      }

      $select = $table->select()
          ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
          ->where( $sort_where )
          ->where($collection_key . ' = ?', $collection->getIdentity());

      if ($is_order){
        $select->order('order ASC');
      }

      $select->order(''.$primary.' ASC');

      $result = (int) $table->getAdapter()->fetchOne($select);
      $page = intval($result/$paginator->getItemCountPerPage())+1;

    }


    $this->view->page = $page;
    $paginator->setCurrentPageNumber($page);


    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer || !$viewer->getIdentity() || !$collection->isOwner($viewer)) {  
		if (isset($subject->view_count)){ 
			$subject->view_count = new Zend_Db_Expr('view_count + 1');
			$subject->save();
		}
    }

    $this->view->canEdit = $canEdit = $collection->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canEdit;
    $this->view->canTag = $canEdit;
    $this->view->canUntagGlobal = $canEdit;
    $this->view->makePhoto = true;
	
	
    if ($subject->getType() == 'album_photo'){

      $this->view->canDelete = $canDelete = $collection->authorization()->isAllowed($viewer, 'delete');
      $this->view->canTag = $canTag = $collection->authorization()->isAllowed($viewer, 'tag');
      $this->view->canUntagGlobal = $canUntag = $collection->isOwner($viewer);
      
    } else if ($subject->getType() == 'pagealbumphoto'){

      $this->view->canTag = false;
      $this->view->canUntagGlobal = false;
      $this->view->makePhoto = false;
      
    }


  }


  public function externalPhotoAction()
  {
    $user = Engine_Api::_()->user()->getViewer();
        // Timeline Page
        $page_id = $this->_getParam('page_id', false);
        $page = false;
        if ($page_id) {
            $page = Engine_Api::_()->getItem('page', $page_id);
        }
        // Timeline Page
    // Get photo
    $photo = Engine_Api::_()->getItemByGuid($this->_getParam('photo'));
        if (!$photo || empty($photo->photo_id)) {
      $this->_forward('requiresubject', 'error', 'core');
      return;
    }



    if (method_exists($photo, 'getAlbum')){

      $collection = $photo->getAlbum();
      $collection_key = 'album_id';

    } else if (isset($photo->album_id)){

      $collection = $photo->getCollection();
      $collection_key = 'album_id';

    } else if (isset($photo->collection_id)) {

      $collection = $photo->getCollection();
      $collection_key = 'collection_id';

    } else {
      return ;
    }

        if (!$collection->authorization()->isAllowed(null, 'view')) {
      $this->_forward('requireauth', 'error', 'core');
      return;
    }


    // Make form
        // Timeline Page
        $params = array();
        if ($page) {
            $params = array(
                'parent_type' => $page->getType(),
                'parent_id' => $page->getIdentity(),
                'user_id' => $user->getIdentity()
            );
            $this->view->form = $form = new Page_Form_Team_ExternalPhoto();
        }
        else {
            $params = array(
                'parent_type' => $user->getType(),
                'parent_id' => $user->getIdentity(),
                'user_id' => $user->getIdentity()
            );
    $this->view->form = $form = new User_Form_Edit_ExternalPhoto();
        }
        // Timeline Page
    $this->view->photo = $photo;

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
        // Timeline Page
    // Process
    $db = $user->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      // Get the owner of the photo
      $photoOwnerId = null;
      if( isset($photo->user_id) ) {
        $photoOwnerId = $photo->user_id;
      } else if( isset($photo->owner_id) && (!isset($photo->owner_type) || $photo->owner_type == 'user') ) {
        $photoOwnerId = $photo->owner_id;
      }

      // if it is from your own profile album do not make copies of the image
      if( $photo instanceof Album_Model_Photo &&
          ($photoParent = $photo->getParent()) instanceof Album_Model_Album &&
          $photoParent->owner_id == $photoOwnerId &&
                $photoParent->type == 'profile'
            ) {

        // ensure thumb.icon and thumb.profile exist
        $newStorageFile = Engine_Api::_()->getItem('storage_file', $photo->file_id);
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        if( $photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.profile') ) {
          try {
            $tmpFile = $newStorageFile->temporary();
            $image = Engine_Image::factory();
            $image->open($tmpFile)
              ->resize(200, 400)
              ->write($tmpFile)
              ->destroy();
                        $params['name'] = basename($tmpFile);
                        $iProfile = $filesTable->createFile($tmpFile, $params);
            $newStorageFile->bridge($iProfile, 'thumb.profile');
            @unlink($tmpFile);
                    } catch (Exception $e) {
                        echo $e;
                        die();
                    }
        }
        if( $photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.icon') ) {
          try {
            $tmpFile = $newStorageFile->temporary();
            $image = Engine_Image::factory();
            $image->open($tmpFile);
            $size = min($image->height, $image->width);
            $x = ($image->width - $size) / 2;
            $y = ($image->height - $size) / 2;
            $image->resample($x, $y, $size, $size, 48, 48)
              ->write($tmpFile)
              ->destroy();
                        $params['name'] = basename($tmpFile);
                        $iSquare = $filesTable->createFile($tmpFile, $params);
            $newStorageFile->bridge($iSquare, 'thumb.icon');
            @unlink($tmpFile);
                    } catch (Exception $e) {
                        echo $e;
                        die();
                    }
        }

                // Timeline Page
        // Set it
                if ($page) {
                    $page->photo_id = $photo->file_id;
                    $page->save();
                } else {
        $user->photo_id = $photo->file_id;
        $user->save();
        // Insert activity
        // @todo maybe it should read "changed their profile photo" ?
        $action = Engine_Api::_()->getDbtable('actions', 'activity')
            ->addActivity($user, $user, 'profile_photo_update',
                '{item:$subject} changed their profile photo.');
        if( $action ) {
          // We have to attach the user himself w/o album plugin
          Engine_Api::_()->getDbtable('actions', 'activity')
              ->attachActivity($action, $photo);
        }
      }
                // Timeline Page
            }

      // Otherwise copy to the profile album
      else {
                // Timeline Page
                if ($page) {
                    //$page->setPhoto($photo);
                    $page->photo_id = $photo->file_id;
                    $page->save();
                } else {
        $user->setPhoto($photo);
        // Insert activity
        $action = Engine_Api::_()->getDbtable('actions', 'activity')
            ->addActivity($user, $user, 'profile_photo_update',
                '{item:$subject} added a new profile photo.');

        // Hooks to enable albums to work
        $newStorageFile = Engine_Api::_()->getItem('storage_file', $user->photo_id);
        $event = Engine_Hooks_Dispatcher::_()
          ->callEvent('onUserProfilePhotoUpload', array(
              'user' => $user,
              'file' => $newStorageFile,
            ));

        $attachment = $event->getResponse();
        if( !$attachment ) {
          $attachment = $newStorageFile;
        }

        if( $action  ) {
          // We have to attach the user himself w/o album plugin
          Engine_Api::_()->getDbtable('actions', 'activity')
              ->attachActivity($action, $attachment);
        }
      }
                // Timeline Page
            }

      $db->commit();
    }

    // Otherwise it's probably a problem with the database or the storage system (just throw it)
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
        // Timeline Page

    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Set as profile photo')),
      'smoothboxClose' => true,
    ));
  }


}