<?php

class Advancedsearch_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->form = $form = new Advancedsearch_Form_Search();
    $this->view->types = $types = Engine_Api::_()->advancedsearch()->getAvailableTypes();

    if ($this->_getParam('squery')) {
      $this->view->squery = $this->_getParam('squery');
    }
    if ($this->_getParam('stype')) {
      $this->view->stype = $this->_getParam('stype');
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $iconTable = Engine_Api::_()->getDbTable('icons', 'advancedsearch');
    $itemIcons = $iconTable->select()
      ->from(array('i' => $iconTable->info('name')), array('item','icon'));
    $this->view->itemicons = $itemIcons = $db->fetchPairs($itemIcons);
  }
  public function searchAction()
  {
    $types = array(
      'album' => array(
        'album','pagealbum', 'pagealbumphoto', 'album_photo'
      ),
      'video' => array(
        'video', 'pagevideo'
      ),
      'music' => array(
        'song', 'playlist','music_playlist','music_playlist_song'
      ),
      'blog' => array(
        'blog', 'pageblog'
      ),
      'discussion' => array(
        'pagediscussion_pagepost','pagediscussion_pagetopic', 'forum_post', 'forum_topic'
      ),
      'review' => array(
        'pagereview', 'offerreview'
      ),
      'event' => array(
        'event', 'pageevent'
      )
    );
    $this->view->query = $text = $this->_getParam('query');
    $this->view->stype = $type = $this->_getParam('type');
    $page = intval($this->_getParam('page'));
    if (isset($types[$type])) {
      $type = $types[$type];
    }
    if ($this->_getParam('global')) {
      $this->view->global = true;
      $this->view->items = $items = Engine_Api::_()->advancedsearch()->getGlobalResult($text, $type);
    } else {
      $this->view->items = $items = Engine_Api::_()->advancedsearch()->getPaginator($text, $type, $page);
    }
    $this->view->html = $this->view->render('search.tpl');
  }
}
