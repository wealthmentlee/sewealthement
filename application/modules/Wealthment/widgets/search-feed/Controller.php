<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wealthment_Widget_SearchFeedController extends Engine_Content_Widget_Abstract
{
  public  $pinfeed = 1;
  public function indexAction()
  {
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      $subject = Engine_Api::_()->core()->getSubject();
      if( !$subject->authorization()->isAllowed($viewer, 'view') || !in_array($subject->getType(), Engine_Api::_()->wall()->getSupportedItems())) {
        return $this->setNoRender();
      }
    }




    if ($subject && ($subject instanceof User_Model_User) && $subject->getIdentity() == $viewer->getIdentity() && Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.profilehome', false)){
      $subject = null;
    }

    $this->view->subject = $subject;

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->cat = $cat = $this->_getParam('cat');
    // Get some options
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly', false);
    //$this->view->length = $length = 30;//$request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
    $this->view->itemActionLimit = $itemActionLimit = 30;//Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 30);
    $this->view->pinfeed  =  $settings->__get('Pinfeed.use_homepage', 'choice');


    if($this->view->pinfeed!=1 || $this->view->pinfeed ==0 ){
      $this->view->pinfeed =0;
    }

        $pin   = $request->getParam('pinfeed');
    if($pin !=1){
      $this->view->pinfeed =0;
    }

 //$this->view->pinfeed = 1;
    $this->view->updateSettings   = 12000; //Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
    $this->view->viewAllLikes     = $request->getParam('viewAllLikes',    $request->getParam('show_likes',    false));
    $this->view->viewAllComments  = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
    $this->view->getUpdate        = $request->getParam('getUpdate');
    $this->view->checkUpdate      = $request->getParam('checkUpdate');
    $this->view->action_id        = (int) $request->getParam('action_id');
    $this->view->comment_pagination = $request->getParam('comment_pagination', false);

    $userSetting = null;
    if ($viewer->getIdentity()){
      $userSetting = Engine_Api::_()->getDbTable('userSettings', 'wall')->getUserSetting($viewer);
    }
    $this->view->userSetting = $userSetting;

    $customStreamClass = null;

    if( $feedOnly ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    

    $config = array(
      'action_id' => (int) $request->getParam('action_id'),
      'max_id'    => (int) $request->getParam('maxid'),
      'min_id'    => (int) $request->getParam('minid'),
      //'limit'     => (int) $length,
      'page'      => (int) $request->getParam('page')
    );

	$config['q'] = $q = $_GET['query'];

    // get mute actions
    if ($viewer->getIdentity()){
      $config['hideIds'] = Engine_Api::_()->getDbTable('mute', 'wall')->getActionIds($viewer);
    }

    $list_params = array(
      'mode' => 'recent',
      'list_id' => 0,
      'type' => ''
    );


    // Lists
    if (empty($subject) && $viewer->getIdentity()){

      $default_type = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.default');
      if ($default_type != ''){
        $list_params['mode'] = 'type';
        $list_params['type'] = $default_type;

        if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.user_save', false)){
          $userSetting->getParams();
          $list_params['mode'] = $userSetting->mode;
          $list_params['type'] = $userSetting->type;
          $list_params['list_id'] = $userSetting->list_id;
        }

      }
      if ($request->getParam('mode')){

        if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.user_save', false)){
          $userSetting->setParams($request);
        }

        $list_params['mode'] = $request->getParam('mode', 'recent');
        $list_params['list_id'] = $request->getParam('list_id');
        $list_params['type'] = $request->getParam('type');
      }

      $this->view->list_params = $list_params;

      if ($list_params['mode'] == 'type'){

        try {

          $types = Engine_Api::_()->wall()->getManifestType('wall_type');

          if (in_array($list_params['type'], array_keys($types))){
            $typeClass = Engine_Api::_()->loadClass(@$types[$list_params['type']]['plugin']);
            if ($typeClass instanceof Wall_Plugin_Type_Abstract) {
              $config['items'] = $typeClass->getItems($viewer);
              $config['showTypes'] = $typeClass->getTypes($viewer);
              if ($typeClass->customStream){
                $customStreamClass = $typeClass;
              }
            }
          }

        } catch (Exception $e){}

      } else if ($list_params['mode'] == 'list' && Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.listenable', true)){

        $list = Engine_Api::_()->getDbTable('lists', 'wall')->getList($list_params['list_id']);
        if ($list) {
          $config['items'] = $list->getItems();
        }

      } else if ($list_params['mode'] == 'friendlist' && Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.frendlistenable', true)){

        $list = Engine_Api::_()->getDbTable('lists', 'user')->fetchRow(array('list_id = ?' => $list_params['list_id']));

        if ($list) {

          $table = Engine_Api::_()->getDbTable('users', 'user');
          $select = $table->select()
              ->from(array('li' => Engine_Api::_()->getDbTable('listItems', 'user')->info('name')), array())
              ->join(array('u' => $table->info('name')), 'li.child_id = u.user_id', new Zend_Db_Expr('u.*'))
              ->where('li.list_id = ?', $list->getIdentity());

          $data = array();
          foreach ($table->fetchAll($select) as $item){
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
	$actions = $actions_for_count = $actionTable->fetchAll($actionTable->select()->where('body like ?',"%".$q."%")->order('action_id DESC'));
	
      $this->view->length = $length = count($actions_for_count);
    
	
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
	
    /*do {


      /*if ($customStreamClass){
        $actions = $customStreamClass->getCustomStream($viewer, $tmpConfig);
        if (!empty($customStreamClass->feed_config)){
          $this->view->feed_config = $customStreamClass->feed_config;
        }
      } else if (!empty($subject)){
        $actions = $actionTable->getActivityAbout($subject, $viewer, $tmpConfig);
      } else {
        $actions = $actionTable->getActivity($viewer, $tmpConfig);
      }*/
	
	  //$actions = $actionTable->fetchAll($actionTable->select()->where('body like ?',"%".$q."%")->order('action_id DESC'));
		
      /*$selectCount++;

      if (count($actions) < $length || count($actions) <= 0) {
        $endOfFeed = true;
      }

      if (count($actions) > 0) {

        foreach ($actions as $action) {

          if (null === $nextid || $action->action_id <= $nextid) {
            $nextid = $action->action_id - 1;
          }
          if( null === $firstid || $action->action_id > $firstid ) {
            $firstid = $action->action_id;
          }

          if($cat != null) {
			if($action->cat != $cat) continue;
		  }
		  
          if( !$action->getTypeInfo() || !$action->getTypeInfo()->enabled ) continue;

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

          if (in_array($action->type, $group_types)){

            $subject_guid = $action->getSubject()->getGuid();
            $total_guid = $action->type . '_' . $subject_guid;

            if (!isset($grouped_actions[$total_guid])){
              $grouped_actions[$total_guid] = array();
            }
            $grouped_actions[$total_guid][] = $action->getObject();

            if (count($grouped_actions[$total_guid]) > 1){
              continue ;
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

    } while (count($activity) < $length && $selectCount <= 3 && !$endOfFeed);*/


	if (count($actions) > 0) {

        foreach ($actions as $action) {

          if (null === $nextid || $action->action_id <= $nextid) {
            $nextid = $action->action_id - 1;
          }
          if( null === $firstid || $action->action_id > $firstid ) {
            $firstid = $action->action_id;
          }

          if($cat != null) {
			if($action->cat != $cat) continue;
		  }
		  
          if( !$action->getTypeInfo() || !$action->getTypeInfo()->enabled ) continue;

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

          if (in_array($action->type, $group_types)){

            $subject_guid = $action->getSubject()->getGuid();
            $total_guid = $action->type . '_' . $subject_guid;

            if (!isset($grouped_actions[$total_guid])){
              $grouped_actions[$total_guid] = array();
            }
            $grouped_actions[$total_guid][] = $action->getObject();

            if (count($grouped_actions[$total_guid]) > 1){
              continue ;
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
	  
    foreach ($activity as $key => $action){

      if (in_array($action->type, $group_types)){

        $subject_guid = $action->getSubject()->getGuid();
        $total_guid = $action->type . '_' . $subject_guid;

        if (isset($grouped_actions[$total_guid])){
          foreach ($grouped_actions[$total_guid] as $item){
            $activity[$key]->grouped_subjects[] = $item;
          }
        }
      }
    }

    $this->view->activity = $activity;
    $this->view->activityCount = count($activity);
    $this->view->nextid = (int) $nextid;
    $this->view->firstid = $firstid;
    $this->view->endOfFeed = $endOfFeed;


    if( !empty($subject) ) {
      $this->view->subjectGuid = $subject->getGuid(false);
    }

    $this->view->enableComposer = false;
    if( $viewer->getIdentity() && !$this->_getParam('action_id') ) {
      if( !$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer)) ) {
        if( Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status') ) {
          $this->view->enableComposer = true;
        }
      } else if( $subject ) {
        if( Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment') ) {
          $this->view->enableComposer = true;
        }
      }
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('wall');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);



    if (!$feedOnly){ // no ajax

      // Instance
      $unique = rand(11111, 99999);
      $this->view->feed_uid = 'wall_' . $unique;

      $composers_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.composers.disabled', 'smile'));

      // Composers
      $composePartials = array();
      foreach (Engine_Api::_()->wall()->getManifestType('wall_composer') as $type => $config){
        if( !empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1]) ) {
          continue;
        }
        if (in_array($type, $composers_disabled)){
          continue ;
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
    foreach (Engine_Api::_()->wall()->getPrivacy($privacy_type) as $item){
      if (in_array($privacy_type.'_'.$item, $privacy_disabled)){
        continue ;
      }
      $privacy[] = $item;
    }
    $this->view->privacy = $privacy;

    if ($viewer->getIdentity() && $privacy){

      $this->view->allowPrivacy = true;
      $this->view->privacy_active = (empty($privacy[0])) ? null : $privacy[0];

      $last_privacy = Engine_Api::_()->getDbTable('userSettings', 'wall')->getLastPrivacy($subject, $viewer);
      if ($last_privacy && in_array($last_privacy, $privacy)){
        $this->view->privacy_active = $last_privacy;
      }

    }


    $this->view->fbpage_id = 0;
    if ($subject && $subject->getType() == 'page'){
      $fbpage_id = Engine_Api::_()->wall()->getPageLink($viewer, $subject->getIdentity());
      if (!empty($fbpage_id)){
        $this->view->fbpage_id = $fbpage_id;
      }
    }


  }

}