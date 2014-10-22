<?php
/**
 * SocialEngine
 *
 * @category   Application_Apptouch
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Themes.php 2012-12-13 15:13 ulan t $
 * @author     Ulan T
 */

/**
 * @category   Application_Apptouch
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_Model_DbTable_Themes extends Engine_Db_Table
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

  public function getActiveTheme()
  {
    $select = $this->select()->where('active = ?', 1);
    return $this->fetchRow($select);
  }

  public function getActiveThemeName()
  {
    $select = $this->select()->where('active = ?', 1);
    $theme = $this->fetchRow($select);
    $name = 'default';
    if($theme && $theme->name)
      $name = $theme->name;
    return $name;
  }
}
