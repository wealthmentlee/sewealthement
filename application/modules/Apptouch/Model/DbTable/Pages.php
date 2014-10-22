<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pages.php 2012-12-03 11:18:13 ulan t $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_Model_DbTable_Pages extends Engine_Db_Table
{
  protected $_rowClass = 'Apptouch_Model_Page';

  public function getPages()
  {
    $select = $this->select()->from($this, array('page_id', 'displayname'));
    return $this->fetchAll($select);
  }
}