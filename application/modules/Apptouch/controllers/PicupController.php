<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 16.08.12
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_PicupController
  extends Apptouch_Controller_Action_Bridge
{
  public function indexAction()
  {

//    if (!$this->navigator()->isPicup())
//      return $this->_helper->viewRenderer->setNoRender(true);


    $upload_tmp_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

    $response = array(
      'start' => 1,
      'input' => array(
        'name' => $this->_getParam('input'),
        'value' => ''
      ),
    );

    foreach ($_FILES as $fileKey => $files) {

      if (!is_array($files["name"]) && !$files['error']) {

        $tmp_name = $files["tmp_name"];
        $name = $files["name"];
        $newFileName = $upload_tmp_dir . DIRECTORY_SEPARATOR . $name;

        $success = move_uploaded_file($tmp_name, $newFileName);

        if ($success){
          $response['input']['value'] = $newFileName;
          if(!$response['input']['name']){
            $response['input']['name'] = $fileKey;
          }
          $response['input']['filename'] = basename($newFileName);
        }
      } else {
        $fnames = array();
        $values = array();
        if(!$response['input']['name']){
          $response['input']['name'] = $fileKey . '[]';
        }
        foreach ($files["error"] as $key => $error) {

          if ($error)
            continue;

          $tmp_name = $files["tmp_name"][$key];
          $name = $files["name"][$key];



          $newFileName = $upload_tmp_dir . DIRECTORY_SEPARATOR . $name;

          $succeed = move_uploaded_file($tmp_name, $upload_tmp_dir . DIRECTORY_SEPARATOR . $name);

          if ($succeed){
            $values[] = $newFileName;
            $fnames[] = basename($newFileName);
          }
        }
        $response['input']['value'] = $values;
        $response['input']['filename'] = $fnames;
      }

    }
    $response['end'] = 1;
    $this->view->clearVars();
    $this->view->response = $response;

  }
}
