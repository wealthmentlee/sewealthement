<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 12.06.12
 * Time: 14:29
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_PagemusicController
  extends Apptouch_Controller_Action_Bridge
{
  public function musicsBrowseAction()
  {
    if (!$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid()) return;

    //Get settings

    //Get Params
    $params = $this->_request->getParams();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $params['ipp'] = $settings->getSetting('pagemusic.page', 10);

    //Get paginator
    $paginator = Engine_Api::_()->getApi('core', 'pagemusic')->getMusicPaginator($params);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

//    $paginator->setItemCountPerPage(2);
    $this
      ->setPageTitle($this->view->translate('Everyone\'s Playlists'))
      ->addPageInfo('type', 'browse')
      ->add($this->component()->navigation('music_main', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ->add($this->component()->quickLinks('music_quick', true))
      ->add($this->component()->customComponent('itemList', $this->prepareBrowseList($paginator)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function musicsManageAction()
  {
    if (!$this->_helper->requireUser->isValid()) return;

    $params = $this->_request->getParams();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $params['ipp'] = $settings->getSetting('pagemusic.page', 10);
    $params['show'] = 3;
    $params['owner'] = Engine_Api::_()->user()->getViewer();

    //Get paginator
    $paginator = Engine_Api::_()->getApi('core', 'pagemusic')->getMusicPaginator($params);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
//    $paginator->setItemCountPerPage(2);

    $this
      ->setPageTitle($this->view->translate('My Playlists'))
      ->addPageInfo('type', 'manage')
      ->add($this->component()->navigation('music_main', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ->add($this->component()->quickLinks('music_quick', true))
      ->add($this->component()->customComponent('itemList', $this->prepareManageList($paginator)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  private function prepareBrowseList(Zend_Paginator $paginator)
  {
    $items = array();
    foreach ($paginator as $p_item) {
      $page_pref = '';

      if (!is_array($p_item))
        throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator');

      if ($p_item['type'] == 'page') {
        $page_pref = 'page';
        $item = Engine_Api::_()->getItem('playlist', $p_item['playlist_id']);
      } else {
        $item = Engine_Api::_()->getItem('music_playlist', $p_item['playlist_id']);
      }

      $owner = $item->getOwner();

      $std = array(
        'title' => $item->getTitle(),
        'descriptions' => array(
          $this->view->translate('Created by') . ' ' . $owner->getTitle()
        ),
        'photo' => $item->getPhotoUrl('thumb.normal'),
        'href' => $item->getHref(),
        'photo' => $item->getPhotoUrl('thumb.normal'),
        'creation_date' => $this->view->timestamp(strtotime($item->creation_date)),
        'owner_id' => $owner->getIdentity(),
        'owner' => $this->subject($owner)
      );

      if ($page_pref) {
        $std['descriptions'][] = $this->view->translate('On page ') . $this->view->htmlLink($item->getPage()->getHref(), $item->getPage()->getTitle());
        $std['counter'] = strtoupper($this->view->translate(array('pagemusic_%s track', 'pagemusic_%s tracks', $item->track_count), ($item->track_count)));
      } else {
        $std['counter'] = strtoupper($this->view->translate(array('%s track', '%s tracks', count($item->getSongs()->toArray())), $this->view->locale()->toNumber(count($item->getSongs()->toArray()))));
      }


      $items[] = $std;
    }

    $paginatorPages = $paginator->getPages();
    $component = array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',

      'items' => $items
    );
    $searchKeyword = $this->_getParam('search', false);
    if ($searchKeyword) {
      $component['search'] = array(
        'keyword' => $searchKeyword . '', // to string
        'count' => $paginator->getTotalItemCount(),
      );
    }

    return $component;
  }

  private function prepareManageList(Zend_Paginator $paginator)
  {
    $items = array();
    foreach ($paginator as $p_item) {
      $page_pref = '';

      if (!is_array($p_item))
        throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator');

      if ($p_item['type'] == 'page') {
        $page_pref = 'page';
        $item = Engine_Api::_()->getItem('playlist', $p_item['playlist_id']);
      } else {
        $item = Engine_Api::_()->getItem('music_playlist', $p_item['playlist_id']);
      }

      $owner = $item->getOwner();
      $options = array();
      if ($page_pref) {
        $options[] = array(
          'label' => $this->view->translate('Edit Playlist'),
          'attrs' => array(
            'href' => $item->getHref(),
            'class' => 'buttonlink icon_music_edit'
          ),
        );

        $options[] = array(
          'label' => $this->view->translate('Delete Playlist'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'delete', 'playlist_id' => $item->getIdentity()), 'page_musics', true),
            'class' => 'buttonlink smoothbox icon_music_delete'
          ),
        );
      } else {
        if ($item->isEditable()) {
          $options[] = array(
            'label' => $this->view->translate('Edit Playlist'),
            'attrs' => array(
              'href' => $item->getHref(array('route' => 'music_playlist_specific', 'action' => 'edit')),
              'class' => 'buttonlink icon_music_edit'
            ),
          );
        }
        if ($item->isDeletable()) {
          $options[] = array(
            'label' => $this->view->translate('Delete Playlist'),
            'attrs' => array(
              'href' => $this->view->url(array(
                'module' => 'music',
                'controller' => 'playlist',
                'action' => 'delete',
              ), 'default', true),
              'class' => 'buttonlink smoothbox icon_music_delete'
            ),
          );
        }
      }

      $std = array(
        'title' => $item->getTitle(),
        'descriptions' => array(
        ),
        'photo' => $owner->getPhotoUrl('thumb.normal'),
        'href' => $item->getHref(),
        'photo' => $item->getPhotoUrl('thumb.normal'),
        'creation_date' => $this->view->timestamp(strtotime($item->creation_date)),
        'owner_id' => null,
        'owner' => null,
        'manage' => $options
      );

      if ($page_pref) {
        $std['descriptions'][] = $this->view->translate('On page ') . $this->view->htmlLink($item->getPage()->getHref(), $item->getPage()->getTitle());
        $std['counter'] = strtoupper($this->view->translate(array('pagemusic_%s track', 'pagemusic_%s tracks', $item->track_count), ($item->track_count)));
      } else {
        $std['counter'] = strtoupper($this->view->translate(array('%s track', '%s tracks', count($item->getSongs()->toArray())), $this->view->locale()->toNumber(count($item->getSongs()->toArray()))));
      }


      $items[] = $std;
    }

    $paginatorPages = $paginator->getPages();
    $component = array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',

      'items' => $items
    );
    $searchKeyword = $this->_getParam('search', false);
    if ($searchKeyword) {
      $component['search'] = array(
        'keyword' => $searchKeyword . '', // to string
        'count' => $paginator->getTotalItemCount(),
      );
    }

    return $component;
  }

  public function indexInit()
  {
    $this->page_id = $page_id = $this->_getParam('page_id');
    if (!$page_id) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this->viewer = Engine_Api::_()->user()->getViewer();

    $this->pageObject = $page = Engine_Api::_()->getItem('page', $page_id);

    if (!$page) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this->isAllowedView = $this->getPageApi()->isAllowedView($page);

    if (!$this->isAllowedView) {
      $this->isAllowedPost = false;
      $this->isAllowedComment = false;
      return;
    }

    $this->isAllowedPost = $this->getApi()->isAllowedPost($page);
    $this->isAllowedComment = $this->getPageApi()->isAllowedComment($page);

    $this->addPageInfo('contentTheme', 'd');
  }

  public function indexIndexAction()
  {
    if (!$this->isAllowedView) {
      $this->view->error = 1;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
      return;
    }
    if ($this->pageObject)
      Engine_Api::_()->core()->setSubject($this->pageObject);

    $select = Engine_Api::_()->getDbTable('playlists', 'pagemusic')
          ->getSelect(array(
                'p' => $this->_getParam('page', 1),
                'ipp' => $this->_getParam('itemCountPerPage', 10),
                'page_id' => $this->pageObject->getIdentity()
              ));
    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);


    $this->add($this->component()->subjectPhoto($this->pageObject))
      ->add($this->component()->navigation('pagemusic', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ;

    if ($this->isAllowedPost)
      $this->add($this->component()->quickLinks('pagemusic_quick', true));

    $this->add($this->component()->itemList($paginator, 'browseItemList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function indexManageAction()
  {
    if (!$this->isAllowedView) {
      $this->view->error = 1;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
      return;
    }
    if ($this->pageObject)
      Engine_Api::_()->core()->setSubject($this->pageObject);
    $select = Engine_Api::_()->getDbTable('playlists', 'pagemusic')
          ->getSelect(array(
                'p' => $this->_getParam('page', 1),
                'ipp' => $this->_getParam('itemCountPerPage', 10),
                'page_id' => $this->pageObject->getIdentity(),
                'user_id' => $this->viewer->getIdentity()
              ));
    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);

    $this->add($this->component()->subjectPhoto($this->pageObject))
      ->add($this->component()->navigation('pagemusic', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
    ;

    if ($this->isAllowedPost)
      $this->add($this->component()->quickLinks('pagemusic_quick', true));

    $this->add($this->component()->itemList($paginator, 'manageMusictList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function indexCreateAction()
  {
    if (!$this->isAllowedPost)
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));

    $form = new Pagemusic_Form_Music();
    $form->removeAttrib('action');
    $form->removeElement('music_art');
    $form->removeElement('music_file');
    $form->addElement('File', 'music_art', array(
      'label' => 'pagemusic_Select Playlist Artwork',
      'order' => 3
    ));
    $form->addElement('File', 'music_file', array(
      'label' => 'pagemusic_Add Music',
      'order' => 4,
      'accept' => 'audio/*',
      'isArray' => true
    ));

    Engine_Api::_()->core()->setSubject($this->pageObject);
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->subjectPhoto($this->pageObject))
        ->add($this->component()->navigation('pagemusic', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->subjectPhoto($this->pageObject))
        ->add($this->component()->navigation('pagemusic', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    /**
     * @var $table Pagemusic_Model_DbTable_Playlists
     */
    $table = Engine_Api::_()->getDbTable('playlists', 'pagemusic');
    $db = $table->getAdapter();

    $db->beginTransaction();
    try {
      $values = $form->getValues();

      $playlist = $table->createRow();
      $playlist->page_id = $this->page_id;
      $playlist->owner_id = $this->viewer->getIdentity();
      $playlist->owner_type = 'user';
      $playlist->search = 1;
      $playlist->title = $values['music_title'];
      $playlist->description = $values['music_description'];
      $playlist->save();

      $tags = preg_split('/[,]+/', $values['music_tags']);
      if ($tags) {
        $playlist->tags()->setTagMaps($this->viewer, $tags);
      }

      $photo = $this->getPicupFiles('music_art');

      // Set photo
      if (!empty($values['music_art'])) {
        $playlist->setPhoto($form->music_art);
      } else if (!empty($photo)) {
        $photo = $photo[0];
        $playlist->setPhoto($photo);
      }

      $picupFiles = $this->getPicupFiles('music_file');
      if (empty($picupFiles))
        $files = $form->music_file->getFileName();
      else
        $files = $picupFiles;

      $songsTbl = Engine_Api::_()->getDbTable('songs', 'pagemusic');
      $params = array(
        'parent_type' => 'playlist',
        'parent_id' => $playlist->getIdentity()
      );

      $count = $playlist->track_count;

      if (is_array($files)) {
        foreach ($files as $file) {
          $song = Engine_Api::_()->getApi('core', 'pagemusic')->createSong($file, $params);
          $playlist->addSong($song);
          $count++;
          @unlink($file);
        }
      } else {
        $song = Engine_Api::_()->getApi('core', 'pagemusic')->createSong($files, $params);
        $playlist->addSong($song);
        $count++;
        @unlink($files);
      }

      $playlist->track_count = $count;
      $playlist->save();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->commit();

    return $this->redirect($playlist);
  }

  public function indexEditAction()
  {
    $playlist = Engine_Api::_()->getItem('playlist', $this->_getParam('playlist_id'));
    $user = $playlist->getOwner();
    $page = $playlist->getPage();

    $form = new Pagemusic_Form_Music();
    $form->removeElement('music_art');
    $form->removeElement('music_file');
    $form->setAction('');

    $form->music_title->setValue($playlist->title);
    $form->music_description->setValue($playlist->description);
    $tags = $playlist->tags()->getTagMaps();
    $tagString = '';
    foreach ($tags as $tagmap) {
      if ($tagString !== '') $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }
    $form->getElement('music_tags')->setValue($tagString);

    Engine_Api::_()->core()->setSubject($this->pageObject);
    if (!$this->getRequest()->isPost() || !$form->isValid($values = $this->getRequest()->getPost())) {
      $this->add($this->component()->subjectPhoto($playlist))
        ->add($this->component()->navigation('pagemusic', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $playlist->title = $values['music_title'];
    $playlist->description = $values['music_description'];

    $tags = preg_split('/[,]+/', $values['music_tags']);
    if ($tags) {
      $playlist->tags()->setTagMaps($user, $tags);
    }

    $playlist->save();

    return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'music'), 'page_view'));
  }

  public function indexDeleteAction()
  {
    $playlist = Engine_Api::_()->getItem('playlist', $this->_getParam('playlist_id'));
    $page = $playlist->getPage();

    $form = new Pagemusic_Form_Delete();

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $playlist->delete();
    return $this->redirect($this->view->url(array('module' => 'pagemusic', 'controller' => 'index', 'action' => 'manage', 'page_id' => $page->getIdentity()), 'default'), Zend_Registry::get('Zend_Translate')->_('pagemusic_Playlist was deleted.'), true);
  }

  public function browseItemList(Core_Model_Item_Abstract $item)
  {
    if (isset($item->photo_id)) {
      $photoUrl = $item->getPhotoUrl('thumb.normal');
    } else {
      $photoUrl = $item->getOwner()->getPhotoUrl('thumb.normal');
    }
    $customize_fields = array(
      'title' => $item->getTitle(),
      'descriptions' => array(
        $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date) . ' ' . $this->view->translate('By') . ' ' . $item->getOwner()->getTitle()
      ),
      'photo' => $photoUrl,
      'counter' => strtoupper($this->view->translate(array('%s track', '%s tracks', count($item->getSongs()->toArray())), $this->view->locale()->toNumber(count($item->getSongs()->toArray())))),
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageMusic Customizer Functions ---------------------------------
  public function manageMusictList(Core_Model_Item_Abstract $item)
  {
    $options = array();
    $page_id = $item->getPage()->getIdentity();

    $options[] = array(
      'label' => $this->view->translate('Edit'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'edit', 'page_id' => $page_id, 'playlist_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_music', true),
        'class' => 'buttonlink icon_album_edit'
      )
    );

    $options[] = array(
      'label' => $this->view->translate('Delete'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'delete', 'page_id' => $page_id, 'playlist_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_music', true),
        'class' => 'buttonlink smoothbox icon_album_delete',
      )
    );

    if (isset($item->photo_id)) {
      $photoUrl = $item->getPhotoUrl('thumb.normal');
    } else {
      $photoUrl = $item->getOwner()->getPhotoUrl('thumb.normal');
    }

    $customize_fields = array(
      'title' => $item->getTitle(),
      'descriptions' => array(
        $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date) . ' ' . $this->view->translate('By') . ' ' . $item->getOwner()->getTitle()
      ),
      'photo' => $photoUrl,
      'manage' => $options,
      'counter' => strtoupper($this->view->translate(array('%s track', '%s tracks', count($item->getSongs()->toArray())), $this->view->locale()->toNumber(count($item->getSongs()->toArray())))),
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageMusic Customizer Functions ---------------------------------

  protected function getApi()
  {
    return Engine_Api::_()->getApi('core', 'pagemusic');
  }

  protected function getPageApi()
  {
    return Engine_Api::_()->getApi('core', 'page');
  }
}
