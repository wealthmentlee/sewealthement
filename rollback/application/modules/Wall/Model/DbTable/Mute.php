<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Mute.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_DbTable_Mute extends Engine_Db_Table
{


  public function getActionIds($user)
  {
    $select = $this->select()
        ->where('user_id = ?', $user->getIdentity());

    $action_ids = array();

    foreach ($this->fetchAll($select) as $item){
      $action_ids[] = $item->action_id;
    }

    return $action_ids;

  }


}