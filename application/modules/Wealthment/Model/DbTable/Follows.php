<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Users.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Wealthment_Model_DbTable_Follows extends Engine_Db_Table
{
  protected $_rowClass = 'Wealthment_Model_Follow';
  
  public function getActionsOfFollower($user) {
      $select = $this->select()->where('follower_id = ?',$user->getIdentity());
      $data = $this->fetchAll($select);
      foreach($data as $d){
          $ids[] = $d->action_id;
      }
      return $ids;
  }
}