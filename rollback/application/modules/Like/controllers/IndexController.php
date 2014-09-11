<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Like_IndexController extends Core_Controller_Action_Standard
{

  public function init()
  {
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('like', 'json')
      ->addActionContext('unlike', 'json')
      ->initContext();

    $this->view->labels = Engine_Api::_()->like()->getSupportedModulesLabels();
    $this->view->icons = Engine_Api::_()->like()->getSupportedModulesIcons();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject($viewer);
    }

    $this->view->nophoto = Engine_Api::_()->like()->getNoPhotos();
  }

  public function indexAction()
  {
    $user_id = $this->_getParam('user_id');
    $period_type = $this->_getParam('period_type', 'all');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if ($user_id !== null) {
      $user = Engine_Api::_()->getItem('user', $user_id);
      if (!$user->getIdentity()) {
        $user = $viewer;
      }
    }
    else {
      $user = $viewer;
    }

    if (!$this->_helper->requireAuth()->setAuthParams($user, $viewer, 'interest')->isValid())
      return;

    $this->view->user = $user;
    $this->view->isSelf = $isSelf = $user->isSelf($viewer);

    $mutualItems = array();
    if (!$isSelf) {
      $params = array('poster_id' => $user->getIdentity(), 'mutual' => $viewer->getIdentity(), 'poster_type' => 'user', 'fetch' => 'resource');
      $select = Engine_Api::_()->like()->getLikesSelect($params, $period_type);

      $db = Engine_Api::_()->like()->getTable()->getAdapter();
      $mutuals = $db->fetchAll($select);

      foreach ($mutuals as $mutual) {
        $mutualItems[] = $mutual['resource_type'] . '_' . $mutual['resource_id'];
      }
    }
    $this->view->mutualItems = $mutualItems;
    $items = Engine_Api::_()->like()->getLikedItems($user, true, $period_type);

    if(!is_array($items)){
      $items = array();
    }
    foreach ($items as $type => $item) {
      $checkModule = $type;
      switch ($type) {
        case 'group' :
          {
          $checkModule = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('group')
            ? 'group'
            : 'advgroup';
          }
          break;
        case 'avp_video' :
          $checkModule = 'avp';
          break;
        case 'music_playlist' :
          $checkModule = 'music';
          break;
        case 'store_product' :
          $checkModule = 'store';
          break;
        case 'list_listing' :
          $checkModule = 'list';
          break;
        case 'artarticle' :
          $checkModule = 'advancedarticles';
          break;
      }
      if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled($checkModule)) {
        unset($items[$type]);
        continue;
      }
    }

    $this->view->items = $items;

    $this->view->modules = array_keys($items);

    sort($this->view->modules);
    if(isset($this->view->modules[0])){
      $this->view->activeTab = $this->view->modules[0];
    }
  }

  public function likeAction()
  {
    $api = Engine_Api::_()->like();
    $object = $this->_getParam('object', '');
    $object_id = $this->_getParam('object_id', '');

    $object = Engine_Api::_()->getItem($object, $object_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->view->error = 3;
      $this->view->html = Zend_Registry::get('Zend_Translate')->_('LIKE_LOGIN_ERROR');
      return;
    }

    if (in_array($object, array('page', 'group', 'event', 'user'))) {
      if (!$api->isAllowed($object)) {
        $this->view->error = 2;
        $this->view->html = Zend_Registry::get('Zend_Translate')->_('LIKE_AUTH_ERROR');
        return;
      }
    }

    if ($api->isLike($object)) {
      $this->view->error = 4;
      $this->view->html = Zend_Registry::get('Zend_Translate')->_('like_Already liked.');
      return;
    }

    $this->view->link = 'unlike';
    if ($api->like($object)) {
      $api->addAction($object, $viewer);
      if ($object->getType() == 'page' && Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
        $api->notify($object, 'send');
      }
    }
    else {
      $this->view->error = 1;
      $this->view->html = 'Error';
    }
  }

  public function unlikeAction()
  {
    $api = Engine_Api::_()->like();
    $object = $this->_getParam('object', '');
    $object_id = $this->_getParam('object_id', '');

    $object = Engine_Api::_()->getItem($object, $object_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->view->error = 3;
      $this->view->html = Zend_Registry::get('Zend_Translate')->_('LIKE_LOGIN_ERROR');
      return;
    }

    if (in_array($object, array('page', 'group', 'event', 'user'))) {
      if (!$api->isAllowed($object)) {
        $this->view->error = 2;
        $this->view->html = Zend_Registry::get('Zend_Translate')->_('LIKE_AUTH_ERROR');
        return;
      }
    }

    if (!$api->isLike($object)) {
      $this->view->error = 4;
      $this->view->html = Zend_Registry::get('Zend_Translate')->_('like_Already unliked.');
      return;
    }

    $this->view->link = 'like';
    if ($api->unlike($object)) {
      $api->deleteAction($object, $viewer);
      if ($object->getType() == 'page') {
        $api->notify($object, 'delete');
      }
    }
    else {
      $this->view->error = 1;
      $this->view->html = Zend_Registry::get('Zend_Translate')->_('LIKE_UNDEFINED_ERROR');
    }
  }

  public function seeLikedAction()
  {
    $user_id = (int)$this->_getParam('user_id');
    $period_type = $this->_getParam('period_type', 'all');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('LIKE_LOGIN_ERROR');
      return;
    }

    if ($user_id) {
      $this->view->subject = $subject = Engine_Api::_()->getItem('user', $user_id);
    }
    else {
      $this->view->subject = $subject = $viewer;
    }

    $list = $this->_getParam('list', 'all');
    $api = Engine_Api::_()->like();

    $params = array('poster_type' => $subject->getType(), 'poster_id' => $subject->getIdentity());

    if ($list == 'mutual') {
      $params['mutual'] = $viewer->getIdentity();
    }

    $params['fetch'] = 'resource';
    $select = $api->getLikesSelect($params, $period_type);
    $select->where('like1.resource_type IN ("page", "user")');

    $rawData = $api->getTable()->fetchAll($select);
    $items = array();

    foreach ($rawData as $data) {
      $items[$data->resource_type][] = Engine_Api::_()->getItem($data->resource_type, $data->resource_id);
    }

    $this->view->items = $items;

    if ($this->_getParam('format') == 'json') {
      $this->view->html = $this->view->render('_composeItems.tpl');
    }
  }

  public function showMatchesAction()
  {
    $id = (int)array_pop(explode('_', $this->_getParam('id', '')));

    if (!$id) {
      $this->view->html = false;
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $api = Engine_Api::_()->like();

    $this->view->nophotoItems = array('blog', 'pageblog', 'classified', 'poll');
    $this->view->user = $user = Engine_Api::_()->getItem('user', $id);
    $this->view->showInterests = $showInterests = $user->authorization()->isAllowed($viewer, 'interest');

    $this->view->isSelf = $user->isSelf($viewer);
    if ($showInterests) {
      $this->view->paginator = $api->getMatchedItems($viewer, $id);
      $this->view->paginator->setItemCountPerPage(5);
    }
    else {
      $this->view->paginator = Zend_Paginator::factory(array());
    }

    $this->view->html = $this->view->render('_composeMatchHint.tpl');
  }

  public function showUserAction()
  {
    $id = (int)array_pop(explode('_', $this->_getParam('id', '')));

    if (!$id) {
      $this->view->html = false;
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $api = Engine_Api::_()->like();

    $this->view->user = $user = Engine_Api::_()->getItem('user', $id);
    $this->view->isSelf = $user->isSelf($viewer);
    $this->view->paginator = $api->getMutualFriends(array('user_id' => $id));
    $this->view->paginator->setItemCountPerPage(5);

    $this->view->html = $this->view->render('_composeFriendHint.tpl');
  }

  public function showContentAction()
  {
    $data = explode('_', $this->_getParam('id', ''));
    if (count($data) == 5) {
      $id = (int)array_pop($data);
      $temp = array_pop($data);
      $type = array_pop($data) . '_' . $temp;
    }
    else {
      $id = (int)array_pop($data);
      $type = array_pop($data);
    }
    if ($type == 'playlist') {
      $type = array_pop($data) . '_' . $type;
    }

    if ($type == 'liked_playlist') {
      $type = 'playlist';
    }

    if (!$id) {
      $this->view->html = false;
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $api = Engine_Api::_()->like();
    $this->view->item = $item = Engine_Api::_()->getItem($type, $id);

    $params = array(
      'resource_type' => $item->getType(),
      'resource_id' => $item->getIdentity(),
      'fetch' => 'user_id'
    );

    $select = $api->getLikesSelect($params);
    $userIds = $api->getTable()->getAdapter()->fetchCol($select);
    $this->view->displayFriends = false;

    if (!empty($userIds)) {
      $table = Engine_Api::_()->getItemTable('user');
      $name1 = $table->info('name');

      $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');
      $name2 = $membershipTable->info('name');

      $select = $table->select()
        ->setIntegrityCheck(false)
        ->from($name1, array($name1 . '.user_id'))
        ->joinLeft($name2, $name2 . '.user_id = ' . $name1 . '.user_id', array())
        ->where($name2 . '.resource_id = ?', $viewer->getIdentity())
        ->where($name2 . '.resource_approved = 1')
        ->where($name2 . '.user_approved = 1')
        ->where($name1 . '.user_id IN (' . implode(',', $userIds) . ')')
        ->limit(5);

      $frIds = $api->getTable()->getAdapter()->fetchCol($select);
      if (!empty($frIds)) {
        $this->view->displayFriends = true;
        $this->view->paginator = Zend_Paginator::factory(Engine_Api::_()->getItemMulti('user', $frIds));
      }
      else {
        $this->view->paginator = Zend_Paginator::factory(Engine_Api::_()->getItemMulti('user', $userIds));
      }
    }
    else {
      $this->view->paginator = Zend_Paginator::factory(array());
    }

    $this->view->paginator->setItemCountPerPage(5);
    $this->view->like_count = $api->getLikeCount($item);
    $this->view->html = $this->view->render('_composeContentHint.tpl');
  }

  public function seeMatchesAction()
  {
    $user_id = $this->_getParam('user_id');

    if (!$user_id) {
      $this->view->error = 1;
      $this->view->message = 'Error happened.';
      return;
    }

    $user = Engine_Api::_()->getItem('user', $user_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$user->authorization()->isAllowed($viewer, 'interest')) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => false,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('LIKE_AUTH_ERROR'))
      ));
      return;
    }

    $data = Engine_Api::_()->like()->getMatches($user);

    $this->view->items = $data['paginator'];
    $this->view->items->setItemCountPerPage(1000);
    $this->view->counts = $data['counts'];
  }

}
