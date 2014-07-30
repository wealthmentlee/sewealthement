<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: MobileActiveTheme.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_View_Helper_MobileActiveTheme extends Zend_View_Helper_Abstract
{
  public function mobileActiveTheme()
  {
		$table = Engine_Api::_()->getDbtable('themes', 'mobile');

		if (null === ($theme = $table->fetchRow($table->select()->where('active=?', 1)->limit(1))))
		{
			$theme = $table->fetchRow($table->select()->where('name=?', 'default')->limit(1));
		}

		return $theme;
	}
}
