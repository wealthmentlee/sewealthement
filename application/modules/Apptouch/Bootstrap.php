<?php

class Apptouch_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->debug();
    $this->initViewHelperPath();
  }

  protected function _bootstrap($resource = null)
  {
    parent::_bootstrap($resource);
    $front = Zend_Controller_Front::getInstance();

    $front->registerPlugin(new Apptouch_Plugin_Core(), 917);

    $file = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "Apptouch" . DIRECTORY_SEPARATOR . "settings" . DIRECTORY_SEPARATOR . "content" . DIRECTORY_SEPARATOR . "map.php";
    if (file_exists($file)) {
      $map = include $file;
      Zend_Registry::set("Apptouch_Content", $map);
    }
    $this->_setupHelpers();
  }

  private function _setupHelpers()
  {
    if (Engine_Api::_()->getApi('core', 'apptouch')->isApptouchMode()) {
      Zend_Controller_Action_HelperBroker::addHelper(new Apptouch_Controller_Action_Helper_Fields());
      Zend_Controller_Action_HelperBroker::addHelper(new Apptouch_Controller_Action_Helper_Components());
      Zend_Controller_Action_HelperBroker::addHelper(new Apptouch_Controller_Action_Helper_Activity());
      Zend_Controller_Action_HelperBroker::addHelper(new Apptouch_Controller_Action_Helper_JsonML());
      Zend_Controller_Action_HelperBroker::addHelper(new Apptouch_Controller_Action_Helper_RequireAuth());
      Zend_Controller_Action_HelperBroker::addHelper(new Apptouch_Controller_Action_Helper_RequireUser());
      Zend_Controller_Action_HelperBroker::addHelper(new Apptouch_Controller_Action_Helper_RequireSubject());
    }
  }
  function debug(){
    error_reporting(E_ALL);
    ini_set('display_errors',true);
    ini_set('html_errors',true);
    ini_set('error_reporting',E_ALL ^ E_NOTICE);
  }
}