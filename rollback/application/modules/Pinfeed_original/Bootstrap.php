<?php

class Pinfeed_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  protected function _bootstrap($resource = null)
  {
    parent::_bootstrap($resource);
    $front = Zend_Controller_Front::getInstance();

    $front->registerPlugin(new Pinfeed_Plugin_Core());

  }

}