<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Page.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Type_Page extends Wall_Plugin_Type_Abstract
{

  public function getItems(User_Model_User $user)
  {
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $membership = Engine_Api::_()->getDbTable('membership', 'page');
    $like = Engine_Api::_()->getDbTable('likes', 'core');

    $type = "page";
    
    $matches = $table->info('primary');
    $primary = array_shift($matches);

    $select = $table->select()
        ->setIntegrityCheck(false)
        ->from(array('t' => $table->info('name')), new Zend_Db_Expr("'$type' AS `type` , t.$primary AS id"))
        ->joinLeft(array('m' => $membership->info('name')), "m.resource_id = t.$primary AND m.user_id = {$user->getIdentity()}", array())
        ->joinLeft(array('l' => $like->info('name')), "l.resource_type = '$type' AND l.resource_id = t.$primary AND l.poster_type = 'user' AND l.poster_id = {$user->getIdentity()}", array())
        ->where('NOT ISNULL(m.resource_id) OR NOT ISNULL(l.resource_id)');

    return $table->fetchAll($select)->toArray();

  }


}