<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Mobile_Plugin_Menus
{

  public function onMenuInitialize_MobileAdminMainPluginSettings($row)
  {
    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate')){
      return $row;
    }
    return false;
  }

}