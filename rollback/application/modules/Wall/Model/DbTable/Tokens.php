<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Tokens.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Wall_Model_DbTable_Tokens extends Engine_Db_Table
{
  protected $_rowClass = 'Wall_Model_Token';

  public function getUserToken(User_Model_User $user, $provider)
  {
    $select = $this->select()
        ->where('user_id = ?', $user->getIdentity())
        ->where('provider = ?', $provider)
        ->order('creation_date DESC')
        ->limit(1);

    return $this->fetchRow($select);

  }

}