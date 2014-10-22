<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 12.06.12
 * Time: 14:29
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_PagevideoController extends Apptouch_Controller_Action_Bridge
{
  public function videosBrowseAction()
  {
    if (!$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid()) return;

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $params = $this->_request->getParams();
    $params['ipp'] = $settings->getSetting('pagevideo.page', 10);

    //Paginator
    $paginator = Engine_Api::_()->getApi('core', 'pagevideo')->getVideoPaginator($params);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
//    $paginator->setItemCountPerPage(2);
    $this
      ->setPageTitle($this->view->translate('Browse Videos'))
      ->addPageInfo('type', 'browse')
      ->add($this->component()->navigation('video_main', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ->add($this->component()->quickLinks('video_quick', true))
      ->add($this->component()->customComponent('itemList', $this->prepareBrowseList($paginator)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function videosManageAction()
  {
    if (!$this->_helper->requireUser->isValid()) return;

    $params = $this->_request->getParams();

    $params['view'] = 3;
    $params['owner'] = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $params['ipp'] = $settings->getSetting('pagevideo.page', 10);


    $paginator = Engine_Api::_()->getApi('core', 'pagevideo')->getVideoPaginator($params);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
//    $paginator->setItemCountPerPage(2);

    $this
      ->setPageTitle($this->view->translate('Manage Videos'))
      ->addPageInfo('type', 'manage')
      ->add($this->component()->navigation('video_main', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ->add($this->component()->quickLinks('video_quick', true))
      ->add($this->component()->customComponent('itemList', $this->prepareManageList($paginator)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  private function prepareBrowseList(Zend_Paginator $paginator)
  {
    $photo_type = 'thumb.normal';
    if(Engine_Api::_()->apptouch()->isTabletMode()) {
      $photo_type = 'thumb.profile';
    }

    $items = array();
    foreach ($paginator as $p_item) {
      $page_pref = '';

      if (!is_array($p_item))
        throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator');

      if ($p_item['type'] == 'page') {
        $page_pref = 'page';
      }

      $item = Engine_Api::_()->getItem($page_pref . 'video', $p_item['video_id']);
      $owner = $item->getOwner();

      $std = array(
        'title' => $item->getTitle(),
        'descriptions' => array(
          $this->view->translate('Posted') . ' ' . $this->view->translate('By') . ' ' . $owner->getTitle()
        ),
        'photo' => $item->getPhotoUrl($photo_type),
        'href' => $item->getHref(),
        'creation_date' => $this->getVideoDuration($item),
        'counter' => strtoupper($this->view->translate(array('%1$s view', '%1$s views', $item->view_count), $this->view->locale()->toNumber($item->view_count))),
        'owner_id' => $owner->getIdentity(),
        'owner' => $this->subject($owner),
        'type' => $item->getType(),
      );

      if ($page_pref)
        $std['descriptions'][] = $this->view->translate('On page ') . $this->view->htmlLink($item->getPage()->getHref(), $item->getPage()->getTitle());


      $items[] = $std;
    }

    $paginatorPages = $paginator->getPages();
    $component = array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',

      'items' => $items,
      'attrs' => array('class' => 'tile-view')
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
    $photo_type = 'thumb.normal';
    if(Engine_Api::_()->apptouch()->isTabletMode()) {
      $photo_type = 'thumb.profile';
    }

    $items = array();
    foreach ($paginator as $p_item) {
      $page_pref = '';

      if (!is_array($p_item))
        throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator');

      if ($p_item['type'] == 'page') {
        $page_pref = 'page';
      }

      $item = Engine_Api::_()->getItem($page_pref . 'video', $p_item['video_id']);
      $options = array();
      if ($page_pref) {
        $options[] = array(
          'label' => $this->view->translate('Edit Entry'),
          'href' => $item->getHref(),
          'class' => 'buttonlink icon_pagevideo_edit'
        );

        $options[] = array(
          'label' => $this->view->translate('Delete Entry'),
          'href' => $this->view->url(array('action' => 'delete', 'pagevideo_id' => $item->getIdentity()), 'page_videos', true),
          'class' => 'buttonlink smoothbox icon_pagevideo_delete'
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
        'creation_date' => $this->getVideoDuration($item),
        'counter' => strtoupper($this->view->translate(array('%1$s view', '%1$s views', $item->view_count), $this->view->locale()->toNumber($item->view_count))),
        'owner_id' => null,
        'owner' => null,
        'manage' => $options,
        'type' => $item->getType(),
      );

      if ($page_pref)
        $std['descriptions'][] = $this->view->translate('On page ') . $this->view->htmlLink($item->getPage()->getHref(), $item->getPage()->getTitle());


      $items[] = $std;
    }

    $paginatorPages = $paginator->getPages();
    $component = array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',

      'items' => $items,
      'attrs' => array('class' => 'tile-view')
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

  private function getVideoDuration(Core_Model_Item_Abstract $item)
  {
    if ($item->duration >= 3600) {
      $duration = gmdate("H:i:s", $item->duration);
    } else {
      $duration = gmdate("i:s", $item->duration);
    }
    return $duration;
  }

  public function indexInit()
  {
    $this->page_id = $page_id = $this->_getParam('page_id');

    if (!$page_id) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this->pageObject = $page = Engine_Api::_()->getItem('page', $page_id);

    if (!$page) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this->viewer = Engine_Api::_()->user()->getViewer();
    $this->isAllowedView = $this->getPageApi()->isAllowedView($page);
    $this->isTeamMember = $page->isTeamMember();

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
    $select = Engine_Api::_()->getDbTable('pagevideos', 'pagevideo')
          ->getSelect(array(
                'p' => $this->_getParam('page', 1),
                'ipp' => $this->_getParam('itemCountPerPage', 10),
                'page_id' => $this->pageObject->getIdentity()
              ));
    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);

    if ($this->isAllowedPost)
      $this->add($this->component()->quickLinks('pagevideo_quick', true));

    $this->add($this->component()->subjectPhoto($this->pageObject))
      ->add($this->component()->navigation('pagevideo', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ->add($this->component()->itemList($paginator, 'browseItemList', array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))))
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

    $select = Engine_Api::_()->getDbTable('pagevideos', 'pagevideo')
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

    if ($this->isAllowedPost)
      $this->add($this->component()->quickLinks('pagevideo_quick', true));

    $this->add($this->component()->subjectPhoto($this->pageObject))
      ->add($this->component()->navigation('pagevideo', true))
      ->add($this->component()->itemSearch($this->getSearchForm()))
      ->add($this->component()->itemList($paginator, 'manageVideoList', array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function indexCreateAction()
  {
    if (!$this->isAllowedPost)
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));

    $form = new Pagevideo_Form_Video();
    $form->getElement('video_url')->setAttrib('maxlength', 500);
    $form->removeElement('video_code');
    $form->removeElement('video_id');
    $form->removeElement('video_ignore');

    $form->removeElement('video_file');
    $form->addElement('File', 'video_file', array(
      'label' => 'Add Video',
      'order' => 5
    ));
    $form->getElement('video_type')->setAttrib('onchange', '');

    Engine_Api::_()->core()->setSubject($this->pageObject);
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->subjectPhoto($this->pageObject))
        ->add($this->component()->navigation('pagevideo', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->subjectPhoto($this->pageObject))
        ->add($this->component()->navigation('pagevideo', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $values = $form->getValues();

    $code = '';
    if( $values['video_type'] == 2 || $values['video_type'] == 1 ) {
      $code = $this->extractCode($values['video_url'], $values['video_type']);

      if ($values['video_type'] == 1) {
        if (!$this->checkYouTube($code)) {
          $form->getElement('video_url')->addError('We could not find a video there - please check the URL and try again.');
          $this->add($this->component()->form($form))
            ->renderContent();
          return;
        }
      } elseif ($values['video_type'] == 2) {
        $form->getElement('video_url')->addError('We could not find a video there - please check the URL and try again.');
        if (!$this->checkVimeo($code)) {
          $this->add($this->component()->form($form))
            ->renderContent();
          return;
        }
      }
    }

    /**
     * @var $table Pagevideo_Model_DbTable_Pagevideos
     */
    $table = $this->getTable();
    $db = $table->getDefaultAdapter();
    $video = $table->createRow();

    $db->beginTransaction();
    try {
      $video->page_id = $this->pageObject->getIdentity();
      $video->user_id = $this->viewer->getIdentity();
      $video->title = $values['video_title'];
      $video->description = $values['video_description'];
      $video->search = 1;
      $video->type = $values['video_type'];

      $video->save();

      $tags = preg_split('/[,]+/', $values['video_tags']);
      if ($tags) {
        $video->tags()->setTagMaps($this->viewer, $tags);
      }

    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if ($values['video_type'] == 2 || $values['video_type'] == 1) {

      //Check
      if ($values['video_type'] == 1) {
        if ($this->checkYouTube($code)) {
          $video->code = $code;
        }
      } elseif ($values['video_type'] == 2) {
        if ($this->checkVimeo($code)) {
          $video->code = $code;
        }
      }

      // Now try to create thumbnail
      $thumbnail = $this->handleThumbnail($video->type, $video->code);
      $ext = ltrim(strrchr($thumbnail, '.'), '.');
      $thumbnail_parsed = @parse_url($thumbnail);

      if (@GetImageSize($thumbnail)) {
        $valid_thumb = true;
      } else {
        $valid_thumb = false;
      }

      if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
        $mini_file = APPLICATION_PATH . '/temporary/link_mini_' . md5($thumbnail) . '.' . $ext;
        $icon_file = APPLICATION_PATH . '/temporary/link_thumb_icon_' . md5($thumbnail) . '.' . $ext;
        $normal_file = APPLICATION_PATH . '/temporary/link_thumb_normal_' . md5($thumbnail) . '.' . $ext;

        $src_fh = fopen($thumbnail, 'r');
        $tmp_fh = fopen($tmp_file, 'w');
        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

        $image = Engine_Image::factory();
        $image->open($tmp_file)
          ->resize(240, 180)
          ->write($thumb_file)
          ->destroy();

        $image = Engine_Image::factory();
        $image->open($tmp_file)
          ->resize(34, 34)
          ->write($mini_file)
          ->destroy();

        $image = Engine_Image::factory();
        $image->open($tmp_file)
          ->resize(48, 48)
          ->write($icon_file)
          ->destroy();

        $image = Engine_Image::factory();
        $image->open($tmp_file)
          ->resize(120, 240)
          ->write($normal_file)
          ->destroy();

        try {
          $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
          ));

          $thumbMiniFileRow = Engine_Api::_()->storage()->create($mini_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
          ));

          $thumbIconFileRow = Engine_Api::_()->storage()->create($icon_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
          ));

          $thumbNormaloFile = Engine_Api::_()->storage()->create($normal_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
          ));

          $thumbFileRow->bridge($thumbMiniFileRow, 'thumb.mini');
          $thumbFileRow->bridge($thumbIconFileRow, 'thumb.icon');
          $thumbFileRow->bridge($thumbNormaloFile, 'thumb.norm');

          // Remove temp file
          @unlink($thumb_file);
          @unlink($mini_file);
          @unlink($tmp_file);
          @unlink($icon_file);
          @unlink($normal_file);
        }
        catch (Exception $e)
        {
          throw $e;
        }
        $information = $this->handleInformation($video->type, $video->code);

        $video->duration = $information['duration'];
        if (!$video->description) $video->description = $information['description'];
        $video->photo_id = $thumbFileRow->file_id;
        $video->status = 1;
        $video->save();

        // Insert new action item
        $insert_action = true;
      }
    } else if ($values['video_type'] == 3) {
      $video_file = $this->getPicupFiles('video_file');
      // Set photo

      if (!empty($values['video_file'])) {
        $file = $form->video_file;
        $video_info = $form->video_file->getFileInfo();
        $extension = pathinfo($video_info['video_file']['name']);
        $video->code = $extension['extension'];

        $tmp_file = $video_info['video_file']['tmp_name'];

      } else if (!empty($video_file)) {
        $file = $video_file[0];
        $extension = pathinfo($file);
        $video->code = $extension['extension'];

        $tmp_file = $file;
      }

      // Store video in temporary storage object for ffmpeg to handle
      $storage = Engine_Api::_()->getItemTable('storage_file');
      $storageObject = $storage->createFile($file, array(
        'parent_id' => $video->getIdentity(),
        'parent_type' => $video->getType(),
        'user_id' => $video->user_id,
      ));

      @unlink($tmp_file);

      $video->file_id = $storageObject->file_id;
      $video->save();

      Engine_Api::_()->getDbtable('jobs', 'core')->addJob('pagevideo_encode', array(
        'pagevideo_id' => $video->getIdentity(),
      ));
    }

    $db->commit();

    $db->beginTransaction();
    try {

      $owner = $video->getOwner();
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video->getPage(), 'pagevideo_new');
      if ($action != null) {
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
      }

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($this->view->url(array('action' => 'manage', 'page_id' => $this->page_id), 'page_video'));
  }

  public function indexEditAction()
  {
    $video = Engine_Api::_()->getItem('pagevideo', $this->_getParam('video_id'));
    $user = $video->getOwner();
    $page = $video->getPage();

    $form = new Pagevideo_Form_Edit();
    $form->video_title->setValue($video->title);
    $form->video_description->setValue($video->description);

    $tags = $video->tags()->getTagMaps();
    $tagString = '';
    foreach ($tags as $tagmap) {
      if ($tagString !== '') $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }
    $form->getElement('video_tags')->setValue($tagString);

    Engine_Api::_()->core()->setSubject($this->pageObject);
    if (!$this->getRequest()->isPost() || !$form->isValid($values = $this->getRequest()->getPost())) {
      $this->add($this->component()->subjectPhoto($video))
        ->add($this->component()->navigation('pagevideo', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $video->title = $values['video_title'];
    $video->description = $values['video_description'];

    $tags = preg_split('/[,]+/', $values['video_tags']);
    if ($tags) {
      $video->tags()->setTagMaps($user, $tags);
    }

    $video->save();

    return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'video'), 'page_view'));
  }

  public function indexDeleteAction()
  {
    $video = Engine_Api::_()->getItem('pagevideo', $this->_getParam('video_id'));
    $page = $video->getPage();

    $form = new Pagevideo_Form_Delete();

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $video->delete();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video was deleted.');

    $this->add($this->component()->form($form))
      ->renderContent();

    return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'video'), 'page_view'));
  }

  protected function getApi()
  {
    return Engine_Api::_()->getApi('core', 'pagevideo');
  }

  protected function getPageApi()
  {
    return Engine_Api::_()->getApi('core', 'page');
  }

  protected function getTable()
  {
    return Engine_Api::_()->getDbTable('pagevideos', 'pagevideo');
  }

  // YouTube Functions
  public function checkYouTube($code)
  {
    $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
    if (!$data = @file_get_contents($prefix."gdata.youtube.com/feeds/api/videos/" . $code)) return false;
    if ($data == "Video not found") return false;
    return $data;
  }

  // Vimeo Functions
  public function checkVimeo($code)
  {
    $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
    //http://www.vimeo.com/api/docs/simple-api
    //http://vimeo.com/api/v2/video
    $data = @simplexml_load_file($prefix."vimeo.com/api/v2/video" . $code . ".xml");
    $id = count($data->video->id);
    if ($id == 0) return false;
    return true;
  }

  // handles thumbnails
  public function handleThumbnail($type, $code = null)
  {
    $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
    switch ($type) {
      //youtube
      case "1":
        // http://img.youtube.com/vi/E98IYokujSY/default.jpg
        return $prefix."img.youtube.com/vi/$code/0.jpg";
      // vimeo
      case "2":
        // thumbnail_medium
        $data = simplexml_load_file($prefix."vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        return $thumbnail;
    }
  }

  public function extractCode($url, $type)
  {
    switch ($type) {
      //youtube
      case "1":
        // change new youtube URL to old one
        $new_code = @pathinfo($url);
        $url = preg_replace("/#!/", "?", $url);

        // get v variable from the url
        $arr = array();
        $arr = @parse_url($url);
        $code = "code";
        $parameters = $arr["query"];
        parse_str($parameters, $data);
        $code = $data['v'];
        if ($code == "") {
          $code = $new_code['basename'];
        }

        return $code;
      //vimeo
      case "2":
        // get the first variable after slash
        $code = @pathinfo($url);
        return $code['basename'];
    }
  }
  // retrieves infromation and returns title + desc
  public function handleInformation($type, $code)
  {
    $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
    switch ($type) {
      //youtube
      case "1":
        $yt = new Zend_Gdata_YouTube();
        $youtube_video = $yt->getVideoEntry($code);
        $information = array();
        $information['title'] = $youtube_video->getTitle();
        $information['description'] = $youtube_video->getVideoDescription();
        $information['duration'] = $youtube_video->getVideoDuration();

        return $information;
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file($prefix."vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        $information = array();
        $information['title'] = $data->video->title;
        $information['description'] = $data->video->description;
        $information['duration'] = $data->video->duration;

        return $information;
    }
  }

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
      'counter' => strtoupper($this->view->translate(array('%1$s view', '%1$s views', $item->view_count), $this->view->locale()->toNumber($item->view_count))),
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageVideo Customizer Functions ---------------------------------
  public function manageVideoList(Core_Model_Item_Abstract $item)
  {
    $photo_type = 'thumb.normal';
    if(Engine_Api::_()->apptouch()->isTabletMode()) {
      $photo_type = 'thumb.profile';
    }

    $options = array();
    $page_id = $item->getPage()->getIdentity();

    $options[] = array(
      'label' => $this->view->translate('Edit'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'edit', 'page_id' => $page_id, 'video_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_video', true),
        'class' => 'buttonlink icon_album_edit'
      )
    );

    $options[] = array(
      'label' => $this->view->translate('Delete'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'delete', 'page_id' => $page_id, 'video_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_video', true),
        'class' => 'buttonlink smoothbox icon_album_delete',
      )
    );

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
      'manage' => $options,
      'counter' => strtoupper($this->view->translate(array('%1$s view', '%1$s views', $item->view_count), $this->view->locale()->toNumber($item->view_count))),
    );

    return $customize_fields;
  }
  //=------------------------------------------ PageVideo Customizer Functions ---------------------------------
}
