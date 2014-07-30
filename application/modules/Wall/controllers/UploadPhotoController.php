<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: UploadPhotoController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_UploadPhotoController extends Core_Controller_Action_Standard
{


  public function indexAction()
  {
    $this->view->form = $form = new Wall_Form_Photo();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()){
      return ;
    }

    if (!$this->getRequest()->isPost()){
      return ;
    }
    if (!$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    if( $form->Filedata->getValue() !== null ) {
      $viewer->setPhoto($form->Filedata);
    }

    $this->view->save = true;


  }

  public function cameraAction()
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

    $viewer->setPhoto($file);

    $this->view->photo_url = $viewer->getPhotoUrl('thumb.profile');
    $this->view->save = true;

  }



}