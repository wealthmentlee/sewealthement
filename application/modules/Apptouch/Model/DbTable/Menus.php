<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_Model_DbTable_Menus extends Engine_Db_Table
{
//  protected $_name = 'touch_menus';
  public function getEnabledMenus()
  {
    /**
     * @var $modulesTable Core_Model_DbTable_Modules
     */
    $modulesTable = Engine_Api::_()->getDbTable('modules', 'core');
    $moduleSelect = $modulesTable->select()
      ->from(array('modules' => $modulesTable->info('name')), array('name'))
      ->where('modules.enabled = 1')
    ;
    $menusSelect = $this->select()
      ->where('module IN ?', $moduleSelect)
      ;

    return $this->fetchAll($menusSelect);
  }
}