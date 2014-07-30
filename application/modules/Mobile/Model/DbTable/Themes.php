<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Themes.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Model_DbTable_Themes extends Engine_Db_Table
{

  /**
   * Deletes all temporary files in the Scaffold cache
   *
   * @example self::clearScaffoldCache();
   * @return void
   */
  public static function clearScaffoldCache()
  {
    try {
      Engine_Package_Utilities::fsRmdirRecursive(APPLICATION_PATH . '/temporary/scaffold', false);
    } catch( Exception $e ) {}
  }
}
