<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Signup.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_Model_DbTable_Signup extends Engine_Db_Table
{
  protected $_serializedColumns = array('admin_route');
	public function getByClassName($class)
	{
		if (!is_string($class)){
			return false;
		}

		$select = $this->select()->where('class = ? ', $class)->limit(1);
		return $this->fetchRow($select);
	}
}
