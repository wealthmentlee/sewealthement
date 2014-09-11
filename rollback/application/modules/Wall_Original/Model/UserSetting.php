<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: UserSetting.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_UserSetting extends Engine_Db_Table_Row
{

  public function setParams(Zend_Controller_Request_Abstract $request)
  {
    $mode = $request->getParam('mode');

    if ($mode == 'type'){
      $type = $request->getParam('type');
      $types = Engine_Api::_()->wall()->getManifestType('wall_type');
      if (in_array($type, array_keys($types))){
        $this->mode = $mode;
        $this->type = $type;
        $this->list_id = 0;
        $this->save();
      }
      return ;
    } else if ($mode == 'list'){
      $list_id = $request->getParam('list_id');
      $list = Engine_Api::_()->getDbTable('lists', 'wall')->getList($list_id);
      if ($list){
        $this->mode = $mode;
        $this->type = '';
        $this->list_id = $list_id;
        $this->save();
      }
      return ;
    } else if ($mode == 'friendlist'){
      $list_id = $request->getParam('list_id');
      $list = Engine_Api::_()->getDbTable('lists', 'wall')->getList($list_id);
      if ($list){
        $this->mode = $mode;
        $this->type = '';
        $this->list_id = $list_id;
        $this->save();
      }
      return ;
    }
    $this->setDefault();
  }

  public function setDefault()
  {
    $this->mode = 'recent';
    $this->type = '';
    $this->list_id = 0;
    $this->save();
  }

  public function getParams()
  {
    if ($this->mode == 'type'){
      $types = Engine_Api::_()->wall()->getManifestType('wall_type');
      if (!in_array($this->type, array_keys($types))){
        $this->setDefault();
        return $this->getParams();
      }
    } else if ($this->mode == 'list'){

      $list = Engine_Api::_()->getDbTable('lists', 'wall')->getList($this->list_id);
      if (!$list){
        $this->setDefault();
        return $this->getParams();
      }
    } else if ($this->mode == 'friendlist'){

      $list = Engine_Api::_()->getDbTable('lists', 'user')->fetchRow(array('list_id => ?' => $this->list_id));
      if (!$list){
        $this->setDefault();
        return $this->getParams();
      }
    } else if ($this->mode == 'recent'){

      return $this;

    } else {

      $this->setDefault();

    }
    return $this;

  }



}