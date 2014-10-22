<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 07.06.12
 * Time: 18:07
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_MusicController
  extends Apptouch_Controller_Action_Bridge
{
  protected $_roles = array(
    'everyone'            => 'Everyone',
    'registered'          => 'All Registered Members',
    'owner_network'       => 'Friends and Networks',
    'owner_member_member' => 'Friends of Friends',
    'owner_member'        => 'Friends Only',
    'owner'               => 'Just Me'
  );

  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    // Check auth
    if (!$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) {
      return;
    }

  }

  public function indexBrowseAction()
  {
    // Can create?
    $canCreate = Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');

    $values = array_filter($this->_getAllParams());

    // Show
    $viewer = Engine_Api::_()->user()->getViewer();
    if (@$values['show'] == 2 && $viewer->getIdentity()) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
    }
    unset($values['show']);

    // Get paginator
    $sName = Engine_Api::_()->getItemTable('music_playlist')->info('name');

    $select = Engine_Api::_()->music()->getPlaylistSelect($values);
     if ($search = $this->_getParam('search', false)) {
       $select->where('`' . $sName . '`.title LIKE ? OR `' . $sName . '`.description LIKE ?', '%' . $this->_getParam('search') . '%');
     }

    $paginator = Zend_Paginator::factory($select);
    if( !empty($params['page']) ) {
      $paginator->setCurrentPageNumber($values['page']);
    }
    if( !empty($params['limit']) ) {
      $paginator->setItemCountPerPage($values['limit']);
    }

    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->setFormat('browse')
      ->add($this->component()->itemSearch($this->getSearchForm()));
    if ($paginator->getTotalItemCount()) {
      $this->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($search) {
      if($canCreate)
        $this->add($this->component()->tip(
          $this->view->htmlLink(array(
                            'route' => 'music_general',
                            'action' => 'create'
                          ), $this->view->translate('Why don\'t you add some?')),
          $this->view->translate('APPTOUCH_There is no music uploaded with that criteria.')
        ));
      else
        $this->add($this->component()->tip(
          $this->view->translate('APPTOUCH_There is no music uploaded with that criteria.')
        ));
    } else {
      if($canCreate)
        $this->add($this->component()->tip(
          $this->view->htmlLink(array(
                            'route' => 'music_general',
                            'action' => 'create'
                          ), $this->view->translate('Why don\'t you add some?')),
          $this->view->translate('There is no music uploaded yet.')
        ));
      else
        $this->add($this->component()->tip(
          $this->view->translate('There is no music uploaded yet.')
        ));

    }
    $this->renderContent();

  }

  public function indexManageAction()
  {
    // only members can manage music
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $canCreate = Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');
    $values = $this->_getAllParams(); // todo array();

    // Get paginator
    $values['user'] = Engine_Api::_()->user()->getViewer()->getIdentity();
    //    $this->view->paginator =
    $sName = Engine_Api::_()->getItemTable('music_playlist')->info('name');

    $select = Engine_Api::_()->music()->getPlaylistSelect($values);
     if ($search = $this->_getParam('search', false)) {
       $select->where('`' . $sName . '`.title LIKE ? OR `' . $sName . '`.description LIKE ?', '%' . $this->_getParam('search') . '%');
     }

    $paginator = Zend_Paginator::factory($select);
    if( !empty($params['page']) ) {
      $paginator->setCurrentPageNumber($values['page']);
    }
    if( !empty($params['limit']) ) {
      $paginator->setItemCountPerPage($values['limit']);
    }

    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->setFormat('manage')
      ->add($this->component()->itemSearch($this->getSearchForm()));
          if ($paginator->getTotalItemCount()) {
            $this->add($this->component()->itemList($paginator, "manageItemData", array('listPaginator' => true,)))
//              ->add($this->component()->paginator($paginator))
            ;
          } elseif ($search) {
            if($canCreate)
              $this->add($this->component()->tip(
                $this->view->htmlLink(array(
                                  'route' => 'music_general',
                                  'action' => 'create'
                                ), $this->view->translate('Why don\'t you add some?')),
                $this->view->translate('APPTOUCH_There is no music uploaded with that criteria.')
              ));
            else
              $this->add($this->component()->tip(
                $this->view->translate('APPTOUCH_There is no music uploaded with that criteria.')
              ));
          } else {
            if($canCreate)
              $this->add($this->component()->tip(
                $this->view->htmlLink(array(
                                  'route' => 'music_general',
                                  'action' => 'create'
                                ), $this->view->translate('Why don\'t you add some?')),
                $this->view->translate('There is no music uploaded yet.')
              ));
            else
              $this->add($this->component()->tip(
                $this->view->translate('There is no music uploaded yet.')
              ));

          }
          $this->renderContent();

  }

  public function indexCreateAction()
  {
    // only members can upload music
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->isValid() ) {
      return;
    }

    $this->setFormat('create');
    // Get form
    $form = new Music_Form_Create();
    $form->removeElement('file');
    //$form->removeElement('submit');
    $form->addElement('File', 'files', array(
      'label' => 'Upload Music',
      'order' => 6,
      'accept' => 'audio/*',
      'isArray' => true
    ));
//    $form->files->addValidator('Extension', false, 'mp3,m4a,aac,mp4');

//    $playlist_id = $this->_getParam('playlist_id', '0');

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $values = $form->getValues();
      $translate= Zend_Registry::get('Zend_Translate');

      if(!empty($values['playlist_id']))
        $playlist = Engine_Api::_()->getItem('music_playlist', $values['playlist_id']);
      else {
        $playlist = Engine_Api::_()->getDbtable('playlists', 'music')->createRow();
        $playlist->title = strip_tags(trim($values['title']));
        if (empty($playlist->title))
          $playlist->title = $translate->_('_MUSIC_UNTITLED_PLAYLIST');

        $playlist->owner_type    = 'user';
        $playlist->owner_id      = Engine_Api::_()->user()->getViewer()->getIdentity();
        $playlist->description   = trim($values['description']);
        $playlist->search        = $values['search'];
        $playlist->save();
        $values['playlist_id']   = $playlist->playlist_id;

        // Assign $playlist to a Core_Model_Item

        $playlist = Engine_Api::_()->getItem('music_playlist', $values['playlist_id']);

        $picupFiles = $this->getPicupFiles('files');
        if (empty($picupFiles)){
          $files = $form->files->getFileName();
          if(is_string($files)) $files = array($files);
        }
        else
          $files = $picupFiles;
        // get file_id list
        foreach ($files as $file) {
          if( !preg_match('/\.(mp3|m4a|aac|mp4)$/iu', $file) ) {
            continue;
          }
          $song = Engine_Api::_()->getApi('core', 'music')->createSong($file);
          if (!empty($song)){
            $playlist->addSong($song);
          }
        }
        // Only create activity feed item if "search" is checked
        if ($playlist->search) {
          $activity = Engine_Api::_()->getDbtable('actions', 'activity');
          $action   = $activity->addActivity(
              Engine_Api::_()->user()->getViewer(),
              $playlist,
              'music_playlist_new',
              null, array('is_mobile' => true, 'count' => count($files))
          );
          if (null !== $action)
            $activity->attachActivity($action, $playlist);
        }
      }




      // Authorizations
      $auth      = Engine_Api::_()->authorization()->context;
      $prev_allow_comment = $prev_allow_view = false;
      foreach ($this->_roles as $role => $role_label) {
        // allow viewers
        if ($values['auth_view'] == $role || $prev_allow_view) {
          $auth->setAllowed($playlist, $role, 'view', true);
          $prev_allow_view = true;
        } else
          $auth->setAllowed($playlist, $role, 'view', 0);

        // allow comments
        if ($values['auth_comment'] == $role || $prev_allow_comment) {
          $auth->setAllowed($playlist, $role, 'comment', true);
          $prev_allow_comment = true;
        } else
          $auth->setAllowed($playlist, $role, 'comment', 0);
      }

      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($playlist) as $action ) {
        $actionTable->resetActivityBindings($action);
      }

      $art = $this->getPicupFiles('art');

      if( !empty($art) ) {
        $playlist->setPhoto($art[0]);
      } else
        if (!empty($values['art']))
          $playlist->setPhoto($form->art->getFileName());


      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }

    return $this->redirect($playlist);
  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function browseItemData(Core_Model_Item_Abstract $item)
  {
    $owner = $item->getOwner();
    $customize_fields = array(
      'descriptions' => array(
        $this->view->translate('Created by') . ' ' . $owner->getTitle()
      ),
      'photo' => $item->getPhotoUrl('thumb.normal'),
      'counter' => strtoupper($this->view->translate(array('%s track', '%s tracks', count($item->getSongs()->toArray())), $this->view->locale()->toNumber(count($item->getSongs()->toArray())))),
    );
    return $customize_fields;
  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function manageItemData(Core_Model_Item_Abstract $item)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $options = array();
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
    if ($item->getOwner()->isSelf($viewer)) {
      $options[] = array(
        'label' => $this->view->translate($item->profile ? 'Disable Profile Playlist' : 'Play on my Profile'),
        'attrs' => array(
          'href' => $item->getHref(array('route' => 'music_playlist_specific', 'action' => 'set-profile')),
          'class' => 'buttonlink music_set_profile_playlist ' . ($item->profile ? 'icon_music_disableonprofile' : 'icon_music_playonprofile')
        ),
      );
    }
    $owner = $item->getOwner();
    $customize_fields = array(
      'description' => null,
      'owner_id' => null,
      'owner' => null,
      'photo' => $owner->getPhotoUrl('thumb.normal'),
      'counter' => strtoupper($this->view->translate(array('%s track', '%s tracks', count($item->getSongs()->toArray())), $this->view->locale()->toNumber(count($item->getSongs()->toArray())))),
      'manage' => $options
    );
    return $customize_fields;
  }


  private function prepareListData($paginator)
  {
    $items = array();
    foreach ($paginator as $item) {
      $owner = $item->getOwner();
      $items[] = array(
        'title' => $item->getTitle(),
        'descriptions' => array(
          $this->view->translate('Created by') . ' ' . $owner->getTitle()
        ),
        'href' => $item->getHref(),
        'photo' => $item->getPhotoUrl('thumb.normal'),
        'creation_date' => $this->view->timestamp(strtotime($item->creation_date)),
        'counter' => strtoupper($this->view->translate(array('%s track', '%s tracks', count($item->getSongs()->toArray())), $this->view->locale()->toNumber(count($item->getSongs()->toArray())))),
        'owner_id' => $owner->getIdentity(),
        'owner' => array(
          'id' => $owner->getIdentity(),
          'type' => $owner->getType(),
          'title' => $owner->getTitle(),
          'href' => $owner->getHref(),
          'photo' => $owner->getPhotoUrl('thumb.icon'),
        )
      );
    }

    return $items;
  }


//  PlaylistController {
  public function playlistInit()
  {
    $this->addPageInfo('contentTheme', 'd');
    // Check auth
    if (!$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) {
      return;
    }

    // Get viewer info
    $this->viewer = Engine_Api::_()->user()->getViewer();
    $this->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    // Get subject
    if (null !== ($playlist_id = $this->_getParam('playlist_id')) &&
      null !== ($playlist = Engine_Api::_()->getItem('music_playlist', $playlist_id)) &&
      $playlist instanceof Music_Model_Playlist &&
      !Engine_Api::_()->core()->hasSubject()
    ) {
      Engine_Api::_()->core()->setSubject($playlist);
    }
  }

  public function playlistViewAction()
  {

    // Check subject
    if (!$this->_helper->requireSubject()->isValid()) {
      return $this->renderContent();
    }

    // Get viewer/subject
    $viewer = Engine_Api::_()->user()->getViewer();
    $playlist = Engine_Api::_()->core()->getSubject('music_playlist');

    // Increment view count
    if (!$viewer->isSelf($playlist->getOwner())) {
      $playlist->view_count++;
      //$playlist->play_count++;
      $playlist->save();
    }
    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_view = false;
    if (null !== ($message_id = $this->_getParam('message'))) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      $message_view = $conversation->hasRecipient($viewer);
    }
    //    $this->view->message_view = $message_view;

    // Check auth
    if (!$message_view && !$this->_helper->requireAuth()->setAuthParams($playlist, $viewer, 'view')->isValid()) {
      return $this->renderContent();
    }
    $options = array();
    if ($playlist->isEditable()){
      $options[] = array(
        'label' => $this->view->translate('Edit Playlist'),
        'attrs' => array(
          'href' => $playlist->getHref(array('route' => 'music_playlist_specific', 'action' => 'edit'))
        )
      );
    }

    if ($playlist->isDeletable()) {
      $options[] = array(
        'label' => $this->view->translate('Delete Playlist'),
        'attrs' => array(
          'href' => $playlist->getHref(array('route' => 'music_playlist_specific', 'action' => 'delete'))
        )
      );
    }

    $controlGroup = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal', 'style' => 'text-align: center;'));

    if( $viewer && $viewer->getIdentity() ) {
      $controlGroup->append($this->dom()->new_('a',
        array(
          'data-role' => 'button',
          'data-icon' => 'chat',
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
            'module'=>'activity',
            'controller'=>'index',
            'action'=>'share',
            'type'=>'music_playlist',
            'id' => $playlist->getIdentity()), 'default', true)), $this->view->translate('Share')))

        ->append($this->dom()->new_('a',
        array(
          'data-role' => 'button',
          'data-icon' => 'flag',
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
            'module'=>'core',
            'controller'=>'report',
            'action'=>'create',
            'subject'=>$playlist->getGuid(),
            'id' => $playlist->getIdentity()), 'default', true)), $this->view->translate('Report')));
    }

    if ($playlist->getOwner()->isSelf( Engine_Api::_()->user()->getViewer() )) {
      $controlGroup->append($this->dom()->new_('a',
        array(
          'data-role' => 'button',
          'data-icon' => 'music',
          'href' => $playlist->getHref(array('route' => 'music_playlist_specific', 'action' => 'set-profile'))
        ),
        $this->view->translate($playlist->profile ? 'Disable Profile Playlist' : 'Play on my Profile')
      ));
    }
     if(!empty($options))
      $this->add($this->component()->customComponent('quickLinks', array('title' => $playlist->getTitle(), 'menu' => $options)));
    /**
     * @var $songs Engine_Db_Table_Rowset
     * */
    $songs = $playlist->getSongs();
    $this->setFormat('view')
      ->add($this->component()->subjectPhoto($playlist))
      ->add($this->component()->html($controlGroup . '<br />'))
      ->add($this->component()->playlist($songs))
      ->add($this->component()->mediaControls())
      ->add($this->component()->html('<br />'))
      ->renderContent();

  }

  public function playlistEditAction()
  {
    // catch uploads from FLASH fancy-uploader and redirect to uploadSongAction()
    if ($this->getRequest()->getQuery('ul', false)) {
      return $this->_forward('add-song', null, null, array('format' => 'json'));
    }

    // only members can upload music
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    if (!$this->_helper->requireSubject('music_playlist')->isValid()) {
      return;
    }

    // Get playlist
    $playlist = Engine_Api::_()->core()->getSubject('music_playlist');

    // only user and admins and moderators can edit
    if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'edit')->isValid()) {
      return;
    }

    // Get navigation
    $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('music_main', array(), 'music_main_manage');

    // Make form
    $form = new Music_Form_Edit();
    $form->removeElement('file');
//    $form->addElement('File', 'file', array(
//      'label' => 'Upload Music',
//      'order' => 6,
//      'isArray' => true
//    ));
    $form->populate($playlist);

    if (!$this->getRequest()->isPost()) {
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

    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $form->saveValues();
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }

    return $this->redirect($playlist);
  }

  public function playlistDeleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $playlist = Engine_Api::_()->getItem('music_playlist', $this->getRequest()->getParam('playlist_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'delete')->isValid()) return;

    $form = new Music_Form_Delete();

    if (!$playlist) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Playlist doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $db = $playlist->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      foreach ($playlist->getSongs() as $song) {
        $song->deleteUnused();
      }
      $playlist->delete();
      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'music_general', true), Zend_Registry::get('Zend_Translate')->_('The selected playlist has been deleted.'), true);
  }

  public function playlistSetProfileAction()
  {
//    if (!$this->getRequest()->isPost()) {
//      return;
//    }
    if (!$this->_helper->requireSubject('music_playlist')->isValid()) {
      return;
    }

    // Get playlist
    $this->view->playlist = $playlist = Engine_Api::_()->core()->getSubject('music_playlist');
    $this->view->playlist_id = $playlist_id = $playlist->getIdentity();

    // Check owner
    if ($playlist->owner_id != Engine_Api::_()->user()->getViewer()->getIdentity()) {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();

    try {
      $playlist->setProfile();

      $this->view->success = true;
      $this->view->enabled = $playlist->profile;

      $db->commit();
    } catch (Exception $e) {
      $this->view->success = false;

      $db->rollback();
    }

    // Redirect
    return $this->redirect($this->view->url(array('controller' => 'index', 'action' => 'manage')));
  }

  public function playlistSortAction()
  {
    if (!$this->_helper->requireSubject('music_playlist')->isValid()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid playlist');
      return;
    }

    // Get playlist
    $this->view->playlist = $playlist = Engine_Api::_()->core()->getSubject('music_playlist');
    $this->view->playlist_id = $playlist_id = $playlist->getIdentity();

    // only user and admins and moderators can edit
    if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'edit')->isValid()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Not allowed to edit this playlist');
      return;
    }


    $songs = $playlist->getSongs();
    $order = explode(',', $this->getRequest()->getParam('order'));
    foreach ($order as $i => $item) {
      $song_id = substr($item, strrpos($item, '_') + 1);
      foreach ($songs as $song) {
        if ($song->song_id == $song_id) {
          $song->order = $i;
          $song->save();
        }
      }
    }

    $this->view->songs = $playlist->getSongs()->toArray();
  }


  public function playlistAddSongAction()
  {
    // Check user
    if (!$this->_helper->requireUser()->isValid()) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('You must be logged in.');
      return;
    }

    // Check auth
    if (!$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->checkRequire()) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('You are not allowed to upload songs.');
      return;
    }

    // Prepare
    $viewer = Engine_Api::_()->user()->getViewer();
    $playlistTable = Engine_Api::_()->getDbTable('playlists', 'music');

    // Get special playlist
    if (0 >= ($playlist_id = $this->_getParam('playlist_id')) &&
      false != ($type = $this->_getParam('type'))
    ) {
      $playlist = $playlistTable->getSpecialPlaylist($viewer, $type);
      Engine_Api::_()->core()->setSubject($playlist);
    }

    // Check subject
    if (!$this->_helper->requireSubject('music_playlist')->checkRequire()) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Invalid playlist');
      return;
    }

    // Get playlist
    $this->view->playlist = $playlist = Engine_Api::_()->core()->getSubject('music_playlist');
    $this->view->playlist_id = $playlist_id = $playlist->getIdentity();

    // check auth
    if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'edit')->isValid()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('You are not allowed to edit this playlist');
      return;
    }

    // Check file
    $values = $this->getRequest()->getPost();
    if (empty($values['Filename']) || empty($_FILES['Filedata'])) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('No file');
      return;
    }


    // Process
    $db = Engine_Api::_()->getDbtable('playlists', 'music')->getAdapter();
    $db->beginTransaction();

    try {

      // Create song
      $file = Engine_Api::_()->getApi('core', 'music')->createSong($_FILES['Filedata']);
      if (!$file) {
        throw new Music_Model_Exception('Song was not successfully attached');
      }

      // Add song
      $song = $playlist->addSong($file);
      if (!$song) {
        throw new Music_Model_Exception('Song was not successfully attached');
      }

      // Response
      $this->view->status = true;
      $this->view->song = $song;
      $this->view->song_id = $song->getIdentity();
      $this->view->song_url = $song->getFilePath();
      $this->view->song_title = $song->getTitle();

      $db->commit();

    } catch (Music_Model_Exception $e) {
      $db->rollback();

      $this->view->status = false;
      $this->view->message = $this->view->translate($e->getMessage());
      return;

    } catch (Exception $e) {
      $db->rollback();

      $this->view->status = false;
      $this->view->message = $this->view->translate('Upload failed by database query');

      throw $e;
    }
  }

//  } PlaylistController

//  SongController {
  public function songInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    // Check auth
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) {
      return;
    }

    // Get viewer info
    $this->viewer     = Engine_Api::_()->user()->getViewer();
    $this->viewer_id  = Engine_Api::_()->user()->getViewer()->getIdentity();

    // Get subject
    if( null !== ($song_id = $this->_getParam('song_id')) &&
        null !== ($song = Engine_Api::_()->getItem('music_playlist_song', $song_id)) &&
        $song instanceof Music_Model_PlaylistSong ) {
      Engine_Api::_()->core()->setSubject($song);
    }
  }

  public function songRenameAction()
  {
    // Check subject
    if( !Engine_Api::_()->core()->hasSubject('music_playlist_song') ) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Not a valid song');
      return;
    }

    // Check method
    if( !$this->getRequest()->isPost() ) {
      $this->view->success = false;
      return;
    }

    // Get song/playlist
    $song = Engine_Api::_()->core()->getSubject('music_playlist_song');
    $playlist = $song->getParent();

    // Check song/playlist
    if( !$song || !$playlist ) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Invalid playlist');
      return;
    }

    // Check auth
    if( !Engine_Api::_()->authorization()->isAllowed($playlist, null, 'edit') ) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Not allowed to edit this playlist');
      return;
    }


    // Process
    $db = $song->getTable()->getAdapter();
    $db->beginTransaction();
    try {

      $song->setTitle( $this->_getParam('title') );

      $db->commit();
    } catch (Exception $e) {
      $db->rollback();

      $this->view->success = false;
      $this->view->error   = $this->view->translate('Unknown database error');
      throw $e;
    }

    $this->view->success = true;
  }

  public function songDeleteAction()
  {
    // Check subject
    if( !Engine_Api::_()->core()->hasSubject('music_playlist_song') ) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Not a valid song');
      return;
    }

    // Check method
    if( !$this->getRequest()->isPost() ) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Invalid request method');
      return;
    }

    // Get song/playlist
    $song = Engine_Api::_()->core()->getSubject('music_playlist_song');
    $playlist = $song->getParent();

    // Check song/playlist
    if( !$song || !$playlist ) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Invalid playlist');
      return;
    }

    // Check auth
    if( !Engine_Api::_()->authorization()->isAllowed($playlist, null, 'edit') ) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Not allowed to edit this playlist');
      return;
    }

    // Get file
    $file = Engine_Api::_()->getItem('storage_file', $song->file_id);
    if( !$file ) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Invalid playlist');
      return;
    }

    $db = $song->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $song->deleteUnused();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();

      $this->view->success = false;
      $this->view->error = $this->view->translate('Unknown database error');
      throw $e;
    }

    $this->view->success = true;
  }

  public function songTallyAction()
  {
    // Check subject
    if( !Engine_Api::_()->core()->hasSubject('music_playlist_song') ) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Not a valid song');
      return;
    }

    // Get song/playlist
    $song = Engine_Api::_()->core()->getSubject('music_playlist_song');
    $playlist = $song->getParent();

    // Check song
    if( !$song || !$playlist ) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('invalid song_id');
      return;
    }


    // Process
    $db = $song->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $song->play_count++;
      $song->save();

      $playlist->play_count++;
      $playlist->save();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      $this->view->success = false;
      return;
    }

    $this->view->success = true;
    $this->view->song = $song->toArray();
    $this->view->play_count = $song->playCountLanguagified();
  }

  public function songAppendAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject('music_playlist_song')->isValid() ) {
      return;
    }

    // Set song
    $song = Engine_Api::_()->core()->getSubject('music_playlist_song');

    $viewer = Engine_Api::_()->user()->getViewer();

    // Get form
    $form = new Music_Form_Song_Append();

    // Populate form
    $songTable = $song->getTable();
    $playlistTable = Engine_Api::_()->getDbtable('playlists', 'music');
    $playlists = $playlistTable->select()
      ->from($playlistTable, array('playlist_id', 'title'))
      ->where('owner_type = ?', 'user')
      ->where('owner_id = ?', $viewer->getIdentity())
      ->query()
      ->fetchAll();
    foreach( $playlists as $playlist ) {
      if( $playlist['playlist_id'] != $song->playlist_id ) {
        $form->playlist_id->addMultiOption($playlist['playlist_id'], $playlist['title']);
      }
    }

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }


    // Get values
    $values = $form->getValues();
    if( empty($values['playlist_id']) && empty($values['title']) ) {
      $form->addError('Please enter a title or select a playlist.');
      $this
        ->add($this->component()->form($form))
        ->renderContent();
        return;
    }



    // Process
    $db = $song->getTable()->getAdapter();
    $db->beginTransaction();

    try {

      // Existing playlist
      if( !empty($values['playlist_id']) ) {
        $playlist = Engine_Api::_()->getItem('music_playlist', $values['playlist_id']);

        // already exists in playlist
        $alreadyExists = $songTable->select()
          ->from($songTable, 'song_id')
          ->where('playlist_id = ?', $playlist->getIdentity())
          ->where('file_id = ?', $song->file_id)
          ->limit(1)
          ->query()
          ->fetchColumn()
          ;
        if( $alreadyExists ) {
          return $form->getElement('playlist_id')->addErrorMessage('This playlist already has this song.');
        }
      }

      // New playlist
      else {
        $playlist = $playlistTable->createRow();
        $playlist->title = trim($values['title']);
        $playlist->owner_type = 'user';
        $playlist->owner_id = $viewer->getIdentity();
        $playlist->search = 1;
        $playlist->save();

        // Add action and attachments
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($playlist, 'registered', 'comment', true);
        foreach( array('everyone', 'registered', 'member') as $role ) {
          $auth->setAllowed($playlist, $role, 'view', true);
        }

        // Only create activity feed item if "search" is checked
        if( $playlist->search ) {
          $activity = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $activity->addActivity(Engine_Api::_()->user()->getViewer(),
              $playlist, 'music_playlist_new', null, array('is_mobile' => true));
          if( $action ) {
            $activity->attachActivity($action, $playlist);
          }
        }
      }

      // Add song
      $playlist->addSong($song->file_id);

      // Response
      $this->view->success = true;
      $this->view->message = $this->view->translate('Your changes have been saved.');
      $this->view->playlist = $playlist;

      $db->commit();
      $this
        ->add($this->component()->form($form))
        ->renderContent();

    } catch( Music_Model_Exception $e ) {
      $this->view->success = false;
      $this->view->error = $this->view->translate($e->getMessage());
      $form->addError($e->getMessage());
      $this
        ->add($this->component()->form($form))
        ->renderContent();

      $db->rollback();

    } catch( Exception $e ) {
      $this->view->success = false;
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      $db->rollback();
    }
  }

  public function songUploadAction()
  {
    // only members can upload music
    if( !$this->_helper->requireUser()->checkRequire() ) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Max file size limit exceeded or session expired.');
      return;
    }

    // Check method
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid request method');
      return;
    }

    // Check file
    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) || empty($_FILES['Filedata']) ) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('No file');
      return;
    }


    // Process
    $db = Engine_Api::_()->getDbtable('playlists', 'music')->getAdapter();
    $db->beginTransaction();

    try {
      $song = Engine_Api::_()->getApi('core', 'music')->createSong($_FILES['Filedata']);
      $this->view->status   = true;
      $this->view->song     = $song;
      $this->view->song_id  = $song->getIdentity();
      $this->view->song_url = $song->getHref();
      $db->commit();

    } catch( Music_Model_Exception $e ) {
      $db->rollback();

      $this->view->status = false;
      $this->view->message = $this->view->translate($e->getMessage());

    } catch( Exception $e ) {
      $db->rollback();

      $this->view->status  = false;
      $this->view->message = $this->view->translate('Upload failed by database query');

      throw $e;
    }
  }

//  } SongController



}
