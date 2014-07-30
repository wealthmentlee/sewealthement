<?php
/***/
class Advancedsearch_Api_Core extends Core_Api_Abstract
{
  public function getAvailableTypes()
  {
    $types = Engine_Api::_()->getDbtable('search', 'core')->getAdapter()
      ->query('SELECT DISTINCT `type` FROM `engine4_core_search`')
      ->fetchAll(Zend_Db::FETCH_COLUMN);
    $types = array_intersect($types, Engine_Api::_()->getItemTypes());
    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagediscussion') || Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('forum')) {
      $types['discussion'] = 'discussion';
    }

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagemusic') || Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('music')) {
      $types['music'] = 'music';
    }

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate') && (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page') || Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('offer'))) {
      $types['review'] = 'review';
    }

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('blog') && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pageblog')) {
      $types['blog'] = 'blog';
    }

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('video') && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagevideo')) {
      $types['video'] = 'video';
    }
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event') && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pageevent')) {
      $types['event'] = 'event';
    }
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('album') && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagealbum')) {
      $types['album'] = 'album';
    }

    $list = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedsearch.typeslist');
    $viewList = explode(',', $list);
    $types = array_intersect($types, $viewList);
    $excludeList = array(
      'music_playlist','pagereview', 'offerreview','music_playlist_song', 'pagealbum', 'pagealbumphoto', 'album_photo', 'pagevideo', 'song', 'playlist', 'pageblog', 'pagediscussion_pagepost', 'pagediscussion_pagetopic', 'pageevent', 'forum_post', 'forum_topic'
    );

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page')) {
      $excludeList[] = 'pagereview';
    }
    foreach ($types as $key => &$type) {
      if (in_array($type, $excludeList)) {
        unset($types[$key]);
      }
    }
    return $types;
  }

  public function getAvailableTypesAdmin()
  {
    $types = Engine_Api::_()->getDbtable('search', 'core')->getAdapter()
      ->query('SELECT DISTINCT `type` FROM `engine4_core_search`')
      ->fetchAll(Zend_Db::FETCH_COLUMN);
    $types = array_intersect($types, Engine_Api::_()->getItemTypes());
    $excludeList = array(
      'pagereview', 'offerreview','music_playlist','music_playlist_song', 'pagealbum', 'pagealbumphoto', 'album_photo', 'pagevideo', 'song', 'playlist', 'pageblog', 'pagediscussion_pagepost', 'pagediscussion_pagetopic', 'pageevent', 'forum_post', 'forum_topic'
    );
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page')) {
      $excludeList[] = 'pagereview';
    }
    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagediscussion') || Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('forum')) {
      $types['discussion'] = 'discussion';
    }

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate') && (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page') || Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('offer'))) {
      $types['review'] = 'review';
    }

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagemusic') || Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('music')) {
      $types['music'] = 'music';
    }

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('blog') && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pageblog')) {
      $types['blog'] = 'blog';
    }

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('video') && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagevideo')) {
      $types['video'] = 'video';
    }
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event') && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pageevent')) {
      $types['event'] = 'event';
    }
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('album') && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagealbum')) {
      $types['album'] = 'album';
    }
    foreach ($types as $key => &$type) {
      if (in_array($type, $excludeList)) {
        unset($types[$key]);
      }
    }
    return $types;
  }

  public function getPaginator($text, $type = null, $page = false)
  {
    $paginator = Zend_Paginator::factory($this->getSelect($text, $type, $page));
    if ($page) {
      $paginator->setCurrentPageNumber($page);
    }
    $paginator->setItemCountPerPage(15);
    if ($paginator->getTotalItemCount() <= 15 * ($page - 1))
      return false;
    return $paginator;
  }

  public function getSelect($text, $type = null, $page = 1)
  {
    // Build base query
    $table = Engine_Api::_()->getDbtable('search', 'core');
    $db = $table->getAdapter();
    $select = $table->select();
//      ->where(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (? IN BOOLEAN MODE)', $text)))

    $availableTypes = Engine_Api::_()->getItemTypes();

    if ($type && in_array($type, $availableTypes)) {
      $select->where('type = ?', $type);
    } else if (is_array($type)) {
      $select->where('type IN(?)', $type);
    } else {
      $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedsearch.typeslist');
      $select->where('type IN(?)', explode(',', $settings));
    }
    $select->where("(`title` LIKE  '%$text%' OR `description` LIKE  '%$text%' OR `keywords` LIKE  '%$text%' OR `hidden` LIKE  '%$text%')");
    $select->order(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (?) DESC', $text)));
    return $select;
  }

  public function getSelectGlobal($text, $type = null, $page = 1)
  {
    /**
     * @var $table Core_Model_DbTable_Search
     */
    $table = Engine_Api::_()->getDbtable('search', 'core');
    $db = $table->getAdapter();
    $select = $table->select();
//      ->where(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (? IN BOOLEAN MODE)', $text)))

    $availableTypes = Engine_Api::_()->getItemTypes();
    if ($type && in_array($type, $availableTypes)) {
      $select->where('type = ?', $type);
    } else if (is_array($type)) {
      $select->where('type IN(?)', $type);
    } else {
      $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedsearch.typeslist');
      $select->where('type IN(?)', explode(',', $settings));
    }
    $select->where("(`title` LIKE  '%$text%' OR `description` LIKE  '%$text%' OR `keywords` LIKE  '%$text%' OR `hidden` LIKE  '%$text%')");

    $select ->order(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (?) DESC', $text)))
      ->limit(5, ($page - 1) * 5);
    return $db->fetchAll($select);
  }

  public function getGlobalResult($text, $type)
  {
    $page = 1;
    $i = 0;
    $foundResults = array();
    for ($j = 0; true; $j++) {
      $i = $this->getNumSelect($i, $page, $text, $foundResults, $type);
      if (!$i || intval($i) >= 5) {
        break;
      }
      $page++;
    }
    return $foundResults;
  }

  public function getNumSelect($i, $page, $text, &$foundResults, $type = null)
  {
    $results = $this->getSelectGlobal($text, $type, $page);
    if (!$results) return false;
    foreach ($results as $result) {
      if (Engine_Api::_()->hasItemType($result['type'])) {
        $item = Engine_Api::_()->getItem($result['type'], $result['id']);
        if ($item == '') {
          $this->deleteItem($result['type'], $result['id']);
          continue;
        } else {
          $i++;
          $foundResults[] = $result;
          if ($i == 5) break;
        }
      }
    }
    return $i;
  }

  public function deleteItem($type, $id)
  {
    if ($type !='' && intval($id) > 0) {
      $table = Engine_Api::_()->getDbtable('search', 'core');
      $table->delete("type='$type' AND id=$id");
    }
  }
}