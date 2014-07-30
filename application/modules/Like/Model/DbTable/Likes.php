<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 03.07.12
 * Time: 10:25
 * To change this template use File | Settings | File Templates.
 */
class Like_Model_DbTable_Likes extends Engine_Db_Table
{
  protected $_rowClass = 'Like_Model_Like';

  public function getLikeTable()
  {
    return $this;
  }

  public function addLike($resource_type, $resource_title, Core_Model_Item_Abstract $poster)
  {
    $row = $this->getLike($resource_type, $resource_title, $poster);
    if( null !== $row )
    {
      throw new Like_Model_Exception('Already liked');
    }
    $table = $this->getLikeTable();
    $row = $table->createRow();

    $row->resource_type = $resource_type;
    $row->resource_title = $resource_title;
    $row->poster_type = $poster->getType();
    $row->poster_id = $poster->getIdentity();
    $row->save();

    return $row;
  }

  public function removeLike($resource_type, $resource_title, Core_Model_Item_Abstract $poster)
  {
    $row = $this->getLike($resource_type, $resource_title, $poster);
    if( null === $row )
    {
      throw new Like_Model_Exception('No like to remove');
    }

    $row->delete();

    return $this;
  }

  public function getLike($resource_type, $resource_title, Core_Model_Item_Abstract $poster)
  {

    $table = $this->getLikeTable();
    $select = $this->getLikeTable()->select()
      ->where('poster_type = ?', $poster->getType())
      ->where('poster_id = ?', $poster->getIdentity())
      ->where('resource_type = ?', $resource_type)
      ->where('resource_title = ?', $resource_title)
      ->limit(1);

    return $table->fetchRow($select);
  }
}
