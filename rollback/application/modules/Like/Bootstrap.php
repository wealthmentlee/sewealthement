<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile(Zend_Registry::get('StaticBaseUrl')
      . 'application/modules/Like/externals/scripts/remote.js');
    $headScript->appendFile(Zend_Registry::get('StaticBaseUrl')
      . 'application/modules/Like/externals/scripts/core.js');
  }
  public  function _bootstrap($resource=null)
  {
    parent::_bootstrap($resource);
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Like_Plugin_Core(), 237);
  }
}