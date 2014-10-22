<?php

class Wealthment_Api_Core extends Core_Api_Abstract
{
    
    public function getCatLabel($cat_id) {
        $array = array(
            '0' => 'All',  
			'1' => 'Stocks',  
            '2' => 'Real Estate',
            '3' => 'Retirement',
            '4' => 'Other Savings',
        );
        
        return $array[$cat_id];
    }
    
	public function isFollowed($action_id,$follower) {
        $table = Engine_Api::_()->getDbTable('follows','wealthment');
        $select = $table->select()->where('follower_id = ?',$follower->getIdentity())->where('action_id = ?',$action_id);
        $row = $table->fetchRow($select);
        if($row instanceof Core_Model_Item_Abstract) {
            return true;
        }
        return false;
    }
    
   public function getFollowed($action_id,$follower) {
        $table = Engine_Api::_()->getDbTable('follows','wealthment');
        $select = $table->select()->where('follower_id = ?',$follower->getIdentity())->where('action_id = ?',$action_id);
        $row = $table->fetchRow($select);
        if($row instanceof Core_Model_Item_Abstract) {
            return $row;
        }
        return false;
    }  
    public function getAllFollowers($action_id) {
        $table = Engine_Api::_()->getDbTable('follows','wealthment');
        $select = $table->select()->where('action_id = ?',$action_id);
        $rows = $table->fetchAll($select);
        $users = array();
        foreach($rows as $r) {
            $users[] = Engine_Api::_()->getItem('user',$r->follower_id);
        }
        return $users;
    }  
	
}
