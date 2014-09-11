<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: CameraController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_CameraController extends Core_Controller_Action_Standard
{

  public function uploadAction()
  {
    // We only need to handle POST requests:
    if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
      exit;
    }

    $original = APPLICATION_PATH_TMP . '/'. md5($_SERVER['REMOTE_ADDR'].rand()).'.jpg';

    // The JPEG snapshot is sent as raw input:
    $input = file_get_contents('php://input');

    if(md5($input) == '7d4df9cc423720b7f1f3d672b89362be'){
      // Blank image. We don't need this one.
      exit;
    }

    $result = file_put_contents($original, $input);
    if (!$result) {
      echo '{
        "error"		: 1,
        "message"	: "Failed save the image. Make sure you chmod the uploads folder and its subfolders to 777."
      }';
      exit;
    }

    $info = getimagesize($original);
    if($info['mime'] != 'image/jpeg'){
      @unlink($original);
      exit;
    }

    $storage = Engine_Api::_()->getItemTable('storage_file');
    $file = $storage->createTemporaryFile($original);
    @unlink($original);


    $viewer = Engine_Api::_()->user()->getViewer();

    if ($this->_getParam('m') == 'advalbum'){
      $table = Engine_Api::_()->getDbtable('albums', 'advalbum');
      $api_album = Engine_Api::_()->advalbum();
    } else {
      $table = Engine_Api::_()->getDbtable('albums', 'album');
      $api_album = Engine_Api::_()->album();
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $type = $this->_getParam('type', 'wall');

      if (empty($type)) $type = 'wall';

      $album = $table->getSpecialAlbum($viewer, $type);
	  

	  $params = array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity()
      );
	  
	  
	  
	  if (method_exists($api_album, 'createPhoto')){ 
	  
		$photo = $api_album->createPhoto($params, array(
	      'name' => $file->name,
          'tmp_name' => $file->temporary()
		));
		
	  
	  } else {
		  $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
		  $photo = $photoTable->createRow();
		  $photo->setFromArray($params);
		  $photo->save();
		  $photo->setPhoto($file);
	  }
	  


      // delete old file
      $file->delete();

      if( $type == 'message' ) {
        $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
      }

	  if (isset($photo->album_id)){
		$photo->album_id = $album->album_id;
	  }
	  if (isset($photo->collection_id)){
		$photo->collection_id = $album->album_id;
	  }
      $photo->save();
	  
	  

      if( !$album->photo_id ) {
        $album->photo_id = $photo->getIdentity();
        $album->save();
      }

      if( $type != 'message' ) {
        // Authorizations
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view',    true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
      }

      $db->commit();

      $this->view->status = true;
      $this->view->photo_id = $photo->photo_id;
      $this->view->album_id = $album->album_id;
      $this->view->src = $photo->getPhotoUrl();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Photo saved successfully');
      
    }

    catch( Exception $e )
    {
      $db->rollBack();
      //throw $e;
      $this->view->status = false;
    }





  }


  public function composeUploadAction()
  {
    if( !Engine_Api::_()->user()->getViewer()->getIdentity() )
    {
      $this->_redirect('login');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    if( empty($_FILES['Filedata']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Get album
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('albums', 'album');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $type = $this->_getParam('type', 'wall');

      if (empty($type)) $type = 'wall';

      $album = $table->getSpecialAlbum($viewer, $type);

      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
        'owner_type' => 'user',
        'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
      ));
      $photo->save();
      $photo->setPhoto($_FILES['Filedata']);

      if( $type == 'message' ) {
        $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
      }

      $photo->album_id = $album->album_id;
      $photo->save();

      if( !$album->photo_id ) {
        $album->photo_id = $photo->getIdentity();
        $album->save();
      }

      if( $type != 'message' ) {
        // Authorizations
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view',    true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
      }

      $db->commit();

      $this->view->status = true;
      $this->view->photo_id = $photo->photo_id;
      $this->view->album_id = $album->album_id;
      $this->view->src = $photo->getPhotoUrl();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Photo saved successfully');
    }

    catch( Exception $e )
    {
      $db->rollBack();
      //throw $e;
      $this->view->status = false;
    }
  }

}