<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 12.06.12
 * Time: 14:29
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_PagealbumController
  extends Apptouch_Controller_Action_Bridge
{
  protected $params;

  protected $pageObject;

  protected $album;

  public function albumsBrowseAction()
  {
    if (!$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid()) return;

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params = $this->_request->getParams();
    $page = $this->_getParam('page');
    $params['ipp'] = $settings->getSetting('pagealbum.page', 10);

    $paginator = Engine_Api::_()->getApi('core', 'pagealbum')->getAlbumPaginator($params);
    $paginator->setCurrentPageNumber($page);

    $this
      ->setPageTitle($this->view->translate('Browse Albums'))
      ->addPageInfo('type', 'browse')
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ->add($this->component()->navigation('album_main', true), -1)
      ->add($this->component()->quickLinks('album_quick', true))
      ->add($this->component()->customComponent('itemList', $this->prepareBrowseList($paginator)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function albumsManageAction()
  {
    if (!$this->_helper->requireUser->isValid()) return;

    // Get Settings
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $params = $this->_request->getParams();
    $params['view'] = 3;
    $params['owner'] = Engine_Api::_()->user()->getViewer();
    $params['ipp'] = $settings->getSetting('pagealbum.page', 10);

    $paginator = Engine_Api::_()->getApi('core', 'pagealbum')->getAlbumPaginator($params);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));


    $this
      ->setPageTitle($this->view->translate('Manage Albums'))
      ->addPageInfo('type', 'manage')
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ->add($this->component()->navigation('album_main', true), -1)
      ->add($this->component()->quickLinks('album_quick', true))
      ->add($this->component()->customComponent('itemList', $this->prepareManageList($paginator)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
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
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
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
    // Ulan O: Setting Subject By page_id
    if ($this->page_id)
      Engine_Api::_()->core()->setSubject(Engine_Api::_()->getItem('page', $this->page_id));

    $table = $this->getTable();
    $select = $table->getSelect(array(
      'page_id' => $this->page_id,
    ));

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }
    $albums = Zend_Paginator::factory($select);
    $albums->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $albums->setCurrentPageNumber($this->_getParam('page', 1));

    $customizer = 'browseItemList';

    if ($this->pageObject->isAdmin())
      $customizer = 'manageAlbumList';

    $form = $this->getSearchForm();
    $form->setMethod('get');

    $form->getElement('search')->setValue($this->_getParam('search'));

    $this->add($this->component()->subjectPhoto($this->pageObject))
      ->add($this->component()->navigation('pagealbum', true))
      ->add($this->component()->itemSearch($form))
      ;
    if ($this->isAllowedPost)
      $this->add($this->component()->quickLinks('pagealbum_quick', true));
    $this->add($this->component()->itemList($albums, $customizer, array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))))
//      ->add($this->component()->paginator($albums))
      ->renderContent();
  }

  public function indexMineAction()
  {
    if (!$this->isAllowedView) {
      $this->view->error = 1;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
      return;
    }
    // Ulan O: Setting Subject By page_id
    if ($this->page_id)
      Engine_Api::_()->core()->setSubject(Engine_Api::_()->getItem('page', $this->page_id));

    $table = $this->getTable();

    $select = $table->getSelect(array(
      'page_id' => $this->page_id,
      'user_id' => $this->viewer->getIdentity()
    ));

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }
    $albums = Zend_Paginator::factory($select);
    $albums->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $albums->setCurrentPageNumber($this->_getParam('page', 1));

    $form = $this->getSearchForm();
    $form->setMethod('get');
    $form->getElement('search')->setValue($this->_getParam('search'));

    $this->add($this->component()->subjectPhoto($this->pageObject))
      ->add($this->component()->navigation('pagealbum', true))
      ->add($this->component()->itemSearch($form));


    if ($this->isAllowedPost)
      $this->add($this->component()->quickLinks('pagealbum_quick', true));

    $this->add($this->component()->itemList($albums, 'manageAlbumList', array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))))
//      ->add($this->component()->paginator($albums))
      ->renderContent();
  }

  public function indexUploadAction()
  {
    if (!$this->isAllowedPost)
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    // Init album
    $albumTable = Engine_Api::_()->getItemTable('pagealbum');
    $myAlbums = $albumTable->select()
      ->from($albumTable, array('pagealbum_id', 'title'))
      ->where('user_id = ?', $this->viewer->getIdentity())
      ->query()
      ->fetchAll();

    $albumOptions = array('0' => 'Create A New Album');
    foreach ($myAlbums as $myAlbum) {
      $albumOptions[$myAlbum['pagealbum_id']] = $myAlbum['title'];
    }

    $form = new Pagealbum_Form_Album();
    $form->removeElement('file');
    $form->addElement('File', 'photos', array(
      'label' => 'Photos',
      'order' => 3,
      'isArray' => true
    ));
    $form->photos->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    $form->album->setMultiOptions($albumOptions);
    $form->album->setAttrib('onchange', '');

    Engine_Api::_()->core()->setSubject($this->pageObject);
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->subjectPhoto($this->pageObject))
        ->add($this->component()->navigation('pagealbum', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->subjectPhoto($this->pageObject))
        ->add($this->component()->navigation('pagealbum', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    /**
     * @var $albumTbl Pagealbum_Model_DbTable_Pagealbums
     */
    $albumTbl = $this->getTable();
    $photoTbl = Engine_Api::_()->getDbTable('pagealbumphotos', 'pagealbum');
    $db = $albumTbl->getAdapter();

    $db->beginTransaction();
    try {
      $values = $form->getValues();
      if ($values['album'] == 0) {
        $album = $albumTbl->createRow();

        $album->title = $values['title'];
        $album->description = $values['description'];
        $album->page_id = $this->page_id;
        $album->user_id = $this->viewer->getIdentity();
        $album->save();
        $tags = preg_split('/[,]+/', $values['tags']);
        if ($tags) {
          $album->tags()->setTagMaps($this->viewer, $tags);
        }
      } else {
        $album = Engine_Api::_()->getItem('pagealbum', $values['album']);
      }

      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity($this->viewer, $album->getPage(), 'pagealbum_photo_new', null, array('is_mobile' => true,
          'count' => count($values['photos']),
          'link' => $album->getLink()
        )
      );

      $picupFiles = $this->getPicupFiles('photos');
      if (empty($picupFiles))
        $photos = $form->photos->getFileName();
      else
        $photos = $picupFiles;
      $count = 0;

      if (is_array($photos)) {
        foreach ($photos as $photoPath) {
          $photo = $photoTbl->createRow();
          $photo->collection_id = $album->getIdentity();
          $photo->owner_id = $this->viewer->getIdentity();
          $photo->save();

          $photo->setPhoto($photoPath);
          $photo->save();

          if ($action instanceof Activity_Model_Action && $count < 8) {
            $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
          }

          $count++;
        }

        if ($values['album'] == 0)
          $album->photo_id = $photo->file_id;
      } else {
        $photo = $photoTbl->createRow();
        $photo->collection_id = $album->getIdentity();
        $photo->owner_id = $this->viewer->getIdentity();
        $photo->save();

        $photo->setPhoto($photos);
        $photo->save();

        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);

        if ($values['album'] == 0)
          $album->photo_id = $photo->file_id;
      }

      $album->save();

      $search_api = Engine_Api::_()->getDbTable('search', 'page');
      $search_api->saveData($album);

      Engine_Api::_()->page()->sendNotification($album, 'post_pagealbum');

    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->commit();
    return $this->redirect($album);
  }

  public function indexEditAction()
  {
    $form = new Pagealbum_Form_Album();
    $form->removeElement('file');

    $page = Engine_Api::_()->getItem('page', $this->_getParam('page_id'));
    $album = Engine_Api::_()->getItem('pagealbum', $this->_getParam('album_id'));
    $user = $album->getOwner();

    $form->removeElement('album');
    $form->getElement('title')->setValue($album->getTitle());
    $form->getElement('description')->setValue($album->getDescription());

    $tags = $album->tags()->getTagMaps();
    $tagString = '';
    foreach ($tags as $tagmap) {
      if ($tagString !== '') $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }

    $form->getElement('tags')->setValue($tagString);

    Engine_Api::_()->core()->setSubject($this->pageObject);
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->subjectPhoto($album))
        ->add($this->component()->navigation('pagealbum', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($values = $this->getRequest()->getPost())) {
      $form->populate($values);
      $this->add($this->component()->subjectPhoto($album))
        ->add($this->component()->navigation('pagealbum', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    try {
      $album->title = $values['title'];
      $album->description = $values['description'];
      $tags = preg_split('/[,]+/', $values['tags']);
      if ($tags) {
        $album->tags()->setTagMaps($user, $tags);
      }

      $album->save();
    } catch (Exception $e) {
      throw $e;
    }
    return $this->redirect($this->view->url(array('page_id' => $page->url, 'content' => 'pagealbum', 'content_id' => $album->getIdentity()), 'page_view'));
  }

  public function indexDeleteAction()
  {
    $form = new Pagealbum_Form_Delete();

    $page = Engine_Api::_()->getItem('page', $this->_getParam('page_id'));
    $album = Engine_Api::_()->getItem('pagealbum', $this->_getParam('album_id'));

    if (!$album) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Album doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $select = $album->getCollectiblesSelect();
    $photo_id = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum')->getAdapter()->fetchOne($select);

    $db = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum')->getAdapter();
    $db->beginTransaction();

    try {
      if (!empty($photo_id)) {
        $attachmentTable = Engine_Api::_()->getDbtable('attachments', 'activity');
        $name = $attachmentTable->info('name');
        $select = $attachmentTable->select()
          ->setIntegrityCheck(false)
          ->from($name, array('action_id'))
          ->where('type = ?', "pagealbumphoto")
          ->where('id = ?', $photo_id);

        $action_id = (int)$attachmentTable->getAdapter()->fetchOne($select);
        $where = array('action_id = ?' => $action_id);

        $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $actionsTable->delete($where);

        $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
        $streamTable->delete($where);

        $attachmentTable->delete($where);

        $where = array('resource_id = ?' => $action_id);

        $commentTable = Engine_Api::_()->getDbtable('comments', 'activity');
        $commentTable->delete($where);

        $likeTable = Engine_Api::_()->getDbtable('likes', 'activity');
        $likeTable->delete($where);
      }

      $album->delete();

      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($this->view->url(array('module' => 'pagealbum', 'controller' => 'index', 'action' => 'mine', 'page_id' => $page->getIdentity()), 'default'), Zend_Registry::get('Zend_Translate')->_("Album was deleted."), true);
  }

  public function indexManagePhotoAction()
  {
    $page = Engine_Api::_()->getItem('page', $this->_getParam('page_id'));
    $album = Engine_Api::_()->getItem('pagealbum', $this->_getParam('album_id'));

    $paginator = $album->getCollectiblesPaginator();
    if ($paginator->getTotalItemCount() > 0) {

      $paginator->setCurrentPageNumber($this->_getParam('page'));
      $paginator->setItemCountPerPage($paginator->getTotalItemCount());

      $form = new Pagealbum_Form_Photos();
    }

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->subjectPhoto($page))
        ->add($this->component()->itemList($paginator, 'managePhotoList', array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
        ->renderContent();
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->subjectPhoto($page))
        ->add($this->component()->itemList($paginator, 'managePhotoList', array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
        ->renderContent();
      return;
    }
  }

  public function indexEditPhotoAction()
  {
    $photo = Engine_Api::_()->getItem('pagealbumphoto', $this->_getParam('pagealbumphoto_id'));
    $album = $photo->getCollection();
    $page = $album->getPAge();
    $action_type = $this->_getParam('action_type');

    if ($action_type == 'edit') {
      $form = new Pagealbum_Form_Photo_Edit();
      $form
        ->setAction($_SERVER['REQUEST_URI'])
        ->clearDecorators()
        ->removeElement('delete');

      $form->addElement('Button', 'submit', array(
        'label' => 'Save Photo',
        'type' => 'submit',
      ))->loadDefaultDecorators();

      $form->getElement('title')->setValue($photo->title);
      $form->getElement('description')->setValue($photo->description);

      if (!$this->getRequest()->isPost()) {
        $this->add($this->component()->subjectPhoto($photo))
          ->add($this->component()->form($form))
          ->renderContent();
        return;
      }

      if (!$form->isValid($this->getRequest()->getPost())) {
        $this->add($this->component()->subjectPhoto($photo))
          ->add($this->component()->form($form))
          ->renderContent();
        return;
      }

      $photo->title = $form->getValue('title');
      $photo->description = $form->getValue('description');
      $photo->save();

    } elseif ($action_type == 'cover') {
      $album->photo_id = $photo->pagealbumphoto_id;
      $album->save();
    } elseif ($action_type == 'delete') {
      $photo->delete();
    }

    return $this->redirect($this->view->url(array('action' => 'manage-photo', 'page_id' => $page->page_id, 'album_id' => $album->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_album', true));
  }

  private function prepareBrowseList(Zend_Paginator $paginator)
  {
    $items = array();

    $photo_type = 'thumb.normal';
    if(Engine_Api::_()->apptouch()->isTabletMode()) {
      $photo_type = 'thumb.profile';
    }

    foreach ($paginator as $p_item) {
      $page_pref = '';

      if (!is_array($p_item))
        throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator');

      if ($p_item['type'] == 'page') {
        $page_pref = 'page';
      }

      $item = Engine_Api::_()->getItem($page_pref . 'album', $p_item['album_id']);
      $owner = $item->getOwner();

      $std = array(
        'title' => $item->getTitle(),
        'descriptions' => array(
          $this->view->translate('By') . ' ' . $owner->getTitle()
        ),
        'href' => $item->getHref(),
        'photo' => $item->getPhotoUrl($photo_type),
        'counter' => strtoupper($this->view->translate(array('%s photo', '%s photos', $item->count()), $this->view->locale()->toNumber($item->count()))),
        'owner_id' => $owner->getIdentity(),
        'owner' => $this->subject($owner)
      );

      if ($page_pref)
        $std['descriptions'][] = $this->view->translate('On page ') . $this->view->htmlLink($item->getPage()->getHref(), $item->getPage()->getTitle());


      $items[] = $std;
    }

    $paginatorPages = $paginator->getPages();
    return array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',

      'items' => $items,
      'attrs' => array('class' => 'tile-view')
    );
  }

  private function prepareManageList(Zend_Paginator $paginator)
  {
    $items = array();
    $photo_type = 'thumb.normal';
    if(Engine_Api::_()->apptouch()->isTabletMode()) {
      $photo_type = 'thumb.profile';
    }

    foreach ($paginator as $p_item) {
      $page_pref = '';

      if (!is_array($p_item))
        throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator');

      if ($p_item['type'] == 'page') {
        $page_pref = 'page';
      }

      $item = Engine_Api::_()->getItem($page_pref . 'album', $p_item['album_id']);

      $owner = $item->getOwner();
      $options = array();
      if ($page_pref) {
        $options[] = array(
          'label' => $this->view->translate('Edit Album'),
          'href' => $item->getHref(),
          'class' => 'buttonlink icon_album_edit'
        );

        $options[] = array(
          'label' => $this->view->translate('Delete Album'),
          'href' => $this->view->url(array('action' => 'delete', 'pagealbum_id' => $item->getIdentity()), 'page_albums', true),
          'class' => 'buttonlink smoothbox icon_album_delete'
        );
      } else {
        $options[] = $this->getOption($item, 0);
        $options[] = $this->getOption($item, 1);
      }

      $std = array(
        'title' => $item->getTitle(),
        'descriptions' => array(
        ),
        'href' => $item->getHref(),
        'photo' => $item->getPhotoUrl($photo_type),
        'counter' => strtoupper($this->view->translate(array('%s photo', '%s photos', $item->count()), $this->view->locale()->toNumber($item->count()))),
        'owner_id' => null,
        'owner' => null,
        'manage' => $options
      );

      if ($page_pref)
        $std['descriptions'][] = $this->view->translate('On page ') . $this->view->htmlLink($item->getPage()->getHref(), $item->getPage()->getTitle());


      $items[] = $std;
    }

    $paginatorPages = $paginator->getPages();
    return array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',

      'items' => $items,
      'attrs' => array('class' => 'tile-view')
    );
  }

  //=------------------------------------------ PageAlbumPhoto Customizer Functions ---------------------------------
  public function managePhotoList(Core_Model_Item_Abstract $item)
  {
    $page_id = $item->getPage()->getIdentity();
    $options = array();
    $customize_fields = array();

    $photo_type = 'thumb.normal';
    if(Engine_Api::_()->apptouch()->isTabletMode()) {
      $photo_type = 'thumb.profile';
    }

    $options[] = array(
      'label' => $this->view->translate('Edit Photo'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'edit-photo', 'page_id' => $page_id, 'action_type' => 'edit', 'pagealbumphoto_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_album', true),
        'class' => 'buttonlink icon_album_edit'
      ),
    );

    $options[] = array(
      'label' => $this->view->translate('Set Album Cover'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'edit-photo', 'page_id' => $page_id, 'pagealbumphoto_id' => $item->getIdentity(), 'action_type' => 'cover', 'no_cache' => rand(0, 1000)), 'page_album', true),
        'class' => 'buttonlink icon_album_edit'
      )
    );

    $options[] = array(
      'label' => $this->view->translate('Delete Photo'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'edit-photo', 'page_id' => $page_id, 'pagealbumphoto_id' => $item->getIdentity(), 'action_type' => 'delete', 'no_cache' => rand(0, 1000)), 'page_album', true),
        'class' => 'buttonlink smoothbox icon_album_delete',
      )
    );

    $customize_fields = array(
      'title' => $item->getTitle(),
      'descriptions' => array(
        $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date) . ' ' . $this->view->translate('By') . ' ' . $item->getOwner()->getTitle()
      ),
      'photo' => $item->getPhotoUrl($photo_type),
      'manage' => $options
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageAlbumPhoto Customizer Functions ---------------------------------

  //=------------------------------------------ PageAlbum Customizer Functions ---------------------------------
  public function manageAlbumList(Core_Model_Item_Abstract $item)
  {
    $page_id = $item->getPage()->getIdentity();
    $options = array();

    $photo_type = 'thumb.normal';
    if(Engine_Api::_()->apptouch()->isTabletMode()) {
      $photo_type = 'thumb.profile';
    }

    $options[] = array(
      'label' => $this->view->translate('Manage Photos'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'manage-photo', 'page_id' => $page_id, 'album_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_album', true),
        'class' => 'buttonlink icon_album_edit'
      ),
    );

    $options[] = array(
      'label' => $this->view->translate('Edit Album'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'edit', 'page_id' => $page_id, 'album_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_album', true),
        'class' => 'buttonlink icon_album_edit'
      )
    );

    $options[] = array(
      'label' => $this->view->translate('Delete Album'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'delete', 'page_id' => $page_id, 'album_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_album', true),
        'class' => 'buttonlink smoothbox icon_album_delete',
      )
    );

    if (isset($item->photo_id)) {
      $photoUrl = $item->getPhotoUrl($photo_type);
    } else {
      $photoUrl = $item->getOwner()->getPhotoUrl($photo_type);
    }

    $customize_fields = array(
      'creation_date' => null,
      'title' => $item->getTitle(),
      'descriptions' => array(
        $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date) . ' ' . $this->view->translate('By') . ' ' . $item->getOwner()->getTitle()
      ),
      'photo' => $photoUrl,
      'manage' => $options,
      'counter' => strtoupper($this->view->translate(array('%s photo', '%s photos', $item->count()), $this->view->locale()->toNumber($item->count()))),
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageAlbum Customizer Functions ---------------------------------

  public function browseItemList(Core_Model_Item_Abstract $item)
  {
    $photo_type = 'thumb.normal';
    if(Engine_Api::_()->apptouch()->isTabletMode()) {
      $photo_type = 'thumb.profile';
    }

    if (isset($item->photo_id)) {
      $photoUrl = $item->getPhotoUrl($photo_type);
    } else {
      $photoUrl = $item->getOwner()->getPhotoUrl($photo_type);
    }
    $customize_fields = array(
      'title' => $item->getTitle(),
      'descriptions' => array(
        $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date) . ' ' . $this->view->translate('By') . ' ' . $item->getOwner()->getTitle()
      ),
      'photo' => $photoUrl,
      'creation_date' => null,
      'counter' => strtoupper($this->view->translate(array('%s photo', '%s photos', $item->count()), $this->view->locale()->toNumber($item->count()))),
    );

    return $customize_fields;
  }

//  Helper Methods from Pagealbum_IndexController.php {
  protected function getValues()
  {
    return array(
      'description' => trim($this->_getParam('description')),
      'title' => trim($this->_getParam('title')),
      'page_id' => (int)$this->_getParam('page_id'),
      'album' => (int)($this->_getParam('album', $this->_getParam('album_id'))),
      'file' => $this->_getParam('file'),
      'tags' => $this->_getParam('tags')
    );
  }

  protected function getApi()
  {
    return Engine_Api::_()->getApi('core', 'pagealbum');
  }

  protected function getPageApi()
  {
    return Engine_Api::_()->getApi('core', 'page');
  }

  protected function getPhotoTable()
  {
    return Engine_Api::_()->getDbTable('pagealbumphotos', 'pagealbum');
  }

  protected function getTable()
  {
    return Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
  }
//  } Helper Methods from Pagealbum_IndexController.php

}
