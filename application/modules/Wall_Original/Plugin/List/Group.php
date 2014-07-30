<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Group.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_List_Group extends Wall_Plugin_List_Abstract
{
  public function getSelect(User_Model_User $user, array $params = array())
  {
    $db = Engine_Db_Table::getDefaultAdapter();

    $table = Engine_Api::_()->getDbTable('groups', 'group');
    $like = Engine_Api::_()->getDbTable('likes', 'core');
    $membership = Engine_Api::_()->getDbTable('membership', 'group');

    $user_id = $user->getIdentity();

    $select = $db->select()
        ->from(array('g' => $table->info('name')), new Zend_Db_Expr("'group' AS `type`, g.group_id AS id"))
        ->joinLeft(array('l' => $like->info('name')), "l.resource_type = 'group' AND l.resource_id = g.group_id AND l.poster_type = 'group' AND l.poster_id = $user_id", array())
        ->joinLeft(array('m' => $membership->info('name')), "m.resource_id = g.group_id AND m.user_id = $user_id AND m.active = 1", array())
        ->where(new Zend_Db_Expr('NOT ISNULL(l.resource_id) OR NOT ISNULL(m.resource_id)'));

    if (!empty($params['search'])){
      $select->where('g.title LIKE ? OR g.description LIKE ?', '%'. $params['search'] .'%');
    }

    return $select;

  }


}