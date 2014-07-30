<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ListItems.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_DbTable_ListItems extends Engine_Db_Table
{

  public function getListsPaginator($user_id)
  {
    $select = $this->select()
        ->where('user_id = ?', $user_id);

    return Zend_Paginator::factory($select);

  }

}