<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchScript.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_View_Helper_ApptouchScript extends Zend_View_Helper_Abstract
{
  public function apptouchScript($path)
  {
    $script = '<script data-cfasync="false" type="text/javascript" src="' . $path . '"></script>';

    return $script;
  }
}