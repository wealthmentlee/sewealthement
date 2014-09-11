<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Actions.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_DbTable_Actions extends Activity_Model_DbTable_Actions
{
  protected $_rowClass = 'Wall_Model_Action';
  protected $_name = 'activity_actions';



  public function addActivity(Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object,
          $type, $body = null, array $params = null, $privacy = false)
  {
    // Disabled or missing type
    $typeInfo = $this->getActionType($type);
    if( !$typeInfo || !$typeInfo->enabled )
    {
      return;
    }

    // User disabled publishing of this type
    $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
    if( !$actionSettingsTable->checkEnabledAction($subject, $type) ) {
      return;
    }

    // Create action
    $action = $this->createRow();
    $action->setFromArray(array(
      'type' => $type,
      'subject_type' => $subject->getType(),
      'subject_id' => $subject->getIdentity(),
      'object_type' => $object->getType(),
      'object_id' => $object->getIdentity(),
      'body' => (string) $body,
      'params' => (array) $params,
      'date' => date('Y-m-d H:i:s')
    ));
    $action->save();

    Engine_Api::_()->getDbTable('privacy', 'wall')->addPrivacy($action, $privacy);

    // Add bindings
    $this->addActivityBindings($action, $type, $subject, $object);

    // We want to update the subject
    if( isset($subject->modified_date) )
    {
      $subject->modified_date = date('Y-m-d H:i:s');
      $subject->save();
    }

   //  Engine_Api::_()->getDbtable('actions', 'hashtags')->addHashtag($action->getParams()->body,$action->getParams()->action_id );
    return $action;
  }


  public function getActivity(User_Model_User $user, $params = array())
  {
	
    $settings = Engine_Api::_()->getApi('settings', 'core');
	
    // params
    $limit = (empty($params['limit'])) ? $settings->getSetting('activity.length', 20) : (int)$params['limit'];
    $action_id = (empty($params['action_id'])) ? null : (int)$params['action_id'];
    $max_id = (empty($params['max_id'])) ? null : (int)$params['max_id'];
    $min_id = (empty($params['min_id'])) ? null : (int)$params['min_id'];
    $hideIds = (empty($params['hideIds'])) ? null : $params['hideIds'];
    $showTypes = (empty($params['showTypes'])) ? null : $params['showTypes'];
    $hideTypes = (empty($params['hideTypes'])) ? null : $params['hideTypes'];


    $tableTypes = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $select = $tableTypes->select()
        ->where('enabled = 1')
        ->where('displayable & 4')
        ->where('module IN (?)', Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames());

    $total_types = $tableTypes->fetchAll($select);

    $types = array();
    foreach ($total_types as $item) {
      $types[] = $item->type;
    }
    if (!empty($showTypes) && is_array($showTypes)) {
      $types = array_intersect($types, $showTypes);
    }
    if (!empty($hideTypes) && is_array($hideTypes)) {
      $types = array_diff($types, $hideTypes);
    }

    if (empty($types)) {
      return null;
    }


    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
      'for' => $user,
    ));
    $responses = (array)$event->getResponses();

    if (empty($responses)) {
      return null;
    }

    /**
     * @var $actionTable Activity_Model_DbTable_Actions
     * @var $mapsTable HashTag_Model_DbTable_Maps
     * @var $tagsTable HashTag_Model_DbTable_Tags
     */
    $actionTable = Engine_Api::_()->getDbTable('actions', 'activity');
    $streamTable = Engine_Api::_()->getDbTable('stream', 'activity');
    if ($params['hashtag']==1) {

      //MapsTable
      $mapsTable = Engine_Api::_()->getDbTable('maps','hashtag');
      $mTName = $mapsTable->info('name');
      $where = 1;
      $where_id = '';
      //TagsTable
      $tagsTable = Engine_Api::_()->getDbTable('tags','hashtag');
      $tTName = $tagsTable->info('name');
      if($params['hashtag_type'] == 'page'){
        $where = 'a.object_type = ?';
        $where_id = $params['hashtag_type'];
      }
      if($params['id'] != -1){
        $object = 'a.object_id = ?';
        $object_id = $params['id'];
      }else{
        $object = '';
        $object_id = '';
      }
      $friend_ids = array(0);
      $data = $data = $user->membership()->getMembershipsOfIds();
      ;
      if (!empty($data)) {
        $friend_ids = array_merge($friend_ids, $data);
      }
      $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
      $tagWhere = '
        (m.hashtagger_type = "user" AND  m.hashtagger_id = ' . $user->getIdentity() . ')
        or
        (ISNULL(p.action_id) OR p.privacy = "everyone" OR p.privacy = "registered")
        OR ((p.privacy = "networks" OR p.privacy = "members") AND m.hashtagger_type = "user" AND m.hashtagger_id IN (' . implode(",", $friend_ids) . ') )
        OR ((p.privacy = "owner" OR p.privacy = "page") AND m.hashtagger_type = "user" AND m.hashtagger_id = ' . $user->getIdentity() . ')
      ';
      $select = $actionTable->select()->group('m.map_id')
        ->from(array('a' => $actionTable->info('name')))
        ->joinInner(array('m' => $mTName), 'a.action_id = m.resource_id', array())
        ->joinInner(array('t' => $tTName), 'm.map_id = t.map_id', array())
        ->joinLeft(array('p' => $privacyTable->info('name')), 'p.action_id = m.resource_id', array())
        ->where('t.hashtag = ?', $params['hashtag_name'])->where($where, $where_id)->where(new Zend_Db_Expr($tagWhere));
          if($params['update']>0){
            $select->where('a.action_id > ?',$params['update']);
          }
      if($params['id'] != -1 && $params['hashtag_type'] == 'page')
        $select->where($object , $object_id);

         $select->order('a.action_id DESC');
      //$select->where("body like '%#".$params['hashtag_name']."%' ")->order('action_id desc');


      if (!empty($hideIds) && is_array($hideIds)) {
        $select->where('m.resource_id NOT IN (?)', $hideIds);
      }
      //print_die($select . '');
      $fatch_all = $this->fetchAll($select);
     if($fatch_all[0]['action_id'] ==$params['update']){
      return;
      }
      /*
        if($params['update']>0){
          foreach($fatch_all as $key => $item){
            if($item['action_id']<=$params['update']){
              unset($fatch_all[$key]);
              print_arr($params['update']);
            }
          }
      }*/




      return $fatch_all;

    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $union = new Zend_Db_Select($db);


    foreach ($responses as $response) {

      if (empty($response)) continue;

      $select = $streamTable->select()
          ->from($streamTable->info('name'), 'action_id')
          ->where('target_type = ?', $response['type']);

      if (empty($response['data'])) {
        $select->where('target_id = ?', 0);
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if (is_array($response['data'])) {
        $select->where('target_id IN(?)', (array)$response['data']);
      } else {
        continue;
      }

      $select
          ->where('type IN (?)', $types);


      if (null !== $min_id) {
        $select->where('action_id >= ?', $min_id);
      } else if (null !== $max_id) {
        $select->where('action_id <= ?', $max_id);
      } else if ($action_id){
        $select->where('action_id = ?', $action_id);
      }

      if (!empty($hideIds) && is_array($hideIds)) {
        $select->where('action_id NOT IN (?)', $hideIds);
      }


      $where = '';

      if (isset($params['items']) && is_array($params['items'])) {

        if (!empty($params['items'])) {

          $where = "(";

          $group_items = array();
          foreach ($params['items'] as $item) {
            if (empty($group_items[$item['type']])) {
              $group_items[$item['type']] = array();
            }
            $group_items[$item['type']][] = $item['id'];
          }
          foreach ($group_items as $key => $item) {
            $where .= "(subject_type = '" . $key . "' AND subject_id IN (" . implode(",", $item) . ")) OR (object_type = '" . $key . "' AND object_id IN (" . implode(",", $item) . ")) OR ";
          }

          $where = substr($where, 0, -4);
          $where .= ")";


          if (!empty($where)) {
            $select->where(new Zend_Db_Expr($where));
          }

        } else {

          $select->where('0');

        }

      }

	
	//echo $union;exit;
      $select
          ->order('action_id DESC')
          ->limit($limit);

      $union->union(array('(' . $select->__toString() . ')'));
	  

    }


    /**
     * Fetch Tags (except in lists)
     */

    if ($showTypes == null && @$params['items'] === null) {

      $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
      $tableTag = Engine_Api::_()->getDbTable('tags', 'wall');

      $friend_ids = array(0);
      $data = $data = $user->membership()->getMembershipsOfIds();
      ;
      if (!empty($data)) {
        $friend_ids = array_merge($friend_ids, $data);
      }

      $tagWhere = '
        (t.object_type = "user" AND t.object_id = ' . $user->getIdentity() . ')
        AND
        (ISNULL(p.action_id) OR p.privacy = "everyone" OR p.privacy = "registered")
        OR ((p.privacy = "networks" OR p.privacy = "members") AND t.object_type = "user" AND t.object_id IN (' . implode(",", $friend_ids) . ') )
        OR ((p.privacy = "owner" OR p.privacy = "page") AND t.object_type = "user" AND t.object_id = ' . $user->getIdentity() . ')
      ';

      $selectTag = $tableTag->select()
          ->setIntegrityCheck(false)
          ->from(array('t' => $tableTag->info('name')), array('t.action_id'))
          ->join(array('p' => $privacyTable->info('name')), 'p.action_id = t.action_id', array())
          ->where(new Zend_Db_Expr($tagWhere));


      /*      $selectTag
   ->where('type IN (?)', $types);*/


      if (null !== $min_id) {
        $selectTag->where('t.action_id >= ?', $min_id);
      } else if (null !== $max_id) {
        $selectTag->where('t.action_id <= ?', $max_id);
      } else if ($action_id){
        $selectTag->where('t.action_id = ?', $action_id);
      }


      if (!empty($hideIds) && is_array($hideIds)) {
        $selectTag->where('t.action_id NOT IN (?)', $hideIds);
      }
      $selectTag->group('t.action_id');

      $selectTag
          ->order('action_id DESC')
          ->limit($limit);

      //$union->union(array('(' . $selectTag->__toString() . ')'));

    }

    $union
        ->order('action_id DESC')
        ->limit($limit);

	


    $actions = $db->fetchAll($union);

    if (empty($actions)) {
      return null;
    }

    $ids = array();
    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);
	
	$actionSelect = $this->select()
          ->where('action_id IN(' . join(',', $ids) . ')')
          ->order('action_id DESC')
          ->limit($limit);
		  
	if(!empty($params['q'])){ // gd code starts
		$actionSelect->where('body like ?','%'.$params['q'].'%');
	} // gd code ends
	
    return $this->fetchAll($actionSelect);

  }


  public function getActivityAbout(Core_Model_Item_Abstract $about, User_Model_User $user,
                                   array $params = array())
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // params
    $limit = (empty($params['limit'])) ? $settings->getSetting('activity.length', 20) : (int)$params['limit'];
    $max_id = (empty($params['max_id'])) ? null : (int)$params['max_id'];
    $min_id = (empty($params['min_id'])) ? null : (int)$params['min_id'];
    $hideIds = (empty($params['hideIds'])) ? null : $params['hideIds'];
    $showTypes = (empty($params['showTypes'])) ? null : $params['showTypes'];
    $hideTypes = (empty($params['hideTypes'])) ? null : $params['hideTypes'];


    $tableTypes = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $select = $tableTypes->select()
        ->where('enabled = 1')
        ->where('displayable & 1 OR displayable & 2')
        ->where('module IN (?)', Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames());


    $total_types = $tableTypes->fetchAll($select);

    $types = array();
    foreach ($total_types as $item) {
      $types[] = $item->type;
    }
    if (!empty($showTypes) && is_array($showTypes)) {
      $types = array_intersect($types, $showTypes);
    }
    if (!empty($hideTypes) && is_array($hideTypes)) {
      $types = array_diff($types, $hideTypes);
    }
    $subjectActionTypes = array(0);
    $objectActionTypes = array(0);

    foreach ($total_types as $type) {
      if ($type->displayable & 1) {
        $subjectActionTypes[] = $type->type;
      }
      if ($type->displayable & 2) {
        $objectActionTypes[] = $type->type;
      }
    }
    if (empty($types)) {
      return null;
    }


    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
      'for' => $user,
      'about' => $about
    ));
    $responses = (array)$event->getResponses();

    if (empty($responses)) {
      return null;
    }


    $actionTable = Engine_Api::_()->getDbTable('actions', 'activity');
    $streamTable = Engine_Api::_()->getDbTable('stream', 'activity');

    $db = Engine_Db_Table::getDefaultAdapter();
    $union = new Zend_Db_Select($db);


    foreach ($responses as $response) {
      if (empty($response)) continue;

      $select = $streamTable->select()
          ->from($streamTable->info('name'), 'action_id')
          ->where('target_type = ?', $response['type']);

      if (empty($response['data'])) {
        $select->where('target_id = ?', 0);
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if (is_array($response['data'])) {
        $select->where('target_id IN(?)', (array)$response['data']);
      } else {
        continue;
      }



      if (null !== $min_id) {
        $select->where('action_id >= ?', $min_id);
      } else if (null !== $max_id) {
        $select->where('action_id <= ?', $max_id);
      }

      if (!empty($hideIds) && is_array($hideIds)) {
        $select->where('action_id NOT IN (?)', $hideIds);
      }

      $select->where(new Zend_Db_Expr("(subject_type = '".$about->getType()."' AND subject_id = ".$about->getIdentity()." AND type IN ('".implode("','", $subjectActionTypes)."') ) OR (object_type = '".$about->getType()."' AND object_id = ".$about->getIdentity()." AND type IN ('".implode("','", $objectActionTypes)."') )"));



      $select
          ->order('action_id DESC')
          ->limit($limit);


      if ($about->getType() == 'user') {
        if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page')) {
          $data = Engine_Api::_()->getDbtable('membership', 'page')->getMembershipsOfIds($about);
          if (!empty($data)) {
            $select->where('!(object_type = "page" AND object_id IN (?))', $data);
          }
        }
      }


      $union->union(array('(' . $select->__toString() . ')'));

    }





    /**
     * Fetch Tags (except in lists)
     */

    if ($about->getType() == 'user') {


      $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
      $tableTag = Engine_Api::_()->getDbTable('tags', 'wall');


      // friends
      $friend_ids = array(0);
      $data = $data = $user->membership()->getMembershipsOfIds();
      ;
      if (!empty($data)) {
        $friend_ids = array_merge($friend_ids, $data);
      }

      $tagWhere = '
        (t.object_type = "user" AND t.object_id = ' . $about->getIdentity() . ')
        AND
        (
        (ISNULL(p.action_id) OR p.privacy = "everyone" OR p.privacy = "registered")
        OR ((p.privacy = "networks" OR p.privacy = "members") AND t.object_type = "user" AND t.object_id IN (' . implode(",", $friend_ids) . ') )
        OR ((p.privacy = "owner" OR p.privacy = "page") AND t.object_type = "user" AND t.object_id = ' . $user->getIdentity() . ')
        )
      ';

      $selectTag = $tableTag->select()
          ->setIntegrityCheck(false)
          ->from(array('t' => $tableTag->info('name')), array('t.action_id'))
          ->join(array('p' => $privacyTable->info('name')), 'p.action_id = t.action_id', array())
          ->where(new Zend_Db_Expr($tagWhere));

      /*      $selectTag
   ->where('type IN (?)', $types);*/


      if (null !== $min_id) {
        $selectTag->where('t.action_id >= ?', $min_id);
      } else if (null !== $max_id) {
        $selectTag->where('t.action_id <= ?', $max_id);
      }

      if (!empty($hideIds) && is_array($hideIds)) {
        $selectTag->where('t.action_id NOT IN (?)', $hideIds);
      }
      $selectTag->group('t.action_id');


      $union->union(array('(' . $selectTag->__toString() . ')'));


    }


    $union
        ->order('action_id DESC')
        ->limit($limit);

    $actions = $db->fetchAll($union);

    if (empty($actions)) {
      return null;
    }

    $ids = array();
    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    return $this->fetchAll(
      $this->select()
          ->where('action_id IN(' . join(',', $ids) . ')')
          ->order('action_id DESC')
          ->limit($limit)
    );


  }


  protected function _getInfo(array $params)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $args = array(
      'limit' => $settings->getSetting('activity.length', 20),
      'action_id' => null,
      'max_id' => null,
      'min_id' => null,
      'showTypes' => null,
      'hideTypes' => null,
      'hideIds' => null,
    );

    $newParams = array();
    foreach( $args as $arg => $default ) {
      if( !empty($params[$arg]) ) {
        $newParams[$arg] = $params[$arg];
      } else {
        $newParams[$arg] = $default;
      }
    }

    return $newParams;
  }

  public function addActivityBindings($action)
  {
    // Get privacy bindings
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('addActivity', array(
      'subject' => $action->getSubject(),
      'object' => $action->getObject(),
      'type' => $action->type,
    ));

    // Add privacy bindings
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    foreach( (array) Engine_Api::_()->wall()->replaceStream($action, $event->getResponses()) as $response )
    {
      if( isset($response['target']) )
      {
        $target_type = $response['target'];
        $target_id = 0;
      }

      else if( isset($response['type']) && isset($response['identity']) )
      {
        $target_type = $response['type'];
        $target_id = $response['identity'];
      }

      else
      {
        continue;
      }

      $streamTable->insert(array(
        'action_id' => $action->action_id,
        'type' => $action->type,
        'target_type' => (string) $target_type,
        'target_id' => (int) $target_id,
        'subject_type' => $action->subject_type,
        'subject_id' => $action->subject_id,
        'object_type' => $action->object_type,
        'object_id' => $action->object_id,
      ));
    }
    return $this;
  }




  public function getPageAction($user, $action_id)
  {
    if (!$user){
      return ;
    }

    $tableTypes = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $select = $tableTypes->select()
        ->where('enabled = 1')
        ->where('displayable & 4 OR displayable & 1 OR displayable & 2')
        ->where('module IN (?)', Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames());

    $total_types = $tableTypes->fetchAll($select);


    $types = array();
    foreach ($total_types as $item){
      $types[] = $item->type;
    }


    if (empty($types)){
      return null;
    }

    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
      'for' => $user,
    ));
    $responses = (array) $event->getResponses();

    if( empty($responses) ) {
      return null;
    }
    $tableStream = Engine_Api::_()->getDbTable('stream', 'activity');

    $where = '0';
    foreach ($responses as $response){

      $where .= ' OR (target_type = "'.$response['type'].'" AND ';

      if( empty($response['data']) ) {
        $where .= 'target_id = 0';
      } else if( is_scalar($response['data']) || count($response['data']) === 1 ) {
        if( is_array($response['data']) ) {
          list($response['data']) = $response['data'];
        }
        $where .= 'target_id = ' . $response['data'];
      } else if( is_array($response['data']) ) {
        $where .= 'target_id IN (' . implode(",", (array) $response['data']) . ')';
      } else {
        continue;
      }

      $where .= ')';

    }

    $actionTable = Engine_Api::_()->getDbTable('actions', 'wall');

    $select = $actionTable->select()
        ->setIntegrityCheck(false)
        ->from(array('s' => $tableStream->info('name')), array())
        ->join(array('a' => $actionTable->info('name')), 'a.action_id = s.action_id', new Zend_Db_Expr('a.*'))
        ->where(new Zend_Db_Expr($where));

    $select
        ->where('s.type IN (?)', $types)
        ->where('s.action_id = ?', $action_id)
        ->group('s.action_id')
        ->order('a.action_id DESC')
    ;



    $action = $actionTable->fetchRow($select);


    // if action tagged

    if (!$action){

      $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
      $tableTag = Engine_Api::_()->getDbTable('tags', 'wall');


      // friends
      $friend_ids = array(0);
      $data = $data = $user->membership()->getMembershipsOfIds();;
      if (!empty($data)){
        $friend_ids = array_merge($friend_ids, $data);
      }

      $tagWhere = '
      (t.object_type = "user" AND t.object_id = '.$user->getIdentity().')
      AND
      (ISNULL(p.action_id) OR p.privacy = "everyone" OR p.privacy = "registered")
      OR ((p.privacy = "networks" OR p.privacy = "members") AND t.object_type = "user" AND t.object_id IN ('.implode(",", $friend_ids).') )
      OR ((p.privacy = "owner" OR p.privacy = "page") AND t.object_type = "user" AND t.object_id = '.$user->getIdentity().')
    ';

      $selectTag = $tableTag->select()
          ->setIntegrityCheck(false)
          ->from(array('t' => $tableTag->info('name')), array())
          ->join(array('a' => $actionTable->info('name')), 'a.action_id = t.action_id', array('a.*'))
          ->joinLeft(array('p' => $privacyTable->info('name')), 'p.action_id = a.action_id', array())
          ->where(new Zend_Db_Expr($tagWhere))
          ->where('a.action_id = ?', $action_id)

      ;

      if (count($types) == count($total_types)){

      } else {
        $selectTag
            ->where('a.type IN (?)', $types);
      }

      $selectTag->group('t.action_id');

      $action = $actionTable->fetchRow($selectTag);

    }

    return $action;

  }






}