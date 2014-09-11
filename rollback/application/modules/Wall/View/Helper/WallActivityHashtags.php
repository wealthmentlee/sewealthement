<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallActivityCheckins.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_View_Helper_WallActivityHashtags extends Zend_View_Helper_Abstract
{
  public function wallActivityHashtags($actions = array())
  {

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('hashtag')) {
      return array();
    }

    $action_ids = array();
    foreach ($actions as $action) {
      try {
        if (!$action->getTypeInfo()->enabled) {
          continue;
        }

        if (!$action->getSubject() || !$action->getSubject()->getIdentity()) {
          continue;
        }

        if (!$action->getObject() || !$action->getObject()->getIdentity()) {
          continue;
        }

        $action_ids[] = $action->getIdentity();

      } catch (Exception $e) {

      }
    }

    if (count($action_ids) == 0) {
      return array();
    }
  //  $actions
 /*   if(){
      return array();
    }*/
   $actions_id = implode(',', $action_ids);
  $tagTable = Engine_Api::_()->getDbTable('tags', 'hashtag');
    $tTName = $tagTable->info('name');
    // print_die('test');
    $mapsTable = Engine_Api::_()->getDbTable('maps', 'hashtag');
    $mTName = $mapsTable->info('name');

    $select = $tagTable->select()
      ->setIntegrityCheck(false)
      ->from(array('t' => $tTName))
      ->joinLeft(array('m' => $mTName), 't.map_id = m.map_id', array('resource_id'))
      ->where('m.resource_id IN ( ' . $actions_id . ' )');

    $tag_db = $tagTable->fetchAll($select);

    return $tag_db;
  }
}