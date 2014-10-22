<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: LikeController.php 2010-09-07 16:05 Ulan T $
 * @author     Ulan T
 */
class Apptouch_LikeController extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('like', 'json')
      ->addActionContext('unlike', 'json')
      ->initContext();


    $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject($viewer);
    }
  }

  public function indexLikeAction()
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

    if ($api->like($object)) {
      $api->addAction($object, $viewer);
      if ($object->getType() == 'page' && Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
        $api->notify($object, 'send');
      }

      $this->view->status = true;
    }
    else
    {
      $this->view->error = 1;
      $this->view->html = 'Error';
    }
  }

  public function indexUnlikeAction()
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

    if ($api->unlike($object)) {
      $api->deleteAction($object, $viewer);
      if ($object->getType() == 'page') {
        $api->notify($object, 'delete');
      }

      $this->view->status = true;
    }
    else
    {
      $this->view->error = 1;
      $this->view->html = Zend_Registry::get('Zend_Translate')->_('LIKE_UNDEFINED_ERROR');
    }
  }


  public function indexSeeLikedAction()
  {
    $user_id = (int) $this->_getParam('user_id');
    $period_type = $this->_getParam('period_type', 'all');
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('LIKE_LOGIN_ERROR');
      return;
    }

    if ($user_id) {
      $subject = Engine_Api::_()->getItem('user', $user_id);
    }
    else {
      $subject = $viewer;
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

    $this->add($this->component()->html($this->view->translate("like_%s's likes", $subject->getTitle())));
    $liked_items = array();
    $labels = Engine_Api::_()->like()->getSupportedModulesLabels();
    if ( count($items) > 0) {

      foreach ($items as $type => $data) {
        $element = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'c', 'class' => 'member_likes'));
        $title = $this->dom()->new_('h3', array(), $labels[$type]);
        $p_body = $this->dom()->new_('p', array());
        $body = $this->dom()->new_('ul', array('data-role' => 'listview'));
        foreach ($data as $item) {
          $item_body = $this->dom()->new_('li', array('class' => 'member_likes_body'));
          $a_el = $this->dom()->new_('a', array('href' => $item->getHref()));
          $item_photo = $this->dom()->new_('img', array('src' => $item->getPhotoUrl('thumb.normal')));
          $item_title = $this->dom()->new_('h3', array('class' => 'item_title'), $item->getTitle());
          $a_el->append($item_photo);
          $a_el->append($item_title);
          $item_body->append($a_el);
          $body->append($item_body);
        }

        $p_body->append($body);
        $element->append($title);
        $element->append($p_body);
        $liked_items[] = $element;
      }
    }

    $this->add($this->component()->html($liked_items))
      ->renderContent();
  }

//  Interests Controller {
  public function interestsInit()
  {
      $this->nophoto = Engine_Api::_()->like()->getNoPhotos();

      if ($this->_getParam('action') == 'suggest')
      {
          return;
      }

      if (!Engine_Api::_()->core()->hasSubject())
      {
          // Can specifiy custom id
          $id = $this->_getParam('id', null);
          $subject = null;
          if (null === $id)
          {
              $subject = $this->_helper->api()->user()->getViewer();
              $this->_helper->api()->core()->setSubject($subject);
          }
          else
          {
              $subject = $this->_helper->api()->user()->getUser($id);
              $this->_helper->api()->core()->setSubject($subject);
          }
      }

      if (!empty($id))
      {
          $params = array('params' => array('id' => $id));
      }
      else
      {
          $params = array();
      }

      // Set up navigation
      $this->navigation = $navigation = $this->_helper->api()
              ->getApi('menus', 'core')
              ->getNavigation('user_edit', array('params' => array('id' => $id)), 'user_edit_interests');

      // Set up require's
      $this->_helper->requireUser();
      $this->_helper->requireSubject('user');

      $this->viewer = Engine_Api::_()->user()->getViewer();

      $this->labels = Engine_Api::_()->like()->getInterestTypes();
      $this->icons = Engine_Api::_()->like()->getInterestIcons();

      $this->moduleApi = $moduleApi = Engine_Api::_()->getDbTable('modules', 'core');
  }

  public function interestsIndexAction()
  {
      $viewer = Engine_Api::_()->user()->getViewer();
      $api = Engine_Api::_()->like();
      $user_id = (int) $viewer->getIdentity();

      $interests = array_keys($this->view->labels);

      $table = $api->getTable();

      $manifest = Zend_Registry::get('Engine_Manifest');
      $itemTypes = array();
      foreach ($manifest as $man)
      {
          if (!isset($man['items']))
          {
              continue;
          }
          foreach ($man['items'] as $item)
          {
              $itemTypes[] = $item;
          }
      }
      $interests = array_intersect(is_array($interests) ? $interests : array(), $itemTypes);
      $availableLabels = array(
          'everyone' => 'Everyone',
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
          'owner' => 'Just Me'
      );

      $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $viewer, 'auth_interest');
      $this->view->viewOptions = $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if ($this->getRequest()->isPost())
      {
          $data = $this->_getParam('data');
          $view_interest = $this->_getParam('view_interest');

          $modules = array_keys($data);
          $where = implode('","', $modules);

          $db = $table->getAdapter();
          $db->beginTransaction();

          try
          {
              if (empty($view_interest))
              {
                  $view_interest = 'everyone';
              }
              $viewMax = array_search($view_interest, $roles);
              foreach ($roles as $i => $role)
              {
                  $auth->setAllowed($viewer, $role, 'interest', ($i <= $viewMax));
              }
              $table->delete('poster_type = "user" AND poster_id = ' . $user_id . ' AND resource_type IN ("' . $where . '")');
              foreach ($data as $key => $value)
              {
                  $ids = explode(',', $value);
                  if (!empty($ids))
                  {
                      foreach ($ids as $id)
                      {
                          $id = (int) $id;
                          if (!$id)
                          {
                              continue;
                          }
                          $toLikeData = array(
                              'resource_type' => $key,
                              'resource_id' => $id,
                              'poster_id' => $user_id,
                              'poster_type' => 'user',
                          );
                          $table->insert($toLikeData);
                      }
                  }
              }
              $db->commit();
          }
          catch (Exception $e)
          {
              $db->rollBack();
              throw $e;
          }
          $fake_data = $this->_getParam('fake_data');
          $fake_modules = array_keys($fake_data);
          $fake_modules[0] = "'" . $fake_modules[0];
          $last = count($fake_modules) - 1;
          $fake_modules[$last] = $fake_modules[$last] . "'";
//      foreach($fake_modules as &$value)
//      {
//        $value = "'".$value."'";
//      }

          $fake_where = implode("','", $fake_modules);
          /**
           * @var $fake_table Engine_Db_Table
           */
          $fake_table = Engine_Api::_()->getDbTable('likes', 'like');
          $fake_db = $fake_table->getAdapter();
          $fake_db->beginTransaction();
          try
          {
              $fake_table->delete('poster_type = "user" AND poster_id = ' . $user_id . ' AND resource_type IN (' . $fake_where . ')');
              foreach ($fake_data as $key => $value)
              {
                  $ids = explode(',', $value);
                  if (!empty($ids))
                  {
                      foreach ($ids as $id)
                      {
                          if (!$id)
                          {
                              continue;
                          }
                          $toLikeData = array(
                              'resource_type' => $key,
                              'resource_title' => $id,
                              'poster_id' => $user_id,
                              'poster_type' => 'user',
                          );
                          $fake_table->insert($toLikeData);
                      }
                  }
              }
              $fake_db->commit();
          }
          catch (Exception $e)
          {
              $fake_db->rollBack();
              throw $e;
          }

          $this->view->message = "like_Your changes has been saved.";
      }

      foreach ($roles as $role)
      {
          if ($auth->isAllowed($viewer, $role, 'interest'))
          {
              $this->view->privacyValue = $role;
          }
      }

      $this->view->items = array();
      foreach ($interests as $type)
      {
          $params = array('poster_type' => $viewer->getType(), 'poster_id' => $user_id, 'fetch' => 'resource', 'resource_type' => $type);
          $select = $api->getLikesSelect($params);
          $rawData = $select->query()->fetchAll();
          $rawData = array_merge($rawData, $api->getFakeLikes($params));
          if ($rawData)
          {
              $this->view->items[$type] = $rawData;
          }
      }
      $data = Engine_Api::_()->like()->getMatches($viewer);

      $this->view->subject = $viewer;
      $this->view->matches = $data['paginator'];
      $this->view->counts = $data['counts'];
      $this->view->matches->setItemCountPerPage(16);

      $this->view->paginators = array();
      $this->view->counts = array();
      foreach ($interests as $interest)
      {
          if ($interest == 'store_product')
          {
              $data = $this->getMostLiked($interest, 3);
          }
          else
          {
              $data = $api->getMostLikedData($interest, 3);
          }
          if ($data)
          {
              $this->view->paginators[$interest] = $data;
          }
      }
  }

  public function interestsSuggestAction()
  {
      $type = $this->_getParam('type');
      $text = $this->_getParam('text');
      $except = $this->_getParam('except');
      $viewer = Engine_Api::_()->user()->getViewer();
      $api = Engine_Api::_()->like();
      $nophoto = $this->view->nophoto;

      if ($type == 'store_product')
      {
          $data = $this->getProductsSuggest($type, $text, $except, $viewer);

          if (!count($data))
          {
              //Select fake products
              $img = $this->view->baseUrl() . $nophoto[$type];
              $fake_except = $this->_getParam('fake_except');

              if (!in_array($text, $fake_except))
              {
                  $params = array('resource_type' => $type, 'title_like' => $text, 'poster_type' => $viewer->getType(), 'poster_id' => $viewer->getIdentity());
                  $fake_likes = $api->getFakeLikes($params);
                  foreach ($fake_likes as $item_like)
                  {
                      $foo = array('id' => $item_like['resource_type'] . '_' . $item_like['resource_title'], 'label' => $item_like['resource_title'], 'img' => $img, 'href' => '');
                      $data[] = $foo;
                  }
                  if (!count($data))
                  {
                      $data[] = array('id' => $type . '_' . $text, 'label' => $text, 'img' => $img, 'href' => '');
                  }
              }
          }

          return $this->_helper->json($data);
      }

      $type2 = $this->_getParam('type');

      $table = Engine_Api::_()->getItemTable($type);
      $primary = $table->info('primary');
      //$page_type = isset($this->_itemTypesPage[$type])?;
      $page_type = null;
      if (isset($this->_itemTypesPage[$type]))
      {
          $page_type = $this->_itemTypesPage[$type];
      }
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $all = 0;
      if ($page_type)
      {
          $all = $settings->getSetting($this->_itemTypesPageSettings[$page_type], 0);
      }
      if ($type == 'music_playlist')
      {
          $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('music');
          $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('pagemusic');
          $type2 = 'playlist';
      }
      elseif ($type == 'group')
      {
          $module = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('group')
                  ? 'group'
                  : 'advgroup';
              $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule($module);
      }
      elseif ($type == 'artarticle')
      {
          $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('advancedarticles');
      }
      elseif ($type == 'avp_video')
      {
          $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('avp');
          $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('pagevideo');
          $type2 = 'video';
      }
      elseif ($type == 'album_photo')
      {
          $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('album');
          $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('pagealbum');
      }
      elseif ($type == 'list_listing')
      {
          $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('list');
          $type2 = 'listing';
      }
      else
      {
          $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule($type);
          $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule($page_type);
      }
      $page = Engine_Api::_()->getDbTable('modules', 'core')->getModule('page');


      if ($module && $module->enabled)
      {
          $title = $type == 'document' ? 'document_title' : 'title';
          $select = $table->select()
                  ->from(array('tablename' => $table->info('name')), array($type2 . '_id', new Zend_Db_Expr("'not_page' as 'type'")))
                  ->where($title . ' LIKE "%' . $text . '%"');

          if (!empty($except))
          {
              $except2 = implode(',', $except);
              $select
                      ->where($primary[1] . " NOT IN (" . $except2 . ")");
          }
      }
      if ($page_type && $all && $page && $page->enabled && $page_module && $page_module->enabled)
      {
          $authallowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
          $listitemTbl = Engine_Api::_()->getItemTable('page_list_item');

          $page_except = $this->_getParam('page_except');
          $table_page_module = Engine_Api::_()->getItemTable($page_type);
          $page_type_id = 'pe.' . $page_type . '_id';
          $primary_page = $table_page_module->info('primary');
          if ($page_type == 'pagealbumphoto')
          {
              $table_album = Engine_Api::_()->getItemTable('pagealbum');
              $select_in_page = $table_page_module->select()
                      ->from(array('pe' => $table_page_module->info('name')), array($type . '_id' => $page_type . '_id', new Zend_Db_Expr("'page' as 'type'")))
                      ->joinLeft(array('pagealbum' => $table_album->info('name')), "pe.collection_id = pagealbum.pagealbum_id", array())
                      ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_id = pagealbum.page_id", array())
                      ->joinLeft(array('li' => $listitemTbl->info('name')), "li.list_id = a.role_id", array())
                      ->where("a.resource_type = 'page' AND a.action = 'view' AND (a.role = 'everyone' OR a.role = 'registered OR li.child_id=?')", $viewer->getIdentity());
          }
          else
          {
              $select_in_page = $table_page_module->select()
                      ->from(array('pe' => $table_page_module->info('name')), array($type . '_id' => $page_type . '_id', new Zend_Db_Expr("'page' as 'type'")))
                      ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_id = pe.page_id", array())
                      ->joinLeft(array('li' => $listitemTbl->info('name')), "li.list_id = a.role_id", array())
                      ->where("a.resource_type = 'page' AND a.action = 'view' AND (a.role = 'everyone' OR a.role = 'registered' OR li.child_id=?)", $viewer->getIdentity());
          }
          if ($type == 'event')
          {
              $select_in_page
                      ->joinLeft(array('aa' => $authallowTbl->info('name')), "aa.resource_id = {$page_type_id}", array())
                      ->joinLeft(array('lii' => $listitemTbl->info('name')), "lii.list_id = aa.role_id", array())
                      ->where("aa.action = 'view' AND (aa.role = 'everyone' OR aa.role = 'registered' OR lii.child_id=?)", $viewer->getIdentity())
                      ->where('aa.resource_type = ?', $page_type);
          }
          $select_in_page->where('title LIKE "%' . $text . '%"');
          if (!empty($page_except))
          {
              $page_except = implode(',', $page_except);
              $select_in_page
                      ->where($primary_page[1] . " NOT IN (" . $page_except . ")");
          }
          $select_in_page->group($page_type_id);
      }

      if ($page_type && $all && $page && $page->enabled && $page_module && $page_module->enabled && $select_in_page && $select)
      {
          $select = Engine_Db_Table::getDefaultAdapter()->select()->union(array($select, $select_in_page));
      }

      $select->limit(10);


      $this->_helper->layout->disableLayout();
      $data = array();

      $types = array('page', 'event', 'group');


      foreach ($select->query()->fetchAll() as $item_like)
      {
          if ($item_like['type'] == 'page')
          {
              $item = Engine_Api::_()->getItem($page_type, $item_like[$type2 . '_id']);
          }
          else
          {
              $item = Engine_Api::_()->getItem($type, $item_like[$type2 . '_id']);
          }
          if (!$item->authorization()->isAllowed($viewer, 'view'))
          {
              continue;
          }

          if (in_array($item->getType(), $types) && !Engine_Api::_()->like()->isAllowed($item))
          {
              continue;
          }

          $img = $item->getPhotoUrl('thumb.icon');
          if (!$img)
          {
              $img = $this->view->baseUrl() . $nophoto[$item->getType()];
          }

          $foo = array('id' => $item->getGuid(), 'label' => $item->getTitle(), 'img' => $img, 'href' => $item->getHref());
          $data[] = $foo;
      }

      if (!count($data))
      {
          $img = $this->view->baseUrl() . $nophoto[$type];
          $fake_except = $this->_getParam('fake_except');

          if (!in_array($text, $fake_except))
          {
              $params = array('resource_type' => $type, 'title_like' => $text, 'poster_type' => $viewer->getType(), 'poster_id' => $viewer->getIdentity());
              $fake_likes = $api->getFakeLikes($params);
              foreach ($fake_likes as $item_like)
              {
                  $foo = array('id' => $item_like['resource_type'] . '_' . $item_like['resource_title'], 'label' => $item_like['resource_title'], 'img' => $img, 'href' => '');
                  $data[] = $foo;
              }
              if (!count($data))
              {
                  $data[] = array('id' => $type . '_' . $text, 'label' => $text, 'img' => $img, 'href' => '');
              }
          }
      }
      return $this->_helper->json($data);
  }

  protected function getMostLiked($type, $limit = null, $period = 'all')
  {
      if (!$type)
      {
          return false;
      }

      /**
       * @var $productsTbl Store_Model_DbTable_Products
       * @var $table Core_Model_DbTable_Likes
       * @var $select Zend_Db_Table_Select;
       */
      $productsTbl = Engine_Api::_()->getDbTable('products', 'store');
      $table = Engine_Api::_()->getDbTable('likes', 'core');
      $prefix = $table->getTablePrefix();

      $select = $table->select()
              ->from(array('like' => $table->info('name')), array('resource_id', 'resource_type', 'creation_date', 'like_count' => 'COUNT(*)'))
              ->joinLeft($prefix . 'store_products', 'like.resource_id = ' . $prefix . 'store_products.product_id', array())
              ->order('like_count DESC')
              ->group('like.resource_id');

      if ($limit)
      {
          $select->limit($limit);
      }

      if ($period == 'month')
      {
          $select->where('`like`.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
      }
      elseif ($period == 'week')
      {
          $select->where('`like`.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
      }

      $select = $productsTbl->setStoreIntegrity($select);

      $select
              ->where('like.resource_type = ?', $type);


      return $select->query()->fetchAll();
  }

  protected function getProductsSuggest($type, $text, $except, $viewer)
  {
      $table = Engine_Api::_()->getItemTable($type);
      $primary = $table->info('primary');

      $select = $table->select()
              ->where('title LIKE "%' . $text . '%"')
              ->limit(10);

      if (!empty($except))
      {
          $except = implode(',', $except);
          $select
                  ->where($primary[1] . " NOT IN (" . $except . ")");
      }

      $this->_helper->layout->disableLayout();
      $items = $table->fetchAll($select);
      $data = array();

      $nophoto = $this->view->nophoto;
      $types = array('page', 'event', 'group');

      foreach ($items as $item)
      {
          if (!$item->authorization()->isAllowed($viewer, 'view'))
          {
              continue;
          }

          if (in_array($item->getType(), $types) && !Engine_Api::_()->like()->isAllowed($item))
          {
              continue;
          }

          $img = $item->getPhotoUrl('thumb.icon');
          if (!$img)
          {
              $img = $this->view->baseUrl() . $nophoto[$item->getType()];
          }

          $foo = array('id' => $item->getGuid(), 'label' => $item->getTitle(), 'img' => $img, 'href' => $item->getHref());
          $data[] = $foo;
      }
      return $data;
  }
//  } Interests Controller

}
