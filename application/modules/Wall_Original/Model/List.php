<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: List.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_List extends Core_Model_Item_Abstract
{

  public function addItem(Core_Model_Item_Abstract $object)
  {
    $tableItem = Engine_Api::_()->getDbTable('listItems', 'wall');

    $item = $tableItem->createRow();
    $item->object_type = $object->getType();
    $item->object_id = $object->getIdentity();
    $item->list_id = $this->getIdentity();
    $item->save();

    return $item;

  }

  public function clearItems()
  {
    $tableItem = Engine_Api::_()->getDbTable('listItems', 'wall');
    $tableItem->delete(array(
      'list_id = ?' => $this->getIdentity()
    ));
  }

  public function _delete()
  {
    parent::_delete();
    $this->clearItems();
  }

  public function getItems()
  {
    $tableItem = Engine_Api::_()->getDbTable('listItems', 'wall');

    $select = $tableItem->select()
        ->where('list_id = ?', $this->getIdentity());

    $items = array();

    foreach ($tableItem->fetchAll($select) as $item){
      $items[] = array(
        'type' => $item->object_type,
        'id' => $item->object_id
      );
    }

    return $items;
  }

}