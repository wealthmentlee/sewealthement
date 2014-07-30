<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallBaseUrl.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_View_Helper_WallBaseUrl extends Zend_View_Helper_Abstract
{
  public function wallBaseUrl()
  {
    if (version_compare(Engine_Api::_()->getDbTable('modules', 'core')->getModule('core')->version, '4.1.8', '>=')){
      return $this->view->layout()->staticBaseUrl;
    } else {
      return $this->view->baseUrl() . '/';
    }

  }

}
