<?php

class Advancedsearch_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile(Zend_Registry::get('StaticBaseUrl')
      . 'application/modules/Advancedsearch/externals/scripts/core.js');
  }
}