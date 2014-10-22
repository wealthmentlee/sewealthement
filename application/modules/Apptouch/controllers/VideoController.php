<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 07.06.12
 * Time: 18:07
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_VideoController
  extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    // only show videos if authorized
    if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid()) return;

    $id = $this->_getParam('video_id', $this->_getParam('id', null));
    if ($id) {
      $video = Engine_Api::_()->getItem('video', $id);
      if ($video) {
        Engine_Api::_()->core()->setSubject($video);
      }
    }
    if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid()) return;
  }

  public function indexBrowseAction()
  {
    // Permissions
    $can_create = $this->_helper->requireAuth()->setAuthParams('video', null, 'create')->checkRequire();
    $values = $this->_getAllParams(); //array();

    $values['status'] = 1;
    $values['search'] = 1;

    // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if ($user_id) {
      $values['user_id'] = $user_id;
    }

    // Get videos
    $select = Engine_Api::_()->getApi('core', 'video')->getVideosSelect($values);
     if ($this->_getParam('search', false)) {
       $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
     }

     $paginator = Zend_Paginator::factory($select);
    $items_count = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('video.page', 10);
    $paginator->setItemCountPerPage($items_count);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->setFormat('browse')
      ->add($this->component()->itemSearch($this->getSearchForm()));

        if ($paginator->getTotalItemCount()) {
          $this->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))))
//            ->add($this->component()->paginator($paginator))
          ;
        } elseif ($this->_getParam('search', false)) {
          if($can_create)
            $this->add($this->component()->tip(
              $this->view->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->view->url(array('action' => 'create'), "video_general").'">', '</a>'),
              $this->view->translate('Nobody has posted a video with that criteria.')
            ));
          else
            $this->add($this->component()->tip(
              $this->view->translate('Nobody has posted a video with that criteria.')
            ));
        } else {
          if($can_create)
            $this->add($this->component()->tip(
              $this->view->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->view->url(array('action' => 'create'), "video_general").'">', '</a>'),
              $this->view->translate('Nobody has created a video yet.')
            ));
          else
            $this->add($this->component()->tip(
              $this->view->translate('Nobody has created a video yet.')
            ));
        }
        $this->renderContent();

  }

  public function indexManageAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$this->_helper->requireUser()->isValid()) return;
    $can_create = $this->_helper->requireAuth()->setAuthParams('video', null, 'create')->checkRequire();

    $this->view->categories = $categories = Engine_Api::_()->video()->getCategories();
    $values['user_id'] = $viewer->getIdentity();

    $select = Engine_Api::_()->getApi('core', 'video')->getVideosSelect($values);
     if ($this->_getParam('search', false)) {
       $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
     }

    $paginator = Zend_Paginator::factory($select);
    $quota = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
    $current_count = $paginator->getTotalItemCount();

    $items_count = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('video.page', 10); // todo
    $paginator->setItemCountPerPage($items_count);

    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->setFormat('manage')
      ->setPageTitle($this->view->translate('Manage Videos'))
      ->add($this->component()->itemSearch($this->getSearchForm()));

        if ($paginator->getTotalItemCount()) {
          $this->add($this->component()->itemList($paginator, "manageItemData", array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))))
//            ->add($this->component()->paginator($paginator))
          ;
        } elseif ($this->_getParam('search', false)) {
          if($can_create)
            $this->add($this->component()->tip(
              $this->view->translate('Get started by %1$sposting%2$s a new video.', '<a href="'.$this->view->url(array('action' => 'create')).'">', '</a>'),
              $this->view->translate('APPTOUCH_You do not have any videos with that criteria.')
            ));
          else
            $this->add($this->component()->tip(
              $this->view->translate('APPTOUCH_You do not have any videos with that criteria.')
            ));

        } else {
          if($can_create)
            $this->add($this->component()->tip(
              $this->view->translate('Get started by %1$sposting%2$s a new video.', '<a href="'.$this->view->url(array('action' => 'create')).'">', '</a>'),
              $this->view->translate('You do not have any videos.')
            ));
          else
            $this->add($this->component()->tip(
              $this->view->translate('You do not have any videos.')
            ));
        }
        $this->renderContent();
    if (($current_count >= $quota) && !empty($quota)){
      $this->add($this->component()->tip(
        $this->view->translate('You have already created the maximum number of videos allowed. If you would like to post a new video, please delete an old one first.')
      ));
    }

  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function browseItemData(Core_Model_Item_Abstract $item)
  {

    $customize_fields = array(
      'descriptions' => array(
        $this->view->translate('By') . ' ' . $item->getOwner()->getTitle(),
        $this->getVideoDuration($item) . ' &#183; ' .  $this->view->translate(array('%1$s view', '%1$s views', $item->view_count), $this->view->locale()->toNumber($item->view_count)) . ' &#183; ' . $this->view->timestamp(strtotime($item->creation_date))
      ),
      'creation_date' => null,
    );
    return $customize_fields;
  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function manageItemData(Core_Model_Item_Abstract $item)
  {
    $owner = $item->getOwner();
    $customize_fields = array(
      'descriptions' => array(
        $this->getVideoDuration($item) . ' &#183; ' .  $this->view->translate(array('%1$s view', '%1$s views', $item->view_count), $this->view->locale()->toNumber($item->view_count)) . ' &#183; ' . $this->view->timestamp(strtotime($item->creation_date))
      ),
      'creation_date' => null,
      'owner_id' => null,
      'owner' => null,
      'manage' => $this->getOptions($item)
    );
    return $customize_fields;
  }

  private function getVideoDuration(Core_Model_Item_Abstract $item)
  {
    $owner = $item->getOwner();
    if ($item->duration >= 3600) {
      $duration = gmdate("H:i:s", $item->duration);
    } else {
      $duration = gmdate("i:s", $item->duration);
    }
    return $duration;
  }

  public function indexRateAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();

    $rating = $this->_getParam('rating');
    $video_id = $this->_getParam('video_id');


    $table = Engine_Api::_()->getDbtable('ratings', 'video');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      Engine_Api::_()->video()->setRating($video_id, $user_id, $rating);

      $video = Engine_Api::_()->getItem('video', $video_id);
      $video->rating = Engine_Api::_()->video()->getRating($video->getIdentity());
      $video->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $total = Engine_Api::_()->video()->ratingCount($video->getIdentity());

    $data = array();
    $data[] = array(
      'total' => $total,
      'rating' => $rating,
    );
    return $this->_helper->json($data);
    $data = Zend_Json::encode($data);
    $this->getResponse()->setBody($data);
  }

  public function indexCreateAction()
  {
    if (!$this->_helper->requireUser->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'create')->isValid()) return;

    // set up data needed to check quota
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();
    $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);

    $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
    $current_count = $paginator->getTotalItemCount();
    $this->setFormat('create');
    $this->lang(array(
      'Checking URL...'
    ));

    // Create form
    $form = new Video_Form_Video();
//    $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', Engine_Api::_()->user()->getViewer(), 'upload');
//    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
//    if( !empty($ffmpeg_path) && $allowed_upload ) {
//      $form->type->removeMultiOption(3);
//    }
    $multiOptions = $form->type->getMultiOptions();
    if(isset($multiOptions[3])) {
      $multiOptions[3] = 'My Device';
      $form->type->setMultiOptions($multiOptions);
    }

    $form->getElement('url')->setAttrib('maxlength', 500);
    $form->removeElement('file');
    $form->addElement('File', 'file', array(
      'label' => 'Choose Video',
      'order' => 12,
      'isArray' => false,
      'accept' => 'video/*',
      'options' => array(
        'use_upload_name' => true
      )
    ));

    $this->addPageInfo('videoUpload', array(
      'tagsUrl' => $this->view->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true),
      'validationUrl' => $this->view->url(array('module' => 'video', 'controller' => 'index', 'action' => 'validation'), 'default', true),
      'validationErrorMessage' => $this->view->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->view->translate("here")."</a>"),
    ));
    $this->lang('Checking URL...');

    if ($this->_getParam('type', false))
      $form->getElement('type')->setValue($this->_getParam('type'));

    // if this is from a failed attempt
    if ($this->_getParam('retry')) { /*
      $video = Engine_Api::_()->getItem('video', $this->_getParam('retry'));
      $form->getElement('search')->setValue($video->search);
      $form->getElement('title')->setValue($video->title);
      $form->getElement('description')->setValue($video->description);
      $form->getElement('category_id')->setValue($video->category_id);
      // prepare tags
      $videoTags = $video->tags()->getTagMaps();

      $tagString = '';
      foreach( $videoTags as $tagmap )
      {
        if( $tagString !== '' ) $tagString .= ', ';
        $tagString .= $tagmap->getTag()->getTitle();
      }

      $this->view->tagNamePrepared = $tagString;
      $form->tags->setValue($tagString);
      // get more information?
      // delete the video?
      $video->delete();*/
    }
    if (($current_count >= $quota) && !empty($quota)){
      return $this->add($this->component()->tip(
        $this->view->translate('You have already uploaded the maximum number of videos allowed.'),
        $this->view->translate('If you would like to upload a new video, please <a href="%1$s">delete</a> an old one first.', $this->view->url(array('action' => 'manage'), 'video_general'))
      ))
        ->renderContent();
    }

    if (!$this->getRequest()->isPost()) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues('url');
      //$form->set = $values['url'];
      // set title and description using getinfromation() here?
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $values = $form->getValues();
    $values['owner_id'] = $viewer->getIdentity();

    $insert_action = false;

    $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
    $db->beginTransaction();

    try {
      // Create video
      $table = Engine_Api::_()->getDbtable('videos', 'video');
      if ($values['type'] == 3) {
        $params = $this->uploadVideo();

        if($params['status']) {
          $video = $params['video'];
        } else {
          $form->addErrorMessage($params['message']);
          $this->add($this->component()->form($form))
            ->renderContent();
          return;
        }
      } else {
        $video = $table->createRow();
      }

      $video->setFromArray($values);
      $video->save();

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

        $src_fh = fopen($thumbnail, 'r');
        $tmp_fh = fopen($tmp_file, 'w');
        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

        $image = Engine_Image::factory();
        $image->open($tmp_file)
          ->resize(120, 240)
          ->write($thumb_file)
          ->destroy();

        try {
          $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
          ));

          // Remove temp file
          @unlink($thumb_file);
          @unlink($tmp_file);
        } catch (Exception $e) {

        }
        $information = $this->handleInformation($video->type, $video->code);

        $video->duration = $information['duration'];
        if (!$video->description) {
          $video->description = $information['description'];
        }
        $video->photo_id = $thumbFileRow->file_id;
        $video->status = 1;
        $video->save();

        // Insert new action item
        $insert_action = true;
      }

      if ($values['ignore'] == true) {
        $video->status = 1;
        $video->save();
        $insert_action = true;
      }

      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if (isset($values['auth_view'])) $auth_view = $values['auth_view'];
      else $auth_view = "everyone";
      $viewMax = array_search($auth_view, $roles);
      foreach ($roles as $i => $role)
      {
        $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
      }

      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if (isset($values['auth_comment'])) $auth_comment = $values['auth_comment'];
      else $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);
      foreach ($roles as $i => $role)
      {
        $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
      }


      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $video->tags()->addTagMaps($viewer, $tags);


      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }


    $db->beginTransaction();
    try {
      if ($insert_action) {
        $owner = $video->getOwner();
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video, 'video_new', null, array('is_mobile' => true));
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
        }
      }

      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($video) as $action) {
        $actionTable->resetActivityBindings($action);
      }


      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if ($video->type == 3) {
      return $this->redirect($this->view->url(array('action' => 'manage'), 'video_general', true));
    }
    return $this->redirect($this->view->url(array('user_id' => $viewer->getIdentity(), 'video_id' => $video->getIdentity()), 'video_view', true));
  }

  public function indexUploadVideoAction()
  {
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();

    if (empty($values['Filename'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . print_r($_FILES, true);
      return;
    }

    $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
    if (in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $values['owner_id'] = $viewer->getIdentity();

      $params = array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity()
      );
      $video = Engine_Api::_()->video()->createVideo($params, $_FILES['Filedata'], $values);

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->code = $video->code;
      $this->view->video_id = $video->video_id;

      // sets up title and owner_id now just incase members switch page as soon as upload is completed
      $video->title = $_FILES['Filedata']['name'];
      $video->owner_id = $viewer->getIdentity();
      $video->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.') . $e;
      // throw $e;
      return;
    }
  }

  public function indexDeleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $video = Engine_Api::_()->getItem('video', $this->getRequest()->getParam('video_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->isValid()) return;

    $form = new Video_Form_Delete();

    if (!$video) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $db = $video->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      Engine_Api::_()->getApi('core', 'video')->deleteVideo($video);
      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'video_general', true), Zend_Registry::get('Zend_Translate')->_('Video has been deleted.'), true);
  }

  public function indexEditAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    $viewer = Engine_Api::_()->user()->getViewer();

    $video = Engine_Api::_()->getItem('video', $this->_getParam('video_id'));
    //Engine_Api::_()->core()->setSubject($video);
    if (!$this->_helper->requireSubject()->isValid()) return;


    if ($viewer->getIdentity() != $video->owner_id && !$this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->isValid()) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Get navigation
    $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('video_main', array(), 'video_main_manage');
    $this->add($this->component()->navigation($navigation));
//    $this->view->video = $video;
//    $this->view->form =
    $form = new Video_Form_Edit();
    $form->getElement('search')->setValue($video->search);
    $form->getElement('title')->setValue($video->title);
    $form->getElement('description')->setValue($video->description);
    $form->getElement('category_id')->setValue($video->category_id);


    // authorization
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    foreach ($roles as $role)
    {
      if (1 === $auth->isAllowed($video, $role, 'view')) {
        $form->auth_view->setValue($role);
      }
      if (1 === $auth->isAllowed($video, $role, 'comment')) {
        $form->auth_comment->setValue($role);
      }
    }

    // prepare tags
    $videoTags = $video->tags()->getTagMaps();

    $tagString = '';
    foreach ($videoTags as $tagmap)
    {
      if ($tagString !== '') $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }

    $this->view->tagNamePrepared = $tagString;
    $form->tags->setValue($tagString);

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
    $db->beginTransaction();
    try {
      $values = $form->getValues();
      $video->setFromArray($values);
      $video->save();

      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if ($values['auth_view']) $auth_view = $values['auth_view'];
      else $auth_view = "everyone";
      $viewMax = array_search($auth_view, $roles);
      foreach ($roles as $i => $role)
      {
        $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
      }

      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if ($values['auth_comment']) $auth_comment = $values['auth_comment'];
      else $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);
      foreach ($roles as $i => $role)
      {
        $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
      }

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $video->tags()->setTagMaps($viewer, $tags);

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($video) as $action) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }


    return $this->redirect($this->view->url(array('action' => 'manage'), 'video_general', true));
  }

  public function indexUploadAction()
  {
    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) return $this->_forward('upload-video', null, null, array('format' => 'json'));

    if (!$this->_helper->requireUser()->isValid()) return;

    $form = new Video_Form_Video();
    $navigation = $this->getNavigation();

    if (!$this->getRequest()->isPost()) {
      if (null !== ($album_id = $this->_getParam('album_id'))) {
        $form->populate(array(
          'album' => $album_id
        ));
      }
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $album = $form->saveValues();
    //$this->_helper->redirector->gotoRoute(array('album_id'=>$album->album_id), 'album_editphotos', true);
  }

  public function indexViewAction()
  {
    //$video_id = $this->_getParam('video_id');
    //$video = Engine_Api::_()->getItem('video', $video_id);
    //if( $video ) Engine_Api::_()->core()->setSubject($video);

    if (!$this->_helper->requireSubject()->isValid()) return;

    $video = Engine_Api::_()->core()->getSubject('video');
    $viewer = Engine_Api::_()->user()->getViewer();

    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) {
        $message_view = true;
      }
    }
    //    $this->view->message_view = $message_view;
    if (!$message_view &&
      !$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid()
    ) {
      return;
    }

    $videoTags = $video->tags()->getTagMaps();

    // Check if edit/delete is allowed
    //    $this->view->can_edit =
    $can_edit = $this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->checkRequire();
    //    $this->view->can_delete =
    $can_delete = $this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->checkRequire();

    // check if embedding is allowed
    $can_embed = true;
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1)) {
      $can_embed = false;
    } else if (isset($video->allow_embed) && !$video->allow_embed) {
      $can_embed = false;
    }
    //    $this->view->can_embed = $can_embed;

    // increment count
    $embedded = "";
    if ($video->status == 1) {
      if (!$video->isOwner($viewer)) {
        $video->view_count++;
        $video->save();
      }
      $embedded = $video->getRichContent(true);
    }

    if ($video->type == 3 && $video->status == 1) {
      if (!empty($video->file_id)) {
        $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
        if ($storage_file) {
          $video_location = $storage_file->map();
        }
      }
    }

    $viewer_id = $viewer->getIdentity();
    $rating_count = Engine_Api::_()->video()->ratingCount($video->getIdentity());
    //    $this->view->video = $video;
    $rated = Engine_Api::_()->video()->checkRated($video->getIdentity(), $viewer->getIdentity());
    //Zend_Registry::get('Zend_View')?
    //    $this->view->videoEmbedded = $embedded;
    if ($video->category_id) {
      $category = Engine_Api::_()->video()->getCategory($video->category_id);
    }
    $options = array();
    if( $can_edit ){
      $options[] = array(
        'label' => $this->view->translate('Edit Video'),
        'attrs' => array(
          'href' => $this->view->url(array(
                      'module' => 'video',
                      'controller' => 'index',
                      'action' => 'edit',
                      'video_id' => $video->video_id
                    ), 'default', true)
        )
      );
    }
    if( $can_delete && $video->status != 2 ){
      $options[] = array(
        'label' => $this->view->translate('Delete Video'),
        'attrs' => array(
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
            'module' => 'video',
            'controller' => 'index',
            'action' => 'delete',
            'video_id' => $video->video_id,
          ), 'default', true)
        )
      );
    }
    if(!empty($options))
     $this->add($this->component()->customComponent('quickLinks', array('title' => $video->getTitle(), 'menu' => $options)));

    $controlGroup = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal', 'style' => 'text-align: center'));
    if( $can_embed ){
      $controlGroup->append($this->dom()->new_('a',
        array(
          'data-role' => 'button',
          'data-icon' => 'wrench',
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
            'module'=> 'video',
            'controller' => 'video',
            'action' => 'embed',
            'id' => $video->getIdentity(),
          ), 'default', true)), $this->view->translate('Embed')));

    }

    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $controlGroup->append($this->dom()->new_('a',
        array(
          'data-role' => 'button',
          'data-icon' => 'chat',
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
            'module'=> 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => 'video',
            'id' => $video->getIdentity(),
          ), 'default', true)), $this->view->translate('Share')));

      $controlGroup->append($this->dom()->new_('a',
        array(
          'data-role' => 'button',
          'data-icon' => 'flag',
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
            'module'=> 'core',
            'controller' => 'report',
            'action' => 'create',
            'route' => 'default',
            'subject' => $video->getGuid(),
          ), 'default', true)), $this->view->translate('Report')));
    }
    $this
      ->add($this->component()->html($this->dom()->new_('strong', array(), $this->view->translate(array('%s view', '%s views', $video->view_count), $this->view->locale()->toNumber($video->view_count)))))
      ->add($this->component()->video($video))
      ->add($this->component()->html('<br />' . $controlGroup))
      ->add($this->component()->comments())
      ->renderContent();
  }

  public function indexComposeUploadAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_redirect('login');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    $video_title = $this->_getParam('title');
    $video_url = $this->_getParam('uri');
    $video_type = $this->_getParam('type');
    $composer_type = $this->_getParam('c_type', 'wall');

    // extract code
    //$code = $this->extractCode("http://www.youtube.com/watch?v=5osJ8-NttnU&feature=popt00us08", $video_type);
    //$code = parse_url("http://vimeo.com/3945157/asd243", PHP_URL_PATH);

    $code = $this->extractCode($video_url, $video_type);
    // check if code is valid
    // check which API should be used
    if ($video_type == 1) {
      $valid = $this->checkYouTube($code);
    }
    if ($video_type == 2) {
      $valid = $this->checkVimeo($code);
    }


    // check to make sure the user has not met their quota of # of allowed video uploads
    // set up data needed to check quota
    $values['user_id'] = $viewer->getIdentity();
    $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);
    $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
    $current_count = $paginator->getTotalItemCount();

    if (($current_count >= $quota) && !empty($quota)) {
      // return error message
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first.');
    }


    else if ($valid) {
      $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
      $db->beginTransaction();

      try
      {
        $information = $this->handleInformation($video_type, $code);

        // create video
        $table = Engine_Api::_()->getDbtable('videos', 'video');
        $video = $table->createRow();
        $video->title = $information['title'];
        $video->description = $information['description'];
        $video->duration = $information['duration'];
        $video->owner_id = $viewer->getIdentity();
        $video->code = $code;
        $video->type = $video_type;
        $video->save();

        // Now try to create thumbnail
        $thumbnail = $this->handleThumbnail($video->type, $video->code);
        $ext = ltrim(strrchr($thumbnail, '.'), '.');
        $thumbnail_parsed = @parse_url($thumbnail);

        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

        $src_fh = fopen($thumbnail, 'r');
        $tmp_fh = fopen($tmp_file, 'w');
        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

        $image = Engine_Image::factory();
        $image->open($tmp_file)
          ->resize(120, 240)
          ->write($thumb_file)
          ->destroy();

        $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
          'parent_type' => $video->getType(),
          'parent_id' => $video->getIdentity()
        ));

        // If video is from the composer, keep it hidden until the post is complete
        if ($composer_type) $video->search = 0;

        $video->photo_id = $thumbFileRow->file_id;
        $video->status = 1;
        $video->save();
        $db->commit();
      }

      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }


      // make the video public
      if ($composer_type === 'wall') {
        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $i => $role)
        {
          $auth->setAllowed($video, $role, 'view', ($i <= $roles));
          $auth->setAllowed($video, $role, 'comment', ($i <= $roles));
        }
      }

      $this->view->status = true;
      $this->view->video_id = $video->video_id;
      $this->view->photo_id = $video->photo_id;
      $this->view->title = $video->title;
      $this->view->description = $video->description;
      $this->view->src = $video->getPhotoUrl();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video posted successfully');
    }
    else {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('We could not find a video there - please check the URL and try again.');
    }
  }

  public function indexValidationAction()
  {
    $video_type = $this->_getParam('type');
    $code = $this->_getParam('code');
    $ajax = $this->_getParam('ajax', false);
    $valid = false;

    // check which API should be used
    if ($video_type == "youtube") {
      $valid = $this->checkYouTube($code);
    }
    if ($video_type == "vimeo") {
      $valid = $this->checkVimeo($code);
    }

    $this->view->code = $code;
    $this->view->ajax = $ajax;
    $this->view->valid = $valid;
  }

  public function getNavigation()
  {
    $this->view->navigation = $navigation = new Zend_Navigation();
    $navigation->addPage(array(
      'label' => 'Browse Videos',
      'route' => 'video_general',
      'action' => 'browse',
      'controller' => 'index',
      'module' => 'video'
    ));

    if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $navigation->addPages(array(
        array(
          'label' => 'My Videos',
          'route' => 'video_general',
          'action' => 'manage',
          'controller' => 'index',
          'module' => 'video'
        ),
        array(
          'label' => 'Post New Video',
          'route' => 'video_general',
          'action' => 'create',
          'controller' => 'index',
          'module' => 'video'
        )
      ));
    }

    return $navigation;
  }

  // HELPER FUNCTIONS

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

        // fix for m.youtube.com
        if (strpos($code, 'watch') !== -1){
          $matches = array();
          parse_str(str_replace(array("watch", "?"), "", $code), $matches);
          if (!empty($matches['v'])){
            $code = $matches['v'];
          }
        }
        
        return $code;
      //vimeo
      case "2":
        // get the first variable after slash
        $code = @pathinfo($url);

        // fix for vimeo.com/m/
        if (strpos($code['basename'], '/m') !== -1){
          $code['basename'] = str_replace('/m', '', $code['basename']);
        }

        return $code['basename'];
    }
  }

  // YouTube Functions
  public function checkYouTube($code)
  {
    $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
    if (!$data = @file_get_contents($prefix."gdata.youtube.com/feeds/api/videos/" . $code)) return false;
    if ($data == "Video not found") return false;
    return true;
  }

  // Vimeo Functions
  public function checkVimeo($code)
  {
    $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
    //http://www.vimeo.com/api/docs/simple-api
    //http://vimeo.com/api/v2/video
    $data = @simplexml_load_file($prefix."vimeo.com/api/v2/video/" . $code . ".xml");
    $id = count($data->video->id);
    if ($id == 0) return false;
    return true;
  }

  public function compileFlowPlayer($video)
  {
    if ($video->type ==3){
      $video_location = Engine_Api::_()->storage()->get($video->file_id, $video->getType())->getHref();
    return "<object width=\"480\" height=\"386\" type=\"application/x-shockwave-flash\"
    data=\"".Zend_Registry::get('StaticBaseUrl')."externals/flowplayer/flowplayer-3.1.5.swf\"><param value=\"true\" name=\"allowfullscreen\">
      <param value=\"always\" name=\"allowscriptaccess\">
      <param value=\"high\" name=\"quality\">
      <param value=\"transparent\" name=\"wmode\">
      <param value=\"config={'clip':{'url':'" . $video_location . "','autoPlay':false,'duration':'" . $video->duration . "','autoBuffering':true},'plugins':{'controls':{'background':'#000000','bufferColor':'#333333','progressColor':'#444444','buttonColor':'#444444','buttonOverColor':'#666666'}},'canvas':{'backgroundColor':'#000000'}}\" name=\"flashvars\">
    </object>";
    }
  }
  // handles thumbnails
  public function handleThumbnail($type, $code = null)
  {
    $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
    switch ($type) {
      //youtube
      case "1":
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return $prefix."img.youtube.com/vi/$code/default.jpg";
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file($prefix."vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        return $thumbnail;
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
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
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
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return $information;
    }
  }

//  Video Controller {
  public function videoInit()
  {
    // Must be able to use videos
    if( !$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid() ) {
      return;
    }

    // Get subject
    $video = null;
    $id = $this->_getParam('video_id', $this->_getParam('id', null));
    if( $id ) {
      $video = Engine_Api::_()->getItem('video', $id);
      if( $video ) {
        Engine_Api::_()->core()->setSubject($video);
      }
    }

    // Require subject
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }

    // Require auth
    if( !$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid() ) {
      return;
    }
  }

  public function videoEmbedAction()
  {
    // Get subject
    $video = Engine_Api::_()->core()->getSubject('video');

    if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1) ) {
      $this->add($this->component()->tip($this->view->translate('Embedding of videos has been disabled.')))
      ->renderContent();
      return;
    } else if( isset($video->allow_embed) && !$video->allow_embed ) {
      $this->add($this->component()->tip($this->view->translate('Embedding of videos has been disabled for this video.')))
      ->renderContent();
      return;
    } else if( !$video || $video->status != 1 ) {
      $this->add($this->component()->tip($this->view->translate('The video you are looking for does not exist or has not been processed yet.')))
        ->renderContent();
      return;
    }

    // Get embed code
      $this->addPageInfo('embed_code', $video->getEmbedCode());
    $this
      ->add($this->component()->html($this->dom()->new_('textarea', array('onfocus' => 'this.select()', 'class' => 'embed_code'))))
      ->add($this->component()->html($this->dom()->new_('br')))
      ->renderContent();
  }

  public function videoExternalAction()
  {
    // Get subject
//    $video = Engine_Api::_()->core()->getSubject('video');

    // Check if embedding is allowed
//    if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1) ) {
      $this->add($this->component()->tip($this->view->translate('Embedding of videos has been disabled.')))
      ->renderContent();
      return;
//    } else if( isset($video->allow_embed) && !$video->allow_embed ) {
//      $this->add($this->component()->tip($this->view->translate('Embedding of videos has been disabled for this video.')))
//      ->renderContent();
//      return;
//    } else if( !$video || $video->status != 1 ) {
//      $this->add($this->component()->tip($this->view->translate('The video you are looking for does not exist or has not been processed yet.')))
//        ->renderContent();
//      return;
//    }
//
//    // Get embed code
//    $embedded = "";
//    if( $video->status == 1 ){
//      $video->view_count++;
//      $video->save();
//      $embedded = $video->getRichContent(true);
//    }
//
//    // Track views from external sources
//    Engine_Api::_()->getDbtable('statistics', 'core')
//        ->increment('video.embedviews');
//
//    // Get file location
//    if( $video->type == 3 && $video->status == 1 ) {
//      if( !empty($video->file_id) ) {
//        $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
//        if( $storage_file ) {
//          $this->view->video_location = $storage_file->map();
//        }
//      }
//    }
//
//    $rating_count = Engine_Api::_()->video()->ratingCount($video->getIdentity());
//    $category = null;
//    if( $video->category_id != 0 ) {
//      $category = Engine_Api::_()->video()->getCategory($video->category_id);
//    }
//    $this->add($this->component()->date($this->view->translate('Posted by %1$s on %2$s',
//              $this->view->htmlLink($video->getParent(), $video->getParent()->getTitle()),
//              $this->view->timestamp($video->creation_date)
//              )))
//      ->add($this->component()->html($this->dom()->new_('')))
//
//    $this->add($this->component()->html($this->video->description));

  }
//  } Video Controller

  protected function uploadVideo()
  {
    if( Engine_Api::_()->apptouch()->isApp() ) {
      $picup_files = $this->getPicupFiles('file');
      if( isset($picup_files[0]) && $picup_files[0] && file_exists($picup_files[0]) ) {
        $_FILES['file'] = array(
          'tmp_name' => $picup_files[0],
          'name' => $picup_files[0],
          'type' => 'application/octet-stream',
        );
      } else {
        return array(
          'status' => false,
          'message' => Zend_Registry::get('Zend_Translate')->_('No file'),
        );
      }

    } else {
      if( !$this->_helper->requireUser()->checkRequire() )
      {
        $this->view->status = false;
        $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
        return;
      }

      if( !$this->getRequest()->isPost() )
      {
        return array(
          'status' => false,
          'message' => Zend_Registry::get('Zend_Translate')->_('Invalid request method')
        );
      }

      if( empty($_FILES['file']) )
      {
        return array(
          'status' => false,
          'message' => Zend_Registry::get('Zend_Translate')->_('No file')
        );
      }
      // todo is_uploaded_file is not working
//    if( !isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name']) )
//    {
//      return array(
//        'status' => false,
//        'mesasge' => Zend_Registry::get('Zend_Translate')->_('Invalid Upload').print_r($_FILES, true)
//      );
//    }

      $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
      if( in_array(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION), $illegal_extensions) )
      {
        return array(
          'status' => false,
          'message' => Zend_Registry::get('Zend_Translate')->_('Invalid Upload')
        );
      }

      // todo ulans mode
      $_FILES["file"]["tmp_name"] = dirname($_FILES["file"]["tmp_name"]). DIRECTORY_SEPARATOR . $_FILES["file"]["name"];
    }

    $values = $this->getRequest()->getPost();

    $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $values['owner_id'] = $viewer->getIdentity();

      $params = array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity(),
      );
      $video = Engine_Api::_()->video()->createVideo($params, $_FILES['file'], $values);

      // sets up title and owner_id now just incase members switch page as soon as upload is completed
      $video->title = $_FILES['file']['name'];
      $video->owner_id = $viewer->getIdentity();
      $video->save();

      $db->commit();
      return array(
        'status' => true,
        'video' => $video
      );
    }

    catch( Exception $e )
    {
      $db->rollBack();
      // throw $e;
      return array(
        'status' => false,
        'message' => Zend_Registry::get('Zend_Translate')->_('An error occurred.').$e
      );
    }
  }
}
