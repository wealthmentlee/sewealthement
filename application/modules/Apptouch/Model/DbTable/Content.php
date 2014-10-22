<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Content.php 2012-12-03 11:18:13 ulan t $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_Model_DbTable_Content extends Engine_Db_Table
{
  public function getContent($page_id)
  {
    $select = $this->select()
      ->where('page_id = ?', $page_id);

    return $this->fetchAll($select);
  }
}