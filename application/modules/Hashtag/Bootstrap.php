<?php

class Hashtag_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  protected function _bootstrap($resource = null)
  {
    parent::_bootstrap($resource);
    $front = Zend_Controller_Front::getInstance();

    $front->registerPlugin(new Hashtag_Plugin_Core());

  }
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

   $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile(Zend_Registry::get('StaticBaseUrl')
      . 'application/modules/Hashtag/externals/scripts/core.js');
  }

}