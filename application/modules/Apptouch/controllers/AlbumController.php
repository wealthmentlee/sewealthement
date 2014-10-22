<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 05.06.12
 * Time: 17:50
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_AlbumController
  extends Apptouch_Controller_Action_Bridge
{
//  IndexController {

  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');
  }

  public function indexBrowseAction()
  {
    //     ------------------------- New { ----------------------------------
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) return;
    $settings = Engine_Api::_()->getApi('settings', 'core');
    // Get params
    switch ($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }


    // Prepare data
    $table = Engine_Api::_()->getItemTable('album');
    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $select = $table->select()
      ->where("search = 1")
      ->order($order . ' DESC');

    $user_id = $this->_getParam('user');
    if ($user_id) $select->where("owner_id = ?", $user_id);
    if ($this->_getParam('category_id')) $select->where("category_id = ?", $this->_getParam('category_id'));

    if ($search = $this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    // Prepare Data  {
    $select = $this->getSelectBrowse();
    // } Prepare Data

     $canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($settings->getSetting('album_page', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    //Form
    $form = $this->getSearchForm();
    $form->setMethod('get');
    //    $form->getElement('sort')->setValue($this->_getParam('sort'));
    $form->getElement('search')->setValue($this->_getParam('search'));
    //    $category_id = $form->getElement('category_id');
    //    if ($category_id) {
    //      $category_id->setValue($this->_getParam('category_id'));
    //    }
    //Form
    $this->setFormat('browse')
      ->add($this->component()->itemSearch($form));

    if ($paginator->getTotalItemCount() > 0) {
      $this->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($search) {
      $this->add($this->component()->tip(
        $this->view->translate('APPTOUCH_Nobody has created an album with that criteria.')
      ));
    } else {
      if($canCreate)
        $this->add($this->component()->tip($this->view->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->view->url(array('action' => 'upload')) . '">', '</a>'), $this->view->translate('Nobody has created an album yet.')));
      else
        $this->add($this->component()->tip($this->view->translate('Nobody has created an album yet.')));
    }
    $this->renderContent();
    //With PAGE URL
    //     ------------------------- } New ----------------------------------
  }

  public function indexManageAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid()) return;
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $form = new Apptouch_Form_Search();
    $form->getElement('search')->setValue($this->_getParam('search'));

    $page = $this->_getParam('page');

    // Get params
    switch ($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }

    // Prepare data
    $user = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getItemTable('album');

    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $select = $table->select()
      ->where('owner_id = ?', $user->getIdentity())
      ->order($order . ' DESC');

    if ($this->_getParam('category_id')) $select->where("category_id = ?", $this->_getParam('category_id'));

    if ($search = $this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($settings->getSetting('album_page', 25));
    $paginator->setCurrentPageNumber($page);

    $canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');

    $this->setFormat('manage')
      ->add($this->component()->itemSearch($form));

    if ($paginator->getTotalItemCount() > 0) {
      $this->add($this->component()->itemList($paginator, "manageItemData", array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($search) {
      $this->add($this->component()->tip(
        $this->view->translate('APPTOUCH_Nobody has created an album with that criteria.')
      ));
    } else {
      if($canCreate)
        $this->add($this->component()->tip($this->view->translate('Get started by %1$screating%2$s your first album!', '<a href="' . $this->view->url(array('action' => 'upload')) . '">', '</a>'), $this->view->translate('You do not have any albums yet.')));
      else
        $this->add($this->component()->tip($this->view->translate('You do not have any albums yet.')));
    }
    $this->renderContent();

  }

  public function indexUploadAction()
  {
    if (isset($_GET['ul'])) return $this->_forward('upload-photo', null, null, array('format' => 'json'));

    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid()) return;

    // Get form
    $form = new Album_Form_Album();
    $form->removeElement('file');
    $form->addElement('File', 'photos', array(
      'label' => 'APPTOUCH_Upload Photos',
      'order' => 4,
      'isArray' => true,
      'multiple' => 'multiple'
    ));
    $form->photos->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    $this->setFormat('create');
    if (!$this->getRequest()->isPost()) {
      if (null !== ($album_id = $this->_getParam('album_id'))) {
        $form->populate(array(
          'album' => $album_id
        ));
      }
      return $this->add($this->component()->form($form))
        ->renderContent();
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return $this->add($this->component()->form($form))
        ->renderContent();
    }
    $db = Engine_Api::_()->getItemTable('album')->getAdapter();
    $db->beginTransaction();


    try
    {
      $set_cover = false;
      $values = $form->getValues();
      $params = Array();
      if ((empty($values['owner_type'])) || (empty($values['owner_id']))) {
        $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
        $params['owner_type'] = 'user';
      } else {
        $params['owner_id'] = $values['owner_id'];
        $params['owner_type'] = $values['owner_type'];
        throw new Zend_Exception("Non-user album owners not yet implemented");
      }

      if (($values['album'] == 0)) {
        $params['title'] = $values['title'];
        if (empty($params['title'])) {
          $params['title'] = "Untitled Album";
        }
        $params['category_id'] = (int)@$values['category_id'];
        $params['description'] = $values['description'];
        $params['search'] = $values['search'];

        $album = Engine_Api::_()->getDbtable('albums', 'album')->createRow();
        $album->setFromArray($params);
        $album->save();

        $set_cover = true;

        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        if (empty($values['auth_view'])) {
          $values['auth_view'] = key($form->auth_view->options);
          if (empty($values['auth_view'])) {
            $values['auth_view'] = 'everyone';
          }
        }
        if (empty($values['auth_comment'])) {
          $values['auth_comment'] = key($form->auth_comment->options);
          if (empty($values['auth_comment'])) {
            $values['auth_comment'] = 'owner_member';
          }
        }
        if (empty($values['auth_tag'])) {
          $values['auth_tag'] = key($form->auth_tag->options);
          if (empty($values['auth_tag'])) {
            $values['auth_tag'] = 'owner_member';
          }
        }

        $viewMax = array_search($values['auth_view'], $roles);
        $commentMax = array_search($values['auth_comment'], $roles);
        $tagMax = array_search($values['auth_tag'], $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
          $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
        }
      }
      else
      {
        if (!isset($album)) {
          $album = Engine_Api::_()->getItem('album', $values['album']);
        }
      }


      // Do other stuff
      if (isset($values['file']))
        foreach ($values['file'] as $photo_id)
        {
          $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
          if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) continue;

          if ($set_cover) {
            $album->photo_id = $photo_id;
            $album->save();
            $set_cover = false;
          }

          $photo->album_id = $album->album_id;
          $photo->order = $photo_id;
          $photo->save();

        }
      //-------------------------- Save Photos ------------------------------------

      $photodb = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
      $photodb->beginTransaction();

      $viewer = Engine_Api::_()->user()->getViewer();

      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
      $picupFiles = $this->getPicupFiles('photos');
      if (empty($picupFiles))
        $photos = $form->photos->getFileName();
      else
        $photos = $picupFiles;
      $count = 0;

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('is_mobile' => true, 'count' => count($photos)));
if(!$photos) {
                $form->addError(Zend_Registry::get('Zend_Translate')->_('There is no photos'));
                return $this->add($this->component()->form($form))
                    ->renderContent();
            }
      if (is_array($photos))
        foreach ($photos as $photoPath) {

          $photo = $photoTable->createRow();
          $photo->setFromArray(array(
            'owner_type' => 'user',
            'owner_id' => $viewer->getIdentity()
          ));
          $photo->save();

          $photo->order = $photo->photo_id;
          $photo->album_id = $album->getIdentity();
          $photo->setPhoto($photoPath);
          $photo->save();
          if ($action instanceof Activity_Model_Action && $count < 8) {
            $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
          }
          $count++;

        } else {
        $photo = $photoTable->createRow();
        $photo->setFromArray(array(
          'owner_type' => 'user',
          'owner_id' => $viewer->getIdentity()
        ));
        $photo->save();

        $photo->order = $photo->photo_id;
        $photo->album_id = $album->getIdentity();
        $photo->setPhoto($photos);
        $photo->save();
      }


      //      $this->view->status = true;
      //      $this->view->name = $_FILES['Filedata']['name'];
      //      $this->view->photo_id = $photo->photo_id;

      $photodb->commit();


      //-------------------------- Save Photos ------------------------------------

      $db->commit();
    }
    catch (Album_Model_Exception $e) {
      $photodb->rollBack();
      $this->view->status = false;
      $this->view->error = $this->view->translate($e->getMessage());
      throw $e;
      return;

    } catch (Exception $e) {
      $photodb->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      throw $e;
      return;
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }


    $this->redirect($this->view->url(array('action' => 'editphotos', 'album_id' => $album->album_id), 'album_specific', true));
  }

  private function handleIosQuirk($name)
  {
    foreach($_FILES[$name]['name'] as $key => $filename){
      if($_FILES[$name]['name'][$key])
        $_FILES[$name]['name'][$key] = $key . $filename;
    }
  }
//  } IndexController

// AlbumController {

  public function albumInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) return;

    if (0 !== ($photo_id = (int)$this->_getParam('photo_id')) &&
      null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))
    ) {
      Engine_Api::_()->core()->setSubject($photo);
    }

    else if (0 !== ($album_id = (int)$this->_getParam('album_id')) &&
      null !== ($album = Engine_Api::_()->getItem('album', $album_id))
    ) {
      Engine_Api::_()->core()->setSubject($album);
    }
  }

  public function albumViewAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if (!$this->_helper->requireSubject('album')->isValid()) return;
    $album = Engine_Api::_()->core()->getSubject();
    if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'view')->isValid()) return;

    // Prepare params
    $page = $this->_getParam('page');

    // Prepare data
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $paginator = $photoTable->getPhotoPaginator(array(
      'album' => $album,
    ));
    $paginator->setItemCountPerPage( /*$settings->getSetting('album_page', 25)*/
      5);
    $paginator->setCurrentPageNumber($page);

    // Do other stuff
    $mine = true;
    $canEdit = $this->_helper->requireAuth()->setAuthParams($album, null, 'edit')->checkRequire();
    if (!$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
      $album->getTable()->update(array(
        'view_count' => new Zend_Db_Expr('view_count + 1'),
      ), array(
        'album_id = ?' => $album->getIdentity(),
      ));
      $mine = false;
    }
    $this->setFormat('view')
      ->add($this->component()->gallery($paginator, null, array('canComment' => true)));
    $controlGroup = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal', 'style' => 'text-align: center'));
    $viewer = Engine_Api::_()->user()->getViewer();
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
            'type'=>'album',
            'id' => $album->getIdentity()), 'default', true)), $this->view->translate('Share')))

        ->append($this->dom()->new_('a',
        array(
          'data-role' => 'button',
          'data-icon' => 'flag',
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
            'module'=>'core',
            'controller'=>'report',
            'action'=>'create',
            'subject'=>$album->getGuid(),
            'id' => $album->getIdentity()), 'default', true)), $this->view->translate('Report')));

      $this->add($this->component()->html($controlGroup . '<br />'));
    }

    $this->add($this->component()->paginator($paginator));
    if ($canEdit || $mine)
      $this->add($this->component()->quickLinks($this->getOptions(Engine_Api::_()->core()->getSubject(), "manage")));
    $this->renderContent();
  }

  public function albumEditAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('album')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid()) return;
    $this->setFormat('create');
    // Get navigation
    $navigation = Engine_Api::_()->getApi('menus', 'apptouch')
      ->getNavigation('album_main');

    // Hack navigation
    foreach ($navigation->getPages() as $page)
    {
      if ($page->route != 'album_general' || $page->action != 'manage') continue;
      $page->active = true;
    }

    // Prepare data
    $album = Engine_Api::_()->core()->getSubject();

    // Make form
    $form = new Album_Form_Album_Edit();

    if (!$this->getRequest()->isPost()) {
      $form->populate($album->toArray());
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      foreach ($roles as $role) {
        if (1 === $auth->isAllowed($album, $role, 'view') && isset($form->auth_view)) {
          $form->auth_view->setValue($role);
        }
        if (1 === $auth->isAllowed($album, $role, 'comment') && isset($form->auth_comment)) {
          $form->auth_comment->setValue($role);
        }
        if (1 === $auth->isAllowed($album, $role, 'tag') && isset($form->auth_tag)) {
          $form->auth_tag->setValue($role);
        }
      }

      $this->view->status = false;
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $album->setFromArray($values);
      $album->save();

      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if (empty($values['auth_view'])) {
        $values['auth_view'] = key($form->auth_view->options);
        if (empty($values['auth_view'])) {
          $values['auth_view'] = 'everyone';
        }
      }
      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = key($form->auth_comment->options);
        if (empty($values['auth_comment'])) {
          $values['auth_comment'] = 'owner_member';
        }
      }
      if (empty($values['auth_tag'])) {
        $values['auth_tag'] = key($form->auth_tag->options);
        if (empty($values['auth_tag'])) {
          $values['auth_tag'] = 'owner_member';
        }
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $tagMax = array_search($values['auth_tag'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
      }

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($album) as $action) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($this->view->url(array('action' => 'manage'), 'album_general', true));
  }

  public function albumDeleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $album = Engine_Api::_()->getItem('album', $this->getRequest()->getParam('album_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'delete')->isValid()) return;


    $form = new Album_Form_Album_Delete();

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

    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $album->delete();
      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect(
      $this->view->url(array('action' => 'manage'), 'album_general', true),
      Zend_Registry::get('Zend_Translate')->_('Album has been deleted.'),
      true
    );
  }

  public function albumEditphotosAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('album')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid()) return;
    //
    //    // Get navigation
    //    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
    //      ->getNavigation('album_main');
    //
    //    // Hack navigation
    //    foreach( $navigation->getPages() as $page ) {
    //      if( $page->route != 'album_general' || $page->action != 'manage' ) continue;
    //      $page->active = true;
    //    }

    // Prepare data
    $this->view->album = $album = Engine_Api::_()->core()->getSubject();
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
      'album' => $album,
    ));
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(10);

    // Get albums
    $albumTable = Engine_Api::_()->getItemTable('album');
    $myAlbums = $albumTable->select()
      ->from($albumTable, array('album_id', 'title'))
      ->where('owner_type = ?', 'user')
      ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
      ->query()
      ->fetchAll();

    $albumOptions = array('' => '');
    foreach ($myAlbums as $myAlbum) {
      $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
    }

    // Make form
    $this->view->form = $form = new Album_Form_Album_Photos();

    foreach ($paginator as $photo) {
      $subform = new Album_Form_Album_EditPhoto(array('elementsBelongTo' => $photo->getGuid()));
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
      $subform->move->setMultiOptions($albumOptions);
    }
    $this->setFormat('html');
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->html($this->view->render('album/album-editphotos.tpl')))
        ->add($this->component()->paginator($paginator))
        ->renderContent();
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->html($this->view->render('album/album-editphotos.tpl')))
        ->add($this->component()->paginator($paginator))
        ->renderContent();
      return;
    }

    $table = $album->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      if (!empty($values['cover'])) {
        $album->photo_id = $values['cover'];
        $album->save();
      }


      // Process
      foreach ($paginator as $photo) {
        $subform = $form->getSubForm($photo->getGuid());
        $values = $subform->getValues();

        $values = $values[$photo->getGuid()];
        unset($values['photo_id']);
        if (isset($values['delete']) && $values['delete'] == '1') {
          $photo->delete();
        } else if (!empty($values['move'])) {
          $nextPhoto = $photo->getNextPhoto();

          $old_album_id = $photo->album_id;
          $photo->album_id = $values['move'];
          $photo->save();

          // Change album cover if necessary
          if (($nextPhoto instanceof Album_Model_Photo) &&
            (int)$album->photo_id == (int)$photo->getIdentity()
          ) {
            $album->photo_id = $nextPhoto->getIdentity();
            $album->save();
          }

          // Remove activity attachments for this photo
          Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($photo);
        } else {
          $photo->setFromArray($values);
          $photo->save();
        }
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($this->view->url(array('action' => 'view', 'album_id' => $album->album_id), 'album_specific', true));
  }

  public function albumOrderAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('album')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid()) return;

    $album = Engine_Api::_()->core()->getSubject();

    $order = $this->_getParam('order');
    if (!$order) {
      $this->view->status = false;
      return;
    }

    // Get a list of all photos in this album, by order
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $currentOrder = $photoTable->select()
      ->from($photoTable, 'photo_id')
      ->where('album_id = ?', $album->getIdentity())
      ->order('order ASC')
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);

    // Find the starting point?
    $start = null;
    $end = null;
    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if (in_array($currentOrder[$i], $order)) {
        $start = $i;
        $end = $i + count($order);
        break;
      }
    }

    if (null === $start || null === $end) {
      $this->view->status = false;
      return;
    }

    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if ($i >= $start && $i <= $end) {
        $photo_id = $order[$i - $start];
      } else {
        $photo_id = $currentOrder[$i];
      }
      $photoTable->update(array(
        'order' => $i,
      ), array(
        'photo_id = ?' => $photo_id,
      ));
    }

    $this->view->status = true;
  }

  public function albumComposeUploadAction()
  {
    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $this->_redirect('login');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    if (empty($_FILES['Filedata'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Get album
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('albums', 'album');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $type = $this->_getParam('type', 'wall');

      if (empty($type)) $type = 'wall';

      $album = $table->getSpecialAlbum($viewer, $type);

      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
        'owner_type' => 'user',
        'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
      ));
      $photo->save();
      $photo->setPhoto($_FILES['Filedata']);

      if ($type == 'message') {
        $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
      }

      $photo->order = $photo->photo_id;
      $photo->album_id = $album->album_id;
      $photo->save();

      if (!$album->photo_id) {
        $album->photo_id = $photo->getIdentity();
        $album->save();
      }

      if ($type != 'message') {
        // Authorizations
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
      }

      $db->commit();

      $this->view->status = true;
      $this->view->photo_id = $photo->photo_id;
      $this->view->album_id = $album->album_id;
      $this->view->src = $photo->getPhotoUrl();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Photo saved successfully');
    }

    catch (Exception $e)
    {
      $db->rollBack();
      //throw $e;
      $this->view->status = false;
    }
  }


// } AlbumController

// PhotoController {
  public function photoInit()
  {
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) return;

    if (0 !== ($photo_id = (int)$this->_getParam('photo_id')) &&
      null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))
    ) {
      Engine_Api::_()->core()->setSubject($photo);
    }
  }

  public function photoViewAction()
  {
    if (!$this->_helper->requireSubject('album_photo')->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->core()->getSubject();
    $album = $photo->getAlbum();

    if (!$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer)) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }

    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) $message_view = true;
    }

    //if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() ) return;
    if (!$message_view && !$this->_helper->requireAuth()->setAuthParams($photo, null, 'view')->isValid()) return;

    $checkAlbum = Engine_Api::_()->getItem('album', $this->_getParam('album_id'));
    if (!($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum->getIdentity() || $checkAlbum->album_id != $photo->album_id) {
      $this->_forward('requiresubject', 'error', 'core');
      return;
    }

    $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
    $canDelete = $album->authorization()->isAllowed($viewer, 'delete');
    $canTag = $album->authorization()->isAllowed($viewer, 'tag');
    $canUntag = $album->isOwner($viewer);

    // Get tags
    $tags = array();
    foreach ($photo->tags()->getTagMaps() as $tagmap) {
      $tags[] = array_merge($tagmap->toArray(), array(
        'id' => $tagmap->getIdentity(),
        'text' => $tagmap->getTitle(),
        'href' => $tagmap->getHref(),
        'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id
      ));
    }
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $paginator = $photoTable->getPhotoPaginator(array(
      'album' => $album,
    ));
    $this->setFormat('view')
      ->add($this->component()->gallery($paginator, $photo, array('canComment' => true)))
      ->renderContent();
  }

// } PhotoController

  /**
   * @return Zend_Paginator
   */
  protected function getSelectBrowse()
  {
    // Get params
    switch ($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }


    // Prepare data
    $table = Engine_Api::_()->getItemTable('album');
    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $select = $table->select()
      ->where("search = 1")
      ->order($order . ' DESC');

    $user_id = $this->_getParam('user');
    if ($user_id) $select->where("owner_id = ?", $user_id);
    if ($this->_getParam('category_id')) $select->where("category_id = ?", $this->_getParam('category_id'));

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }
    return $select;
  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function browseItemData(Core_Model_Item_Abstract $item)
  {
   $owner = $item->owner_id ? $item->getOwner() : false;

    $customize_fields = array(
      'creation_date' => null,
      'descriptions' => array(
        $owner ? ($this->view->translate('By') . ' ' . $owner->getTitle()) : '',
        $this->view->translate(array('%s photo', '%s photos', $item->count()), $this->view->locale()->toNumber($item->count())) . ' &#183; ' . $this->view->timestamp(strtotime($item->creation_date)),
      ),
    );
    return $customize_fields;
  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function manageItemData(Core_Model_Item_Abstract $item)
  {
    $customize_fields = array(
      'creation_date' => null,
      'descriptions' => array(
        $this->view->translate(array('%s photo', '%s photos', $item->count()), $this->view->locale()->toNumber($item->count())) . ' &#183; ' . $this->view->timestamp(strtotime($item->creation_date)),
      ),
      'owner_id' => null,
      'owner' => null,
      'manage-icon' => 'cog',
      'manage' => $this->getOptions($item)
    );
    return $customize_fields;
  }

  private function prepareManageOptions()
  {
    return array(

    );
  }
}
