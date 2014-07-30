<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Like_Api_Core extends Core_Api_Abstract
{

  protected $_itemTypes = array('album', 'list_listing', 'document', 'artarticle', 'avp_video',
    'blog', 'album_photo', 'video', 'classified', 'classified_photo', 'music_playlist',
    'group_photo', 'event_photo', 'quiz', 'poll', 'offers');
  protected $_itemTypesNotPage = array(
    'pagealbum' => 'album',
    'pageevent' => 'event',
    'playlist' => 'music_playlist',
    'pageblog' => 'blog',
    'pagelisting' => 'listing',
    'pagealbumphoto' => 'album_photo',
    'pagevideo' => 'video'
  );
  protected $_itemTypesPage = array(
    'event' => 'pageevent',
    'music_playlist' => 'playlist',
    'blog' => 'pageblog',
    'avp_video' => 'pagevideo',
    'album_photo' => 'pagealbumphoto',
    'video' => 'pagevideo'
  );
  protected $_itemTypesPageSettings = array(
    'pageevent' => 'page.browse.pageevent',
    'playlist' => 'page.browse.pagemusic',
    'pageblog' => 'page.browse.pageblog',
    'pagelisting' => 'page.browse.pagelisting',
    'pagealbumphoto' => 'page.browse.pagealbum',
    'pagevideo' => 'page.browse.pagevideo',
  );
  protected $_itemTypesLabel = array(
    'page' => 'Pages',
    'event' => 'Events',
    'event_photo' => 'Events Photos',
    'classified' => 'Classifieds',
    'group' => 'Groups',
    'advgroup' => 'Groups',
    'group_photo' => 'Groups Photos',
    'user' => 'Members',
    'music_playlist' => 'Music Playlists',
    'blog' => 'Blogs',
    'document' => 'Documents',
    'list_listing' => 'Listing',
    'video' => 'Videos',
    'avp_video' => 'Videos',
    'album' => 'Albums',
    'album_photo' => 'Photos',
    'pagealbum' => 'Page Albums',
    'quiz' => 'Quizzes',
    'poll' => 'Polls',
    'pagereview' => 'Page Reviews',
    'store_product' => 'Store Products',
    'article' => 'Articles',
    'artarticle' => 'Articles',
    'offers' => 'Offer'
  );
  protected $_itemTypesIcons = array(
    'page' => '/application/modules/Like/externals/images/icons/page.png',
    'event' => '/application/modules/Like/externals/images/icons/event.png',
    'event_photo' => '/application/modules/Like/externals/images/icons/photo.png',
    'classified' => '/application/modules/Like/externals/images/icons/classified.png',
    'group' => '/application/modules/Like/externals/images/icons/group.png',
    'advgroup' => '/application/modules/Like/externals/images/icons/group.png',
    'group_photo' => '/application/modules/Like/externals/images/icons/photo.png',
    'user' => '/application/modules/Like/externals/images/icons/user.png',
    'music_playlist' => '/application/modules/Like/externals/images/icons/music_playlist.png',
    'blog' => '/application/modules/Like/externals/images/icons/blog.png',
    'document' => '/application/modules/Like/externals/images/icons/document.png',
    'list_listing' => '/application/modules/Like/externals/images/icons/listing.png',
    'video' => '/application/modules/Like/externals/images/icons/video.png',
    'avp_video' => '/application/modules/Like/externals/images/icons/video.png',
    'album' => '/application/modules/Like/externals/images/icons/album.png',
    'album_photo' => '/application/modules/Like/externals/images/icons/photo.png',
    'pageevent' => '/application/modules/Like/externals/images/icons/event.png',
    'pageblog' => '/application/modules/Like/externals/images/icons/blog.png',
    'pagelisting' => '/application/modules/Like/externals/images/icons/listing.png',
    'playlist' => '/application/modules/Like/externals/images/icons/music_playlist.png',
    'pagealbum' => '/application/modules/Like/externals/images/icons/album.png',
    'pagealbumphoto' => '/application/modules/Like/externals/images/icons/photo.png',
    'pagevideo' => '/application/modules/Like/externals/images/icons/video.png',
    'quiz' => '/application/modules/Like/externals/images/icons/quiz.png',
    'poll' => '/application/modules/Like/externals/images/icons/poll.png',
    'pagereview' => '/application/modules/Like/externals/images/icons/review.png',
    'store_product' => '/application/modules/Like/externals/images/icons/store_product.png',
    'article' => '/application/modules/Like/externals/images/icons/article.png',
    'artarticle' => '/application/modules/Like/externals/images/icons/article.png',
    'offers' => '/application/modules/Like/externals/images/icons/offer.png'
  );
  protected $_interestTypes = array(
    'page' => 'Pages',
    'event' => 'Events',
    'classified' => 'Classifieds',
    'group' => 'Groups',
    'advgroup' => 'Groups',
    'music_playlist' => 'Music',
    'blog' => 'Blogs',
    'document' => 'Documents',
    'list_listing' => 'Listings',
    'video' => 'Videos',
    'avp_video' => 'Videos',
    'album' => 'Albums',
    'quiz' => 'Quizzes',
    'poll' => 'Polls',
    'store_product' => 'Store Products',
    'article' => 'Articles',
    'artarticle' => 'Articles',
    'offers' => 'Offer'
  );
  protected $_interestIcons = array(
    'page' => '/application/modules/Like/externals/images/icons/page.png',
    'event' => '/application/modules/Like/externals/images/icons/event.png',
    'classified' => '/application/modules/Like/externals/images/icons/classified.png',
    'group' => '/application/modules/Like/externals/images/icons/group.png',
    'advgroup' => '/application/modules/Like/externals/images/icons/group.png',
    'blog' => '/application/modules/Like/externals/images/icons/blog.png',
    'document' => '/application/modules/Like/externals/images/icons/document.png',
    'list_listing' => '/application/modules/Like/externals/images/icons/listing.png',
    'video' => '/application/modules/Like/externals/images/icons/video.png',
    'avp_video' => '/application/modules/Like/externals/images/icons/video.png',
    'album' => '/application/modules/Like/externals/images/icons/album.png',
    'quiz' => '/application/modules/Like/externals/images/icons/quiz.png',
    'poll' => '/application/modules/Like/externals/images/icons/poll.png',
    'store_product' => '/application/modules/Like/externals/images/icons/store_product.png',
    'article' => '/application/modules/Like/externals/images/icons/blog.png',
    'offers' => 'application/modules/Like/externals/images/icons/offer.png'
  );
  protected $_nophotos = array(
    'blog' => '/application/modules/Like/externals/images/nophoto/blog.png',
    'document' => '/application/modules/Like/externals/images/nophoto/document.png',
    'pageblog' => '/application/modules/Like/externals/images/nophoto/blog.png',
    'list_listing' => '/application/modules/Like/externals/images/nophoto/listing.png',
    'page' => '/application/modules/Like/externals/images/nophoto/page.png',
    'event' => '/application/modules/Like/externals/images/nophoto/event.png',
    'pageevent' => '/application/modules/Like/externals/images/nophoto/event.png',
    'group' => '/application/modules/Like/externals/images/nophoto/group.png',
    'classified' => '/application/modules/Like/externals/images/nophoto/classified.png',
    'album' => '/application/modules/Like/externals/images/nophoto/album.png',
    'pagealbum' => '/application/modules/Like/externals/images/nophoto/album.png',
    'video' => '/application/modules/Like/externals/images/nophoto/video.png',
    'pagevideo' => '/application/modules/Like/externals/images/nophoto/video.png',
    'music_playlist' => '/application/modules/Like/externals/images/nophoto/music.png',
    'playlist' => '/application/modules/Like/externals/images/nophoto/music.png',
    'quiz' => '/application/modules/Like/externals/images/nophoto/quiz.png',
    'poll' => '/application/modules/Like/externals/images/nophoto/poll.png',
    'pagereview' => '/application/modules/Like/externals/images/nophoto/pagereview.png',
    'store_product' => '/application/modules/Like/externals/images/nophoto/store_product.png',
    'article' => '/application/modules/Like/externals/images/nophoto/blog.png',
    'offers' => '/application/modules/Like/externals/images/nophoto/offer.png',
  );
  protected $_pluginLoader;

  public function getSupportedModules()
  {
    return $this->_itemTypes;
  }

  public function getNoPhotos()
  {
    return $this->_nophotos;
  }

  public function getSupportedModulesLabels()
  {
    return $this->_itemTypesLabel;
  }

  public function getSupportedModulesIcons()
  {
    return $this->_itemTypesIcons;
  }

  public function getInterestTypes()
  {
    return $this->_interestTypes;
  }

  public function getInterestIcons()
  {
    return $this->_interestIcons;
  }

  public function getPluginLoader()
  {
    if (null === $this->_pluginLoader) {
      $path = Engine_Api::_()->getModuleBootstrap('like')->getModulePath();
      $this->_pluginLoader = new Zend_Loader_PluginLoader(array(
        'Like_Model_Helper_' => $path . '/Model/Helper/'
      ));
    }

    return $this->_pluginLoader;
  }

  public function getNormalItemType($page_type)
  {
    if(isset($this->_itemTypesNotPage[$page_type])){
      return $this->_itemTypesNotPage[$page_type];
    }
    return $page_type;
  }

  public function isPageType($type)
  {
    if (isset($this->_itemTypesPage[$type])) {
      return $this->_itemTypesPage[$type];
    }
    return null;
  }

  public function removePhotos()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $file_id = $settings->getSetting('like.logo');

    if (isset($file_id) && $file_id != 0) {
      $storage = Engine_Api::_()->storage();
      $file = $storage->get($file_id);
      if ($file !== null)
        $file->remove();
    }
  }

  public function getLogo()
  {
    $this->view->base_url = $baseUrl = '';

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $storage = Engine_Api::_()->storage();

    $file_id = $settings->getSetting('like.logo');

    if ($file_id) {
      $file = $storage->get($file_id);
      if ($file) {
        return $baseUrl . $file->map();
      }
      else {
        return '';
      }
    }
    else {
      return '';
    }
  }

  public function getActionContent($object)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $api = Engine_Api::_()->like();
    $db = $api->getTable()->getAdapter();

    $params = array('resource_type' => $object->getType(), 'resource_id' => $object->getIdentity(), 'fetch' => 'user_id');
    $select = $api->getLikesSelect($params);

    $user_ids = $db->fetchCol($select);
    $item_ids = array();
    $counter = 0;
    foreach ($user_ids as $id) {
      $item_ids[] = $id;
      $counter++;
      if ($counter == 5) {
        break;
      }
    }
    if (!empty($item_ids)) {
      $users = Engine_Api::_()->getItemMulti('user', $item_ids);
    }
    else {
      $users = array();
    }

    $likeCount = (int)$api->getLikeCount($object);

    $view = Zend_Registry::get('Zend_View');
    $html = $view->partial('_composeLikeAction.tpl', 'like', array(
      'subject' => $viewer,
      'users' => $users,
      'user_ids' => $user_ids,
      'object' => $object,
      'likeCount' => $likeCount
    ));

    return str_replace(array("\n", "\t", "\r"), "", trim($html));
  }

  public function getActionContentPrivate(Core_Model_Item_Abstract $object)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $view = Zend_Registry::get('Zend_View');
    $html = $view->partial('_composeLikeActionPrivate.tpl', 'like', array(
      'subject' => $viewer,
      'object' => $object
    ));

    return str_replace(array("\n", "\t", "\r"), "", trim($html));
  }

  public function addAction($object, $user = null)
  {
    if ($user === null) {
      $user = Engine_Api::_()->user()->getViewer();
    }

    if (is_numeric($user)) {
      $user = Engine_Api::_()->getItem('user', $user);
    }

    if (!($user instanceof User_Model_User) || !$user->getIdentity()) {
      return null;
    }

    if (is_string($object)) {
      $object = Engine_Api::_()->getItemByGuid($object);
    }

    if (!($object instanceof Core_Model_Item_Abstract) || !$object->getIdentity()) {
      return null;
    }

    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $oldAction = $this->getAction($object);

      $actionType = 'like_item';
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($user, $object, $actionType, '', array('content' => $this->getActionContent($object)));
      if($action){
        $action->date = date('Y-m-d H:i:s');
        $action->save();

        if ($oldAction) {
          $this->updateActionStaff($oldAction, $action);
          $oldAction->deleteItem();
        }
      }
      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $action;
  }

  public function notify($item, $task)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($task == 'send') {
      Engine_Api::_()->page()->sendNotification($item, 'page_like');
    }
    elseif ($task == 'delete') {
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      $select = $notifyApi->select()
        ->where('subject_id = ?', $viewer->getIdentity())
        ->where('object_type = ?', 'page')
        ->where('object_id = ?', $item->getIdentity())
        ->where('type = ?', 'page_like');

      $notifications = $notifyApi->fetchAll($select);
      foreach ($notifications as $notification) {
        $notification->delete();
      }
    }
  }

  public function updateActionStaff(Core_Model_Item_Abstract $oldAction, Core_Model_Item_Abstract $newAction)
  {
    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $commentsApi = Engine_Api::_()->getDbTable('comments', 'activity');
      $likesApi = Engine_Api::_()->getDbTable('likes', 'activity');

      if ($oldAction->getIdentity() && $newAction->getIdentity()) {
        $newAction->comment_count = $oldAction->comment_count;
        $newAction->like_count = $oldAction->like_count;

        $commentsApi->update(array('resource_id' => $newAction->action_id), array('resource_id = ?' => $oldAction->action_id));
        $likesApi->update(array('resource_id' => $newAction->action_id), array('resource_id = ?' => $oldAction->action_id));

        $newAction->save();
      }

      $db->commit();
    }
    catch (Exception $e) {

      $db->rollBack();
      throw $e;
    }
  }

  public function deleteAction($object)
  {
    if (is_string($object)) {
      $object = Engine_Api::_()->getItemByGuid($object);
    }

    if (!($object instanceof Core_Model_Item_Abstract) || !$object->getIdentity()) {
      return null;
    }

    $this->addAction($object);
  }

  public function isAllowed(Core_Model_Item_Abstract $subject)
  {
    $api = Engine_Api::_()->getDbTable('permissions', 'authorization');
    $action = 'like_' . $subject->getType();
    if ($action == 'like_store_product') {
      $action = 'like_product';
    }
    $owner = $subject->getOwner();
    return $api->isAllowed($owner, $owner, $action);
  }

  public function getAction($object)
  {
    if (is_string($object)) {
      $object = Engine_Api::_()->getItemByGuid($object);
    }

    if (!($object instanceof Core_Model_Item_Abstract) || !$object->getIdentity()) {
      return null;
    }

    $actionType = 'like_item';
    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

    $select = $activityApi->select()
      ->where('object_type = ?', $object->getType())
      ->where('object_id = ?', $object->getIdentity())
      ->where('type = ?', $actionType);

    $action = $activityApi->fetchRow($select);

    return $action;
  }

  public function setPhoto($photo)
  {
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
    }
    else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
    }
    else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
    }
    else {
      throw new Event_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_id' => (int)rand(0, 10000),
      'parent_type' => 'like'
    );

    // Remove photos
    $this->removePhotos();

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    if ($image->height != 16 && $image->width != 16) {

      $size = min($image->height, $image->width);
      $x = ($image->width - $size) / 2;
      $y = ($image->height - $size) / 2;

      $image->resample($x, $y, $size, $size, 16, 16)
        ->write($path . '/i_' . $name)
        ->destroy();

      $filename = $path . '/i_' . $name;
    }
    else {
      $filename = $file;
    }

    // Store
    $iIcon = $storage->create($filename, $params);

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->setSetting('like.logo', $iIcon->file_id);

    return $iIcon;
  }

  public function isLike($object, $user = null)
  {
    if ($user === null) {
      $user = Engine_Api::_()->user()->getViewer();
    }

    if (is_numeric($user)) {
      $user = Engine_Api::_()->getItem('user', $user);
    }

    if (!($user instanceof User_Model_User) || !$user->getIdentity()) {
      return null;
    }

    if (is_string($object)) {
      $object = Engine_Api::_()->getItemByGuid($object);
    }

    if (!($object instanceof Core_Model_Item_Abstract) || !$object->getIdentity()) {
      return null;
    }

    $table = Engine_Api::_()->getDbTable('likes', 'core');
    return (bool)$table->isLike($object, $user);
  }

  public function like($object, $user = null)
  {
    if ($user === null) {
      $user = Engine_Api::_()->user()->getViewer();
    }

    if (is_numeric($user)) {
      $user = Engine_Api::_()->getItem('user', $user);
    }

    if (!($user instanceof User_Model_User) || !$user->getIdentity()) {
      return null;
    }

    if (is_string($object)) {
      $object = Engine_Api::_()->getItemByGuid($object);
    }

    if (!($object instanceof Core_Model_Item_Abstract) || !$object->getIdentity()) {
      return null;
    }

    $table = Engine_Api::_()->getDbTable('likes', 'core');
    $table->addLike($object, $user);

    if (!$user->isSelf($object)) {
      $actionType = 'like_item_private';
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $privateAction = $activityApi->addActivity($user, $object, $actionType, '', array('content' => $this->getActionContentPrivate($object)));
    }

    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('like', array(
      'object' => $object,
      'user' => $user
    ));

    return true;
  }

  public function unlike($object, $user = null)
  {
    if ($user === null) {
      $user = Engine_Api::_()->user()->getViewer();
    }

    if (is_numeric($user)) {
      $user = Engine_Api::_()->getItem('user', $user);
    }

    if (!($user instanceof User_Model_User) || !$user->getIdentity()) {
      return null;
    }

    if (is_string($object)) {
      $object = Engine_Api::_()->getItemByGuid($object);
    }

    if (!($object instanceof Core_Model_Item_Abstract) || !$object->getIdentity()) {
      return null;
    }

    $table = Engine_Api::_()->getDbTable('likes', 'core');
    $table->removeLike($object, $user);

    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('unlike', array(
      'object' => $object,
      'user' => $user
    ));

    if (!$user->isSelf($object)) {
      $actionType = 'like_item_private';
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

      $select = $activityApi->select()
        ->where('object_type = ?', $object->getType())
        ->where('object_id = ?', $object->getIdentity())
        ->where('type = ?', $actionType);

      $action = $activityApi->fetchRow($select);
      if ($action) {
        $action->deleteItem();
      }
    }

    return true;
  }

  public function getLikesSelect($params, $period = 'all')
  {
    $userTable = Engine_Api::_()->getItemTable('user');
    $UTname = $userTable->info('name');

    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
    $LTname = $likeTable->info('name');

    $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');
    $MTname = $membershipTable->info('name');

    $select = $likeTable->select()
      ->setIntegrityCheck(false);

    if (!empty($params['fetch'])) {
      if ($params['fetch'] == 'user_id') {
        $select
          ->from(array('like1' => $LTname), array())
          ->joinLeft(array('user' => $UTname), 'user.user_id = like1.poster_id', array('user.user_id'));
      }
      elseif ($params['fetch'] == 'resource') {
        $select
          ->from(array('like1' => $LTname), array('like1.resource_type', 'like1.resource_id'))
          ->joinLeft(array('user' => $UTname), 'user.user_id = like1.poster_id', array());
      }
      else {
        $select
          ->from(array('like1' => $LTname), array())
          ->joinLeft(array('user' => $UTname), 'user.user_id = like1.poster_id');
      }
      if ($period == 'month') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
      }
      elseif ($period == 'week') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
      }
    }
    else {
      $select
        ->from(array('like1' => $LTname), array())
        ->joinLeft(array('user' => $UTname), 'user.user_id = like1.poster_id');
      if ($period == 'month') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
      }
      elseif ($period == 'week') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
      }
    }

    if (!empty($params['keyword'])) {
      $select
        ->where("user.username LIKE '%{$params['keyword']}%' OR user.displayname LIKE '%{$params['keyword']}%'");
    }

    if (!empty($params['mutual'])) {
      $select
        ->joinLeft(array('like2' => $LTname), 'like1.resource_id = like2.resource_id AND like1.resource_type = like2.resource_type', array())
        ->where('like2.poster_id = ?', $params['mutual'])
        ->where('like2.poster_type = ?', 'user');
      if ($period == 'month') {
        $select->where('like2.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
      }
      elseif ($period == 'week') {
        $select->where('like2.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
      }
    }

    if (!empty($params['poster_type'])) {
      $select
        ->where('like1.poster_type = ?', $params['poster_type']);
      if ($period == 'month') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
      }
      elseif ($period == 'week') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
      }
    }

    if (!empty($params['poster_id'])) {
      $select
        ->where('like1.poster_id = ?', $params['poster_id']);
      if ($period == 'month') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
      }
      elseif ($period == 'week') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
      }
    }

    if (!empty($params['resource_type'])) {
      if (is_array($params['resource_type'])) {
        $where = 'like1.resource_type IN ("' . implode('","', $params['resource_type']) . '")';
        $select
          ->where($where);
      }
      else {
        $viewer = Engine_Api::_()->user()->getViewer();
        $page_type = $this->isPageType($params['resource_type']);

        if ($page_type) {
          $all = Engine_Api::_()->getApi('settings', 'core')->getSetting($this->_itemTypesPageSettings[$page_type], 0);
        }

        $page = Engine_Api::_()->getDbTable('modules', 'core')->getModule('page');
        if ($page_type == 'playlist') {
          $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('pagemusic');
        }
        else {
          $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule($page_type);
        }

        if ($page_type && empty($params['resource_id'])) {
          if ($all && $page && $page->enabled && $page_module && $page_module->enabled) {
            $authallowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
            $listitemTbl = Engine_Api::_()->getItemTable('page_list_item');

            $select_page_module = clone $select;
            $table_page_module = Engine_Api::_()->getItemTable($page_type);
            $page_type_id = 'pe.' . $page_type . '_id';
            if ($page_type == 'pagealbumphoto') {
              $table_album = Engine_Api::_()->getItemTable('pagealbum');
              $select_in_page = $table_page_module->select()
                ->from(array('pe' => $table_page_module->info('name')), array($page_type . '_id'))
                ->joinLeft(array('pagealbum' => $table_album->info('name')), "pe.collection_id = pagealbum.pagealbum_id", array())
                ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_id = pagealbum.page_id", array())
                ->joinLeft(array('li' => $listitemTbl->info('name')), "li.list_id = a.role_id", array())
                ->where("a.resource_type = 'page' AND a.action = 'view' AND (a.role = 'everyone' OR a.role = 'registered OR li.child_id=?')", $viewer->getIdentity());
            }
            else {
              $select_in_page = $table_page_module->select()
                ->from(array('pe' => $table_page_module->info('name')), array($page_type . '_id'))
                ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_id = pe.page_id", array())
                ->joinLeft(array('li' => $listitemTbl->info('name')), "li.list_id = a.role_id", array())
                ->where("a.resource_type = 'page' AND a.action = 'view' AND (a.role = 'everyone' OR a.role = 'registered' OR li.child_id=?)", $viewer->getIdentity());
            }
            if ($page_type == 'pageevent') {
              $select_in_page
                ->joinLeft(array('aa' => $authallowTbl->info('name')), "aa.resource_id = {$page_type_id}", array())
                ->joinLeft(array('lii' => $listitemTbl->info('name')), "lii.list_id = aa.role_id", array())
                ->where("aa.action = 'view' AND (aa.role = 'everyone' OR aa.role = 'registered' OR lii.child_id=?)", $viewer->getIdentity())
                ->where('aa.resource_type = ?', $page_type);
            }
            $select_in_page->group($page_type_id);

            $select_page_module
              ->where('resource_type=?', $page_type)
              ->where('resource_id in(?)', $select_in_page)
              ->group('resource_id');
          }
        }

        $select
          ->where('like1.resource_type = ?', $params['resource_type']);

        if ($page_type && $all && $page && $page->enabled && $page_module && $page_module->enabled && $select_page_module) {
          $select = Engine_Db_Table::getDefaultAdapter()->select()->union(array($select, $select_page_module));
        }
      }
      if ($period == 'month') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
      }
      elseif ($period == 'week') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
      }
    }
    if (!empty($params['resource_id'])) {
      $select
        ->where('like1.resource_id = ?', $params['resource_id']);
      if ($period == 'month') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
      }
      elseif ($period == 'week') {
        $select->where('like1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
      }
    }

    if (!empty($params['friend_id'])) {
      $select
        ->joinInner(array('membership' => $MTname), 'membership.user_id = user.user_id', array())
        ->where('membership.resource_id = ?', $params['friend_id'])
        ->where('membership.resource_approved = 1')
        ->where('membership.user_approved = 1');
    }

    if (!empty($params['limit'])) {
      $select
        ->limit($params['limit']);
    }
    return $select;
  }

  public function getTable()
  {
    return Engine_Api::_()->getDbTable('likes', 'core');
  }

  public function getMatches(User_Model_User $subject)
  {
    $uTable = Engine_Api::_()->getItemTable('user');
    $UTname = $uTable->info('name');

    $lTable = Engine_Api::_()->getDbTable('likes', 'core');
    $LTname = $lTable->info('name');

    $innerSelect = $lTable->select()
      ->setIntegrityCheck(false)
      ->from($LTname, array('resource_type', 'resource_id'))
      ->where('poster_id = ?', $subject->getIdentity());

    $select = $lTable->select()
      ->setIntegrityCheck(false)
      ->from(array('like1' => $LTname), array('count' => 'COUNT(*)', 'poster_id'))
      ->joinInner(array('tmp' => $innerSelect), 'tmp.resource_type = like1.resource_type')
      ->where('tmp.resource_id = like1.resource_id')
      ->where('like1.poster_id <> ?', $subject->getIdentity())
      ->group('like1.poster_id');

    $db = $uTable->getAdapter();

    $all = $db->fetchAll($select);
    $counts = array();
    $user_ids = array();
    foreach ($all as $data) {
      $user_id = $data['poster_id'];
      $counts[$user_id] = $data['count'];
      $user_ids[] = $user_id;
    }

    if (!empty($user_ids)) {
      $select = $uTable->select()->where('user_id IN (' . implode(',', $user_ids) . ')');
      return array('paginator' => Zend_Paginator::factory($select), 'counts' => $counts);
    }
    else {
      return array('paginator' => Zend_Paginator::factory(array()), 'counts' => $counts);
    }
  }

  public function getMatchedItems(User_Model_User $subject, $user_id)
  {
    $uTable = Engine_Api::_()->getItemTable('user');
    $UTname = $uTable->info('name');

    $lTable = Engine_Api::_()->getDbTable('likes', 'core');
    $LTname = $lTable->info('name');

    $innerSelect = $lTable->select()
      ->setIntegrityCheck(false)
      ->from($LTname, array('resource_type', 'resource_id'))
      ->where('poster_id = ?', $subject->getIdentity());

    $select = $lTable->select()
      ->setIntegrityCheck(false)
      ->from(array('like1' => $LTname))
      ->joinInner(array('tmp' => $innerSelect), 'tmp.resource_type = like1.resource_type', array())
      ->where('tmp.resource_id = like1.resource_id')
      ->where('like1.poster_id = ?', $user_id)
      ->where('like1.resource_type <> ?', 'activity_comment');

    $db = $uTable->getAdapter();

    $all = $db->fetchAll($select);
    $items = array();
    foreach ($all as $data) {
      $items[] = Engine_Api::_()->getItem($data['resource_type'], $data['resource_id']);
    }

    return Zend_Paginator::factory($items);
  }

  public function getLikes($object, $period = 'all')
  {
    if (is_string($object)) {
      $object = Engine_Api::_()->getItemByGuid($object);
    }
    elseif (is_array($object)) {
      $viewer = Engine_Api::_()->user()->getViewer();

      $params = array(
        'resource_type' => $object['object'],
        'resource_id' => $object['object_id'],
        'period_type' => isset($object['period_type']) ? $object['period_type'] : null
      );

      $period = $params['period_type'];
      if (isset($object['list_type']) && $object['list_type'] === 'mutual') {
        $params['friend_id'] = $viewer->getIdentity();
      }

      if (!empty($object['keyword'])) {
        $params['keyword'] = $object['keyword'];
      }
      $params['fetch'] = 'user_id';

      $select = $this->getLikesSelect($params, $period);
      $db = $this->getTable()->getAdapter();
      $user_ids = $db->fetchCol($select);

      return Zend_Paginator::factory(Engine_Api::_()->getItemMulti('user', $user_ids));
    }

    if (!($object instanceof Core_Model_Item_Abstract) || !$object->getIdentity()) {
      return null;
    }
    $select = $this->getAllLikesUsers($object, $period);
    return Zend_Paginator::factory($select);
  }

  public function getLikeCount($object)
  {
    if (is_string($object)) {
      $object = Engine_Api::_()->getItemByGuid($object);
    }

    if (!($object instanceof Core_Model_Item_Abstract) || !$object->getIdentity()) {
      return null;
    }

    $table = Engine_Api::_()->getDbTable('likes', 'core');
    return $table->getLikeCount($object);
  }

  public function getLikesCount($object, array $object_ids)
  {
    if (empty($object_ids)) {
      return false;
    }

    $table = Engine_Api::_()->getDbTable('likes', 'core');
    $name = $table->info('name');
    $resource_ids = implode(",", $object_ids);

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($name, array('object_id' => $name . '.resource_id', 'count' => 'COUNT(*)'))
      ->where($name . '.resource_type = ?', $object)
      ->where($name . '.resource_id IN (' . $resource_ids . ')')
      ->group($name . '.resource_id');

    $db = $table->getAdapter();
    return $db->fetchPairs($select);
  }

  public function getToday()
  {
    $timestamp = time();
    $today = getdate($timestamp);
    $month = $today['mon'];
    $mday = $today['mday'];
    $year = $today['year'];
    return date('Y-m-d H:i:s', mktime(0, 0, 0, $month, $mday, $year));
  }

  public function getFriends($params)
  {
    if (!empty($params['user_id'])) {
      $user_id = $params['user_id'];
    }
    else {
      $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }

    if (!empty($params['list_type']) && $params['list_type'] == 'mutual') {
      return $this->getMutualFriends($params);
    }

    $table = Engine_Api::_()->getItemTable('user');
    $name1 = $table->info('name');

    $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');
    $name2 = $membershipTable->info('name');

    $select = $table->select()
      ->setIntegrityCheck(false);

    if (!empty($params['fetch'])) {
      if ($params['fetch'] == 'user_id') {
        $select
          ->from($name1, array($name1 . '.user_id'));
      }
    }
    else {
      $select
        ->from($name1);
    }

    $select
      ->joinLeft($name2, $name2 . '.user_id = ' . $name1 . '.user_id', array())
      ->where($name2 . '.resource_id = ?', $user_id)
      ->where($name2 . '.resource_approved = 1')
      ->where($name2 . '.user_approved = 1');

    if (!empty($params['fetch'])) {
      if ($params['fetch'] == 'user_id') {
        return $table->getAdapter()->fetchCol($select);
      }
    }
    else {
      return Zend_Paginator::factory($select);
    }
  }

  public function getMutualFriends($params)
  {
    if (!empty($params['user_id'])) {
      $user_id = $params['user_id'];
    }

    if (!empty($params['list_type']) && $params['list_type'] == 'all') {
      return $this->getFriends($params);
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
    $friendsName = $friendsTable->info('name');

    $select = new Zend_Db_Select($friendsTable->getAdapter());
    $select
      ->from($friendsName, 'user_id')
      ->join($friendsName, "`{$friendsName}`.`user_id`=`{$friendsName}_2`.user_id", null)
      ->where("`{$friendsName}`.resource_id = ?", $viewer->getIdentity())
      ->where("`{$friendsName}_2`.resource_id = ?", $user_id)
      ->where("`{$friendsName}`.active = ?", 1)
      ->where("`{$friendsName}_2`.active = ?", 1);

    $uids = array();
    foreach ($select->query()->fetchAll() as $data) {
      $uids[] = $data['user_id'];
    }

    if (count($uids) > 0) {
      $UTable = Engine_Api::_()->getItemTable('user');
      $select = $UTable->select()
        ->where('user_id IN(?)', $uids);
      $paginator = Zend_Paginator::factory($select);
    }
    else {
      $paginator = Zend_Paginator::factory(array());
    }

    return $paginator;
  }

  public function getLikedItems($user = null, $categorized = false, $period = 'all')
  {
    if ($user === null) {
      $user = Engine_Api::_()->user()->getViewer();
    }

    if (is_numeric($user)) {
      $user = Engine_Api::_()->getItem('user', $user);
    }

    if (!($user instanceof User_Model_User) || !$user->getIdentity()) {
      return null;
    }
    $itemTypes = array_keys($this->getSupportedModulesLabels());
    $manifest = Zend_Registry::get('Engine_Manifest');
    $items = array();
    foreach ($manifest as $man) {
      if (!isset($man['items'])) {
        continue;
      }
      foreach ($man['items'] as $item) {
        $items[] = $item;
      }
    }
    $itemTypes = array_intersect($itemTypes, $items);
    $table = $this->getTable();
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->where('poster_type = ?', $user->getType())
      ->where('poster_id = ?', $user->getIdentity())
      ->where('resource_type IN ("' . implode('","', $itemTypes) . '")');
    if ($period == 'month') {
      $select->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
    }
    elseif ($period == 'week') {
      $select->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
    }

    //Select page modules
    $itemTypesPage = array_values($this->_itemTypesPage);
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $page = Engine_Api::_()->getDbTable('modules', 'core')->getModule('page');
    if ($page && $page->enabled) {
      $authallowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
      $listitemTbl = Engine_Api::_()->getItemTable('page_list_item');
    }

    foreach ($itemTypesPage as $page_type) {
      $all = $settings->getSetting($this->_itemTypesPageSettings[$page_type], 0);
      if ($page_type == 'playlist') {
        $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('pagemusic');
      }
      elseif ($page_type == 'pagealbumphoto') {
        $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('pagealbum');
      }
      else {
        $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule($page_type);
      }
      if ($all && $page && $page->enabled && $page_module && $page_module->enabled) {
        $table_page_module = Engine_Api::_()->getItemTable($page_type);
        $page_type_id = 'pe.' . $page_type . '_id';
        if ($page_type == 'pagealbumphoto') {
          $table_album = Engine_Api::_()->getItemTable('pagealbum');
          $select_in_page = $table_page_module->select()
            ->from(array('pe' => $table_page_module->info('name')), array($page_type . '_id'))
            ->joinLeft(array('pagealbum' => $table_album->info('name')), "pe.collection_id = pagealbum.pagealbum_id", array())
            ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_id = pagealbum.page_id", array())
            ->joinLeft(array('li' => $listitemTbl->info('name')), "li.list_id = a.role_id", array())
            ->where("a.resource_type = 'page' AND a.action = 'view' AND (a.role = 'everyone' OR a.role = 'registered OR li.child_id=?')", $user->getIdentity());
        }
        else {
          $select_in_page = $table_page_module->select()
            ->from(array('pe' => $table_page_module->info('name')), array($page_type . '_id'))
            ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_id = pe.page_id", array())
            ->joinLeft(array('li' => $listitemTbl->info('name')), "li.list_id = a.role_id", array())
            ->where("a.resource_type = 'page' AND a.action = 'view' AND (a.role = 'everyone' OR a.role = 'registered' OR li.child_id=?)", $user->getIdentity());
        }
        if ($page_type == 'pageevent') {
          $select_in_page
            ->joinLeft(array('aa' => $authallowTbl->info('name')), "aa.resource_id = {$page_type_id}", array())
            ->joinLeft(array('lii' => $listitemTbl->info('name')), "lii.list_id = aa.role_id", array())
            ->where("aa.action = 'view' AND (aa.role = 'everyone' OR aa.role = 'registered' OR lii.child_id=?)", $user->getIdentity())
            ->where('aa.resource_type = ?', $page_type);
        }
        $select_in_page->group($page_type_id);

        $select_page_module = $table->select()
          ->setIntegrityCheck(false)
          ->where('poster_type = ?', $user->getType())
          ->where('poster_id = ?', $user->getIdentity())
          ->where('resource_type=?', $page_type)
          ->where('resource_id in(?)', $select_in_page)
          ->group('resource_id');

        if ($period == 'month') {
          $select_page_module->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
        }
        else if ($period == 'week') {
          $select_page_module->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
        }

        if ($select_page_module) {
          $select = Engine_Db_Table::getDefaultAdapter()->select()->union(array($select, $select_page_module));
        }
        $select_page_module = null;
        $select_in_page = null;
      }
    }
    $rawData = $select->query()->fetchAll();
    $items = array();
    foreach ($rawData as $data) {
      switch ($data['resource_type']) {
        case 'avp_video' :
          $module = 'avp';
          break;
        case 'group' :
          {
          $module = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('group') ? 'group' : 'advgroup';
          }
          break;
        case 'list_listing' :
          $module = 'list';
          break;
        case 'artarticle' :
          $module = 'advancedarticles';
          break;
        default :
          $module = $data['resource_type'];
          break;
      }
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($module)) {
        if ($categorized) {
          //$type - convert page type to normal type
          $type = $this->getNormalItemType($data['resource_type']);
          $items[$type][] = Engine_Api::_()->getItem($data['resource_type'], $data['resource_id']);
        }
        else {
          $items[] = Engine_Api::_()->getItem($data['resource_type'], $data['resource_id']);
        }
      }
    }
    return $items;
  }

  public function getLikedCount($user = null, $period = 'all')
  {
    if ($user === null) {
      $user = Engine_Api::_()->user()->getViewer();
    }

    if (is_numeric($user)) {
      $user = Engine_Api::_()->getItem('user', $user);
    }

    if (!($user instanceof User_Model_User) || !$user->getIdentity()) {
      return null;
    }

    $itemTypes = array_keys($this->getSupportedModulesLabels());
    $table = Engine_Api::_()->getDbTable('likes', 'core');
    $name = $table->info('name');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($name, array('count' => 'COUNT(*)'))
      ->where('poster_type = ?', 'user')
      ->where('poster_id = ?', $user->getIdentity())
      ->where('resource_type IN ("' . implode('","', $itemTypes) . '")');

    if ($period == 'month') {
      $select->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
    }
    elseif ($period == 'week') {
      $select->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
    }
    $db = $table->getAdapter();

    return $db->fetchOne($select);
  }

  public function getMostLiked($type, $limit = null, $period = 'all')
  {
    if (!$type) {
      return false;
    }

    $table = Engine_Api::_()->getDbTable('likes', 'core');
    $name = $table->info('name');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('like' => $name), array('resource_id', 'like_count' => 'COUNT(*)'))
      ->where('resource_type = ?', $type)
      ->order('like_count DESC')
      ->group('resource_id');

    if ($limit) {
      $select->limit($limit);
    }

    if ($period == 'month') {
      $select->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
    }
    elseif ($period == 'week') {
      $select->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
    }

    $rawData = $table->getAdapter()->fetchPairs($select);
    $ids = array_keys($rawData);

    if (!empty($ids)) {
      return array(
        'paginator' => Zend_Paginator::factory(Engine_Api::_()->getItemMulti($type, $ids)),
        'counts' => $rawData
      );
    }
    else {
      if ($period == 'all') {
        return false;
      }
      else {
        return array(
          'paginator' => Zend_Paginator::factory(Engine_Api::_()->getItemMulti($type, $ids)),
          'counts' => $rawData
        );
      }
    }
  }

  public function getAllLikesUsers(Core_Model_Item_Abstract $resource, $period = 'all')
  {
    $likes = new Core_Model_DbTable_Likes();
    $table = $likes->getLikeTable();
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), array('poster_type', 'poster_id'))
      ->where('resource_type = ?', $resource->getType())
      ->where('resource_id = ?', $resource->getIdentity());

    if ($period == 'month') {
      $select->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
    }
    elseif ($period == 'week') {
      $select->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
    }

    $users = array();
    foreach ($select->query()->fetchAll() as $data) {
      if ($data['poster_type'] == 'user') {
        $users[] = $data['poster_id'];
      }
    }
    $users = array_values(array_unique($users));

    return Engine_Api::_()->getItemMulti('user', $users);
  }

  public function getMostLikedData($type, $limit = null, $period = 'all')
  {
    if (!$type) {
      return false;
    }
    $page_type = $this->isPageType($type);
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $all = 0;
    if ($page_type) {
      $all = $settings->getSetting($this->_itemTypesPageSettings[$page_type], 0);
    }
    switch ($type) {
      case 'group' :
        {

        $module = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('group')
          ? Engine_Api::_()->getDbTable('modules', 'core')->getModule('group')
          : Engine_Api::_()->getDbTable('modules', 'core')->getModule('advgroup');
        }
        break;
      case 'avp_video' :
        {
        $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('avp');
        $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('pagevideo');
        }
        break;
      case 'music_playlist' :
        {
        $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('music');
        $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('pagemusic');
        }
        break;

      case 'album_photo' :
        {
        $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('album');
        $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('pagealbum');
        }
        break;

      case 'list_listing' :
        {
        $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('list');
        }
        break;
      case 'artarticle' :
        {
        $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('advancedarticles');
        }
        break;

      default :
        {
        $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule($type);
        $page_module = Engine_Api::_()->getDbTable('modules', 'core')->getModule($page_type);
        }
        break;
    }

    $page = Engine_Api::_()->getDbTable('modules', 'core')->getModule('page');
    $table_like = Engine_Api::_()->getDbTable('likes', 'core');

    //$table_like = Engine_Api::_()->getItemTable('likes');
    $union_select = null;
    if ($module && $module->enabled) {
      $select_module = $table_like->select()
        ->from(array('like' => $table_like->info('name')), array('resource_id', 'resource_type', 'creation_date', 'like_count' => 'COUNT(*)'))
        ->where('resource_type=?', $type)
        ->group('resource_id');

      if ($period == 'month') {
        $select_module->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
      }
      else if ($period == 'week') {
        $select_module->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
      }
      $union_select = $select_module;
    }
    if ($page_type && $all && $page && $page->enabled && $page_module && $page_module->enabled) {
      $authallowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
      $listitemTbl = Engine_Api::_()->getItemTable('page_list_item');

      $table_page_module = Engine_Api::_()->getItemTable($page_type);
      $page_type_id = 'pe.' . $page_type . '_id';
      if ($page_type == 'pagealbumphoto') {
        $table_album = Engine_Api::_()->getItemTable('pagealbum');
        $select_in_page = $table_page_module->select()
          ->from(array('pe' => $table_page_module->info('name')), array($page_type . '_id'))
          ->joinLeft(array('pagealbum' => $table_album->info('name')), "pe.collection_id = pagealbum.pagealbum_id", array())
          ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_id = pagealbum.page_id", array())
          ->joinLeft(array('li' => $listitemTbl->info('name')), "li.list_id = a.role_id", array())
          ->where("a.resource_type = 'page' AND a.action = 'view' AND (a.role = 'everyone' OR a.role = 'registered OR li.child_id=?')", $viewer->getIdentity());
      }
      else {
        $select_in_page = $table_page_module->select()
          ->from(array('pe' => $table_page_module->info('name')), array($page_type . '_id'))
          ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_id = pe.page_id", array())
          ->joinLeft(array('li' => $listitemTbl->info('name')), "li.list_id = a.role_id", array())
          ->where("a.resource_type = 'page' AND a.action = 'view' AND (a.role = 'everyone' OR a.role = 'registered' OR li.child_id=?)", $viewer->getIdentity());
      }
      if ($type == 'event') {
        $select_in_page
          ->joinLeft(array('aa' => $authallowTbl->info('name')), "aa.resource_id = {$page_type_id}", array())
          ->joinLeft(array('lii' => $listitemTbl->info('name')), "lii.list_id = aa.role_id", array())
          ->where("aa.action = 'view' AND (aa.role = 'everyone' OR aa.role = 'registered' OR lii.child_id=?)", $viewer->getIdentity())
          ->where('aa.resource_type = ?', $page_type);
      }
      $select_in_page->group($page_type_id);

      $select_page_module = $table_like->select()
        ->from(array('like' => $table_like->info('name')), array('resource_id', 'resource_type', 'creation_date', 'like_count' => 'COUNT(*)'))
        ->where('resource_type=?', $page_type)
        ->where('resource_id in(?)', $select_in_page)
        ->group('resource_id');

      if ($period == 'month') {
        $select_page_module->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
      }
      else if ($period == 'week') {
        $select_page_module->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
      }
      $union_select = $select_page_module;
    }

    if ($page_type && $all && $page && $page->enabled && $page_module && $page_module->enabled && $select_page_module && $select_module) {
      $union_select = Engine_Db_Table::getDefaultAdapter()->select()->union(array($select_module, $select_page_module));
    }
    if ($union_select) {
      $union_select->order('like_count DESC');
      if ($limit) {
        $union_select->limit($limit);
      }
      return $union_select->query()->fetchAll();
    }
    return null;
  }

  public function getFakeLikes($params)
  {
    /**
     * @var $table Engine_Db_Table
     */
    $table = Engine_Api::_()->getDbTable('likes', 'like');

    $select = $table->select();

    if (!empty($params['resource_types'])) {
      $select
        ->where('resource_type IN (?)', $params['resource_types']);
    }

    if (!empty($params['resource_type'])) {
      $select
        ->where('resource_type = ?', $params['resource_type']);
    }

    if (!empty($params['poster_type'])) {
      $select
        ->where('poster_type = ?', $params['poster_type']);
    }

    if (!empty($params['poster_id'])) {
      $select
        ->where('poster_id = ?', $params['poster_id']);
    }

    if (!empty($params['title_like'])) {
      $select
        ->where('resource_title LIKE "%' . $params['title_like'] . '%"');
    }
    if (!empty($params['limit'])) {
      $select
        ->limit($params['limit']);
    }
    return $select->query()->fetchAll();
  }

}