<?php

class Hashtag_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
  }

  public function searchAction()
  {

    $name = $this->_getParam('name');
    $pinfeed_module  = $this->_getParam('pinfeed');

    $type_name= $this->_getParam('type');
    $res_id= $this->_getParam('res_id');
    $res_type= $this->_getParam('res_type');
    $update= $this->_getParam('update');
    $this->view->update = $update;



    $id= $this->_getParam('id');
    if($type_name == 'page'){
    $pTable = Engine_Api::_()->getDbTable('pages', 'page');
    $page_id = $pTable->fetchRow($pTable->select()->where('url = ?' ,$id ));
    }else{
      $page_id = -1;
    }



    $this->view->name= $name;
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
      if (!$subject->authorization()->isAllowed($viewer, 'view') || !in_array($subject->getType(), Engine_Api::_()->wall()->getSupportedItems())) {
        return $this->setNoRender();
      }
    }

    if ($subject && ($subject instanceof User_Model_User) && $subject->getIdentity() == $viewer->getIdentity() && Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.profilehome', false)) {
      $subject = null;
    }

    $this->view->subject = $subject;

    $request = Zend_Controller_Front::getInstance()->getRequest();

    // Get some options
    if($update>0){
      $feed = true;
    }else{
      $feed =false;
    }
    $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly', false);
    $this->view->length = $length = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.asdasd', 1000));
    $this->view->itemActionLimit = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.asdasd', 1000);

    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
    $this->view->viewAllLikes = $request->getParam('viewAllLikes', $request->getParam('show_likes', true));
    $this->view->viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', true));
    $this->view->getUpdate = $request->getParam('getUpdate');
    $this->view->checkUpdate = $request->getParam('checkUpdate');
    $this->view->action_id = (int)$request->getParam('action_id');
    $this->view->comment_pagination = $request->getParam('comment_pagination', true);


    $userSetting = null;
    if ($viewer->getIdentity()) {
      $userSetting = Engine_Api::_()->getDbTable('userSettings', 'wall')->getUserSetting($viewer);
    }
    $this->view->userSetting = $userSetting;

    $customStreamClass = null;

    if ($feedOnly) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    if ($length > 50) {
      $this->view->length = $length = 50;
    }

    $config = array(
      'action_id' => (int)$request->getParam('action_id'),
      'max_id' => (int)$request->getParam('maxid'),
      'min_id' => (int)$request->getParam('minid'),
      'limit' => (int)$length,
      'page' => (int)$request->getParam('page')
    );


    // get mute actions
    if ($viewer->getIdentity()) {
      $config['hideIds'] = Engine_Api::_()->getDbTable('mute', 'wall')->getActionIds($viewer);
    }

    $list_params = array(
      'mode' => 'recent',
      'list_id' => 0,
      'type' => ''
    );

    // Lists
    if (empty($subject) && $viewer->getIdentity()) {

      $default_type = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.default');
      if ($default_type != '') {
        $list_params['mode'] = 'type';
        $list_params['type'] = $default_type;

        if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.user_save', false)) {
          $userSetting->getParams();
          $list_params['mode'] = $userSetting->mode;
          $list_params['type'] = $userSetting->type;
          $list_params['list_id'] = $userSetting->list_id;
        }

      }
      if ($request->getParam('mode')) {

        if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.user_save', false)) {
          $userSetting->setParams($request);
        }

        $list_params['mode'] = $request->getParam('mode', 'recent');
        $list_params['list_id'] = $request->getParam('list_id');
        $list_params['type'] = $request->getParam('type');
      }

      $this->view->list_params = $list_params;

      if ($list_params['mode'] == 'type') {

        try {

          $types = Engine_Api::_()->wall()->getManifestType('wall_type');

          if (in_array($list_params['type'], array_keys($types))) {
            $typeClass = Engine_Api::_()->loadClass(@$types[$list_params['type']]['plugin']);
            if ($typeClass instanceof Wall_Plugin_Type_Abstract) {
              $config['items'] = $typeClass->getItems($viewer);
              $config['showTypes'] = $typeClass->getTypes($viewer);
              if ($typeClass->customStream) {
                $customStreamClass = $typeClass;
              }
            }
          }

        } catch (Exception $e) {
        }

      } else if ($list_params['mode'] == 'list' && Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.listenable', true)) {

        $list = Engine_Api::_()->getDbTable('lists', 'wall')->getList($list_params['list_id']);
        if ($list) {
          $config['items'] = $list->getItems();
        }

      } else if ($list_params['mode'] == 'friendlist' && Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.frendlistenable', true)) {

        $list = Engine_Api::_()->getDbTable('lists', 'user')->fetchRow(array('list_id = ?' => $list_params['list_id']));

        if ($list) {

          $table = Engine_Api::_()->getDbTable('users', 'user');
          $select = $table->select()
            ->from(array('li' => Engine_Api::_()->getDbTable('listItems', 'user')->info('name')), array())
            ->join(array('u' => $table->info('name')), 'li.child_id = u.user_id', new Zend_Db_Expr('u.*'))
            ->where('li.list_id = ?', $list->getIdentity());

          $data = array();
          foreach ($table->fetchAll($select) as $item) {
            $data[] = array('type' => $item->getType(), 'id' => $item->getIdentity());
          }

          $config['items'] = $data;
        }

      }

      $this->view->types = array_keys(Engine_Api::_()->wall()->getManifestType('wall_type'));
      $this->view->lists = Engine_Api::_()->getDbTable('lists', 'wall')->getPaginator($viewer);
      $this->view->friendlists = Engine_Api::_()->getDbTable('lists', 'user')->fetchAll(array('owner_id = ?' => $viewer->getIdentity()));

    }


    $actionTable = Engine_Api::_()->getDbtable('actions', 'wall');
    $config['hashtag'] = 1;
    $config['hashtag_name'] = $name;
    $config['hashtag_type'] = $type_name;
    $config['id'] = $page_id['page_id'];
    $config['update'] = $update;


    $selectCount = 0;
    $nextid = null;
    $firstid = null;
    $tmpConfig = $config;
    $activity = array();
    $endOfFeed = false;

    $friendRequests = array();
    $itemActionCounts = array();

    $grouped_actions = array();
    $group_types = array('friends', 'like_item_private');

    $this->view->feed_config = array();

    do {
      $actions = $actionTable->getActivity($viewer, $tmpConfig);
      $selectCount++;

      if (count($actions) < $length || count($actions) <= 0) {
        $endOfFeed = true;
      }

      if (count($actions) > 0) {

        foreach ($actions as $action) {

          if (null === $nextid || $action->action_id <= $nextid) {
            $nextid = $action->action_id - 1;
          }
          if (null === $firstid || $action->action_id > $firstid) {
            $firstid = $action->action_id;
          }

          if (!$action->getTypeInfo() || !$action->getTypeInfo()->enabled) continue;

          if (!$action->hasObjectItem()) continue;

          if (!$action->getSubject() || !$action->getSubject()->getIdentity()) continue;
          if (!$action->getObject() || !$action->getObject()->getIdentity()) continue;

          if (empty($subject)) {
            $actionSubject = $action->getSubject();
            $actionObject = $action->getObject();
            if (!isset($itemActionCounts[$actionSubject->getGuid()])) {
              $itemActionCounts[$actionSubject->getGuid()] = 1;
            } else if ($itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit) {
              continue;
            } else {
              $itemActionCounts[$actionSubject->getGuid()]++;
            }
          }
          if ($action->type == 'friends') {
            $id = $action->subject_id . '_' . $action->object_id;
            $rev_id = $action->object_id . '_' . $action->subject_id;
            if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
              continue;
            } else {
              $friendRequests[] = $id;
              $friendRequests[] = $rev_id;
            }
          }

          if (in_array($action->type, $group_types)) {

            $subject_guid = $action->getSubject()->getGuid();
            $total_guid = $action->type . '_' . $subject_guid;

            if (!isset($grouped_actions[$total_guid])) {
              $grouped_actions[$total_guid] = array();
            }
            $grouped_actions[$total_guid][] = $action->getObject();

            if (count($grouped_actions[$total_guid]) > 1) {
              continue;
            }

          }

          try {
            $attachments = $action->getAttachments();
          } catch (Exception $e) {
            continue;
          }

          if (count($activity) < $length) {
            $activity[] = $action;
            if (count($activity) == $length) {
              $actions = array();
            }
          }
        }
      }

      if ($nextid) {
        $tmpConfig['max_id'] = $nextid;
      }
      if (!empty($tmpConfig['action_id'])) {
        $actions = array();
      }

    } while (count($activity) < $length && $selectCount <= 3 && !$endOfFeed);


    foreach ($activity as $key => $action) {

      if (in_array($action->type, $group_types)) {

        $subject_guid = $action->getSubject()->getGuid();
        $total_guid = $action->type . '_' . $subject_guid;

        if (isset($grouped_actions[$total_guid])) {
          foreach ($grouped_actions[$total_guid] as $item) {
            $activity[$key]->grouped_subjects[] = $item;
          }
        }
      }
    }


    $this->view->activity = $activity;
    $this->view->activityCount = count($activity);
    $this->view->nextid = (int)$nextid;
    $this->view->firstid = $firstid;
    $this->view->endOfFeed = $endOfFeed;


    if (!empty($subject)) {
      $this->view->subjectGuid = $subject->getGuid(false);
    }

    $this->view->enableComposer = false;
    if ($viewer->getIdentity() && !$this->_getParam('action_id')) {
      if (!$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer))) {
        if (Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status')) {
          $this->view->enableComposer = true;
        }
      } else if ($subject) {
        if (Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment')) {
          $this->view->enableComposer = true;
        }
      }
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('wall');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);


    if (!$feedOnly) { // no ajax

      // Instance
      $unique = rand(11111, 99999);
      $this->view->feed_uid = 'wall_' . $unique;

      $composers_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.composers.disabled', 'smile'));

      // Composers
      $composePartials = array();
      foreach (Engine_Api::_()->wall()->getManifestType('wall_composer') as $type => $config) {
        if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
          continue;
        }
        if (in_array($type, $composers_disabled)) {
          continue;
        }
        $composePartials[$type] = $config['script'];

      }


      $this->view->composePartials = $composePartials;

    }


    // Composer Privacy

    $this->view->allowPrivacy = false;
    $this->view->privacy_type = $privacy_type = ($subject) ? $subject->getType() : 'user';

    $privacy = array();
    $privacy_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.privacy.disabled', ''));
    foreach (Engine_Api::_()->wall()->getPrivacy($privacy_type) as $item) {
      if (in_array($privacy_type . '_' . $item, $privacy_disabled)) {
        continue;
      }
      $privacy[] = $item;
    }
    $this->view->privacy = $privacy;

    if ($viewer->getIdentity() && $privacy) {

      $this->view->allowPrivacy = true;
      $this->view->privacy_active = (empty($privacy[0])) ? null : $privacy[0];

      $last_privacy = Engine_Api::_()->getDbTable('userSettings', 'wall')->getLastPrivacy($subject, $viewer);
      if ($last_privacy && in_array($last_privacy, $privacy)) {
        $this->view->privacy_active = $last_privacy;
      }

    }
    if($res_type=='widget'){
      $tagsTable = Engine_Api::_()->getDbTable('tags', 'hashtag');
      $tName = $tagsTable->info('name');

      $mapsTable = Engine_Api::_()->getDbTable('maps', 'hashtag');
      $mTName = $mapsTable->info('name');

      //$select->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $select = $tagsTable->select()->from(array('t' => $tName), array('*', 'c' => 'COUNT(*)'))
        ->joinInner(array('m' => $mTName), 'm.map_id = t.map_id', array())
        ->where('m.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL ' . $settings->getSetting('hashtag.period',5) . ' DAY)'))
        ->group('hashtag')->order('c desc')->limit($settings->getSetting('hashtag.count', 5));


      $trands = $tagsTable->fetchAll($select);
      $i = 1;
      $array = array();
      foreach($trands as $trand){
        if($name == $trand->hashtag){
          $name_count = $i;
        }
        $array[$i] = $trand->hashtag;
        $i++;
       }
      $this->view->trand = 1;
      $this->view->trand_name = $array;
      $this->view->trand_plase = $name_count;

    }else{

      $tagTable = Engine_Api::_()->getDbTable('tags', 'hashtag');
      $tTName = $tagTable->info('name');
      // print_die('test');
      $mapsTable = Engine_Api::_()->getDbTable('maps', 'hashtag');
      $mTName = $mapsTable->info('name');


      $select = $tagTable->select()
        ->from(array('t' => $tTName))
        ->setIntegrityCheck(false)
        ->joinLeft(array('m' => $mTName), 't.map_id = m.map_id', array('resource_id'))
        ->where('m.resource_id = ?', $res_id);


      $all = $tagTable->fetchAll($select);

      $i=1;
      $array = array();
      $res_array = array();
      foreach($all as $trand){
        if($name == $trand->hashtag){
          $name_count = $i;
        }
        $array[$i] = $trand->hashtag;
        $res_array[$i] = $trand->resource_id;
        $i++;
      }
      $this->view->count = count($all);

      $this->view->trand_name = $array;
      $this->view->res_name = $res_array;
      $this->view->trand_plase = $name_count;
      $this->view->trand = 0;
      $this->view->res_id = $res_id;
    }
    if($pinfeed_module == 1){
      $this->view->pinfeed = 1;
    }
    $this->view->fbpage_id = 0;
    if ($subject && $subject->getType() == 'page') {
      $fbpage_id = Engine_Api::_()->wall()->getPageLink($viewer, $subject->getIdentity());
      if (!empty($fbpage_id)) {
        $this->view->fbpage_id = $fbpage_id;
      }
    }
    if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->view->html = $this->view->render('search.tpl');
    }
  }
}
