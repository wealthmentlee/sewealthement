<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: MobileSubstr.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
    
class Mobile_View_Helper_MobileSubstr extends Zend_View_Helper_Abstract
{
	public function mobileSubstr($str, $count = 50)
	{
		$count_tmp = (int) ($count - 1);
		return Engine_String::substr($str, 0, $count) . ((Engine_String::strlen($str) > $count_tmp)? '...':'');
	}
}
