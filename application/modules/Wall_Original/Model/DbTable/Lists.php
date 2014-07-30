<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Lists.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_DbTable_Lists extends Engine_Db_Table
{
  protected $_rowClass = 'Wall_Model_List';

  protected $lists;

  public function getPaginator(User_Model_User $user)
  {
    $select = $this->select()
        ->where('user_id = ?', $user->getIdentity());

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(100);

    return $paginator;

  }


  public function getList($identity)
  {
    if (is_null($this->lists[$identity])){
      $this->lists[$identity] = $this->findRow($identity);
    }
    return $this->lists[$identity];
  }

}