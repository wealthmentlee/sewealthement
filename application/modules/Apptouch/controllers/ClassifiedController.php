<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 06.06.12
 * Time: 15:22
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_ClassifiedController
  extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');
  }
  public function indexIndexAction()
  {
    // Check auth
    if (!$this->_helper->requireAuth()->setAuthParams('classified', null, 'view')->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    $can_create = $this->_helper->requireAuth()->setAuthParams('classified', null, 'create')->checkRequire();


    // Prepare form
    $form = $this->getSearchForm();

    // Process form
    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
    } else {
      $values = array();
    }

    // Do the show thingy
    if (@$values['show'] == 2) {
      // Get an array of friend ids to pass to getClassifiedsPaginator
      $table = Engine_Api::_()->getItemTable('user');
      $select = $viewer->membership()->getMembersSelect('user_id');
      $friends = $table->fetchAll($select);
      // Get stuff
      $ids = array();
      foreach ($friends as $friend)
      {
        $ids[] = $friend->user_id;
      }
      $values['users'] = $ids;
    }

    // check to see if request is for specific user's listings
    if (($user_id = $this->_getParam('user_id'))) {
      $values['user_id'] = $user_id;
    }

    $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator($values);
    $items_count = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.page', 10);
    $paginator->setItemCountPerPage($items_count);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->setFormat('browse')
      ->add($this->component()->itemSearch($form));

    if ($paginator->getTotalItemCount()) {
      $this->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($this->_getParam('search')) {
      if($can_create)
        $this->add($this->component()->tip(
          $this->view->translate('Be the first to %1$spost%2$s one!', '<a href="' . $this->view->url(array('action' => 'create'), 'classified_general', true) . '">', '</a>'),
          $this->view->translate('Nobody has posted a classified listing with that criteria.')
        ));
      else
        $this->add($this->component()->tip(
          $this->view->translate('Nobody has posted a classified listing with that criteria.')
        ));
    } else {
      if($can_create)
        $this->add($this->component()->tip(
          $this->view->translate('Be the first to %1$spost%2$s one!', '<a href="' . $this->view->url(array('action' => 'create'), 'classified_general', true) . '">', '</a>'),
          $this->view->translate('Nobody has posted a classified listing yet.')
        ));
      else
        $this->add($this->component()->tip(
          $this->view->translate('Nobody has posted a classified listing yet.')
        ));
    }
    $this->renderContent();

  }

  // USER SPECIFIC METHODS
  public function indexManageAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $can_create = $this->_helper->requireAuth()->setAuthParams('classified', null, 'create')->checkRequire();
    $allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'photo');

    $form = $this->getSearchForm();
    $values = $this->_getAllParams();
    $values['user_id'] = $viewer->getIdentity();


    // Get paginator
    $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator($values /*, $customFieldValues*/);
    $items_count = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.page', 10);
    $paginator->setItemCountPerPage($items_count);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));


    //    // maximum allowed classifieds
    $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'max');
    $current_count = $paginator->getTotalItemCount();

    $this->setFormat('manage')
      ->setPageTitle($this->view->translate('My Listings'))
      ->add($this->component()->itemSearch($form));
    if (($current_count >= $quota) && !empty($quota)) {
      $this->add($this->component()->tip(
        $this->view->translate('You have already created the maximum number of listings allowed. If you would like to create a new listing, please delete an old one first.')
      ));
    }
    if ($paginator->getTotalItemCount()) {
      $this->add($this->component()->itemList($paginator, "manageItemData", array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($this->search) {
      $this->add($this->component()->tip(
        $this->view->translate('You do not have any classified listing that match your search criteria.')
      ));
    } else {
      if($can_create)
        $this->add($this->component()->tip(
          $this->view->translate('Get started by <a href=\'%1$s\'>posting</a> a new listing.', $this->view->url(array('action' => 'create'), 'classified_general')),
          $this->view->translate('You do not have any classified listings.')
        ));
      else
        $this->add($this->component()->tip(
          $this->view->translate('You do not have any classified listings.')
        ));

    }
    $this->renderContent();

  }

  public function indexViewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', $this->_getParam('classified_id'));
    if ($classified) {
      Engine_Api::_()->core()->setSubject($classified);
    }

    // Check auth
    if (!$this->_helper->requireAuth()->setAuthParams($classified, null, 'view')->isValid()) {
      return;
    }

    $canEdit = $classified->authorization()->isAllowed(null, 'edit');
    $canDelete = $classified->authorization()->isAllowed(null, 'delete');
    $canUpload = $classified->authorization()->isAllowed(null, 'photo');

    // Get navigation
    $gutterNavigation = Engine_Api::_()->getApi('menus', 'apptouch')
      ->getNavigation('classified_gutter');

    if ($classified) {
      $owner = Engine_Api::_()->getItem('user', $classified->owner_id);

      if (!$owner->isSelf($viewer)) {
        $classified->view_count++;
        $classified->save();
      }

      if ($classified->photo_id) {
        $main_photo = $classified->getPhoto($classified->photo_id);
      }

      // get tags
      $classifiedTags = $classified->tags()->getTagMaps();
      $userTags = $classified->tags()->getTagsByTagger($classified->getOwner());

      // get custom field values
      //$this->view->fieldsByAlias = Engine_Api::_()->fields()->getFieldsValuesByAlias($classified);
      // Load fields view helpers
      $view = $this->view;
      $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
      $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($classified);

      // album material
      $album = $classified->getSingletonAlbum();
      $paginator = $album->getCollectiblesPaginator();
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));
      $paginator->setItemCountPerPage(100);

      if ($classified->category_id) {
        $categoryObject = Engine_Api::_()->getDbtable('categories', 'classified')
          ->find($classified->category_id)->current();
      }
      $options = array();
      $option_type = "manage";
      if ($canEdit)
        $options[] = $this->getOption($classified, 0, $option_type);
      if ($canUpload)
        $options[] = $this->getOption($classified, 1, $option_type);
      if ($canEdit)
        if (!$classified->closed) {
          $options[] = $this->getOption($classified, 2, $option_type);
        } else {
          $options[] = $this->getOption($classified, 3, $option_type);
        }
      if ($canDelete)
        $options[] = $this->getOption($classified, 4, $option_type);
      if ($this->view->viewer()->getIdentity())
        $options[] = $this->getOption($classified, 5, $option_type);

      $this->setFormat('view')
        ->add($this->component()->date(array(
        'title' => $this->view->translate('Posted by') . ' ' . $classified->getParent()->getTitle() . ' ' . $this->view->timestamp($classified->creation_date),
        'count' => $this->view->translate(array('%s view', '%s views', $classified->view_count), $this->view->locale()->toNumber($classified->view_count))
      )))
        ->add($this->component()->html($this->view->fieldValueLoop($classified, $fieldStructure)))
        ->add($this->component()->html(nl2br($classified->body)))
        ->add($this->component()->gallery($paginator))
        ->add($this->component()->quickLinks($options))
        ->renderContent();
    }
  }

  public function indexCreateAction()
  {
    // Check auth
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams('classified', null, 'create')->isValid()) return;

    $form = new Classified_Form_Create();
    //    $form->removeElement('photo');
    $this->setFormat('create');

    // set up data needed to check quota
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();
    $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator($values);

    $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'max');
    $current_count = $paginator->getTotalItemCount();

    // If not post or form not valid, return
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('classified');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create classified
      $values = array_merge($form->getValues(), array(
        'owner_type' => $viewer->getType(),
        'owner_id' => $viewer->getIdentity(),
      ));

      $classified = $table->createRow();
      $classified->setFromArray($values);
      $classified->save();

      $photo = $this->getPicupFiles('photo');
      // Set photo
      if (!empty($values['photo'])) {
        $classified->setPhoto($form->photo);
      } else if (!empty($photo)) {
        $photo = $photo[0];
        $classified->setPhoto($photo);
      }

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $tags = array_filter(array_map("trim", $tags));
      $classified->tags()->addTagMaps($viewer, $tags);

      // Add fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($classified);
      $customfieldform->saveValues();

      // Set privacy
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if (empty($values['auth_view'])) {
        $values['auth_view'] = array("everyone");
      }
      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = array("everyone");
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($classified, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($classified, $role, 'comment', ($i <= $commentMax));
      }

      // Commit
      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    try {
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $classified, 'classified_new', null, array('is_mobile' => true));
      if ($action != null) {
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $classified);
      }
      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    // Redirect
    $allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'photo');
    if ($allowed_upload) {
      return $this->redirect($this->view->url(array('action' => 'success', 'classified_id' => $classified->classified_id), 'classified_specific', true));
    } else {
      return $this->redirect($classified); // todo //, array('prependBase' => false));
    }
  }

  public function indexEditAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', $this->_getParam('classified_id'));
    if (!Engine_Api::_()->core()->hasSubject('classified')) {
      Engine_Api::_()->core()->setSubject($classified);
    }

    // Check auth
    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams($classified, $viewer, 'edit')->isValid()) {
      return;
    }


    // Prepare form
    $form = new Classified_Form_Edit(array(
      'item' => $classified
    ));
    $this->setFormat('create');

    $form->removeElement('photo');

    /*
    if( isset($classified->photo_id) &&
        $classified->photo_id != 0 &&
        !$classified->getPhoto($classified->photo_id) ) {
      $classified->addPhoto($classified->photo_id);
    }
    */

    $album = $classified->getSingletonAlbum();
    $paginator = $album->getCollectiblesPaginator();

    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(100);

    foreach ($paginator as $photo) {
      $subform = new Classified_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
      $subform->removeElement('title');

      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
    }

    // Save classified entry
    $saved = $this->_getParam('saved');
    if (!$this->getRequest()->isPost() || $saved) {

      if ($saved) {
        $url = $this->_helper->url->url(array('user_id' => $viewer->getIdentity(), 'classified_id' => $classified->getIdentity()), 'classified_entry_view');
        $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved. Click %s to view your listing.", '<a href="' . $url . '">here</a>');
        $form->addNotice($savedChangesNotice);
      }

      // prepare tags
      $classifiedTags = $classified->tags()->getTagMaps();
      //$form->getSubForm('custom')->saveValues();

      $tagString = '';
      foreach ($classifiedTags as $tagmap)
      {
        if ($tagString !== '') $tagString .= ', ';
        $tagString .= $tagmap->getTag()->getTitle();
      }

      $form->tags->setValue($tagString);

      // etc
      $form->populate($classified->toArray());
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      foreach ($roles as $role)
      {
        if ($form->auth_view && 1 === $auth->isAllowed($classified, $role, 'view')) {
          $form->auth_view->setValue($role);
        }
        if ($form->auth_comment && 1 === $auth->isAllowed($classified, $role, 'comment')) {
          $form->auth_comment->setValue($role);
        }
      }
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process

    // handle save for tags
    $values = $form->getValues();
    $tags = preg_split('/[,]+/', $values['tags']);
    $tags = array_filter(array_map("trim", $tags));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {

      $classified->setFromArray($values);
      $classified->modified_date = date('Y-m-d H:i:s');

      $classified->tags()->setTagMaps($viewer, $tags);
      $classified->save();

      $cover = $values['cover'];

      // Process
      foreach ($paginator as $photo) {
        $subform = $form->getSubForm($photo->getGuid());
        $subValues = $subform->getValues();
        $subValues = $subValues[$photo->getGuid()];
        unset($subValues['photo_id']);

        if (isset($cover) && $cover == $photo->photo_id) {
          $classified->photo_id = $photo->file_id;
          $classified->save();
        }

        if (isset($subValues['delete']) && $subValues['delete'] == '1') {
          if ($classified->photo_id == $photo->file_id) {
            $classified->photo_id = 0;
            $classified->save();
          }
          $photo->delete();
        } else {
          $photo->setFromArray($subValues);
          $photo->save();
        }
      }

      // Save custom fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($classified);
      $customfieldform->saveValues();

      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if (!empty($values['auth_view'])) {
        $auth_view = $values['auth_view'];
      } else {
        $auth_view = "everyone";
      }
      $viewMax = array_search($auth_view, $roles);

      foreach ($roles as $i => $role)
      {
        $auth->setAllowed($classified, $role, 'view', ($i <= $viewMax));
      }

      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if (!empty($values['auth_comment'])) {
        $auth_comment = $values['auth_comment'];
      } else {
        $auth_comment = "everyone";
      }
      $commentMax = array_search($auth_comment, $roles);

      foreach ($roles as $i => $role)
      {
        $auth->setAllowed($classified, $role, 'comment', ($i <= $commentMax));
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
      foreach ($actionTable->getActionsByObject($classified) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
    $this->add($this->component()->form($form))
      ->renderContent();

    return $this->redirect($this->view->url(array('action' => 'manage'), 'classified_general', true));
  }

  public function indexDeleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', $this->getRequest()->getParam('classified_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($classified, null, 'delete')->isValid()) return;

    $form = new Classified_Form_Delete();
    $this->add($this->component()->form($form));
    if (!$classified) {
      $this->view->status = false;
      return $this->renderContent();
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      return $this->renderContent();
    }

    $db = $classified->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $classified->delete();
      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $this->redirect(
      $this->view->url(array('action' => 'manage'), 'classified_general', true),
      Zend_Registry::get('Zend_Translate')->_('Your classified listing has been deleted.'),
      true
    );
  }

  public function indexCloseAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', $this->_getParam('classified_id'));
    if (!Engine_Api::_()->core()->hasSubject('classified')) {
      Engine_Api::_()->core()->setSubject($classified);
    }
    $this->view->classified = $classified;

    // Check auth
    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams($classified, $viewer, 'edit')->isValid()) {
      return;
    }

    // @todo convert this to post only

    $table = $classified->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $classified->closed = $this->_getParam('closed');
      $classified->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    if (!($returnUrl = $this->_getParam('return_url'))) {
      return $this->redirect($this->view->url(array('action' => 'manage'), 'classified_general', true));
    } else {
      return $this->redirect($returnUrl);
    }
  }

  public function indexSuccessAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->classified = $classified = Engine_Api::_()->getItem('classified', $this->_getParam('classified_id'));
    $this->setFormat('html')
      ->add($this->component()->html($this->view->render('classified/index-success.tpl')));

    if ($viewer->getIdentity() != $classified->owner_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
      return $this->redirect("classifieds/photo/upload/subject/classified_" . $this->_getParam('classified_id'));
    }
    $this->renderContent();
  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function browseItemData(Core_Model_Item_Abstract $item)
  {
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item);
    $owner = $item->getOwner();
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    $customize_fields = array(
      'descriptions' => array(
        $this->view->translate('posted by') . ' ' . $owner->getTitle()
      ),
      'counter' => null,
      'custom' => $this->view->fieldValueLoop($item, $fieldStructure),
    );
    return $customize_fields;
  }

  /**
   * @param $item Core_Model_Item_Abstract
   * @return array
   */
  public function manageItemData(Core_Model_Item_Abstract $item)
  {

    $options = array();
    $options[] = $this->getOption($item, 0);
    $options[] = $this->getOption($item, 1);
    if (!$item->closed) {
      $options[] = $this->getOption($item, 2);
    } else {
      $options[] = $this->getOption($item, 3);
    }
    $options[] = $this->getOption($item, 4);
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item);
    $owner = $item->getOwner();
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $customize_fields = array(
      'descriptions' => array(
        $this->view->translate('posted by') . ' ' . $owner->getTitle()
      ),
      'custom' => $this->view->fieldValueLoop($item, $fieldStructure),
      'counter' => null,
      'owner_id' => null,
      'owner' => null,
      'manage' => $options
    );
    return $customize_fields;
  }

//  PhotoController {
  public function photoInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($photo_id = (int)$this->_getParam('photo_id')) &&
        null !== ($photo = Engine_Api::_()->getItem('classified_photo', $photo_id))
      ) {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if (0 !== ($classified_id = (int)$this->_getParam('classified_id')) &&
        null !== ($classified = Engine_Api::_()->getItem('classified', $classified_id))
      ) {
        Engine_Api::_()->core()->setSubject($classified);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
      'photo-upload',
      'photo-edit',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'photo-list' => 'classified',
      'photo-upload' => 'classified',
      'photo-view' => 'classified_photo',
      'photo-edit' => 'classified_photo',
    ));
  }

  public function photoViewAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();
    $album = $photo->getCollection();
    $this->setFormat('view')
      ->add($this->component()->gallery($album->getCollectiblesPaginator(), $photo))
      ->renderContent();
    $canEdit = $photo->authorization()->isAllowed(null, 'photo.edit');
  }

  public function photoUploadAction()
  {
    $classified = Engine_Api::_()->core()->getSubject();
    if (isset($_GET['ul'])) return $this->_forward('upload-photo', null, null, array('format' => 'json', 'classified_id' => (int)$classified->getIdentity()));

    //if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'photo.upload')->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', (int)$classified->getIdentity());

    $album = $classified->getSingletonAlbum();

    $classified_id = $classified->classified_id;
    $form = new Classified_Form_Photo_Upload();
    $form->removeElement('file');
    $form->addElement('File', 'file', array(
      'label' => 'APPTOUCH_Upload Photos',
      'order' => '0',
      'isArray' => true
    ));
    $form->file->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    $form->file->setAttrib('data', array('classified_id' => $classified->getIdentity()));
    $this
      ->add($this->component()->html('<h2>' . $this->view->translate('Classified Listing Photos') . '</h2>'))
      ->add($this->component()->form($form));

    if (!$this->getRequest()->isPost()) {
      return $this->renderContent();
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return $this->renderContent();
    }

    // Process
    $table = Engine_Api::_()->getItemTable('classified_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $params = array(
        'classified_id' => $classified->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $classified, 'classified_photo_upload', null, array('is_mobile' => true, 'count' => count($values['file'])));

      // Do other stuff
      $count = 0;
      $photoTable = Engine_Api::_()->getDbtable('photos', 'classified');

      $photodb = $photoTable->getAdapter();
      $photodb->beginTransaction();

      $album = $classified->getSingletonAlbum();

      $picupFiles = $this->getPicupFiles('file');
      if (empty($picupFiles))
        $photos = $form->file->getFileName();
      else
        $photos = $picupFiles;
      $count = 0;
      foreach ($photos as $photoPath) {
        $params = array(
          // We can set them now since only one album is allowed
          'collection_id' => $album->getIdentity(),
          'album_id' => $album->getIdentity(),

          'classified_id' => $classified->getIdentity(),
          'user_id' => $viewer->getIdentity(),
        );


        $photo = $photoTable->createRow();
        $photo->setFromArray($params);
        $photo->save();

        $photo->setPhoto($photoPath);

        $photo_id = $photo->file_id;

        if (!$classified->photo_id) {
          $classified->photo_id = $photo_id;
          $classified->save();
        }
        if ($action instanceof Activity_Model_Action && $count < 8) {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }

      $db->commit();
      $photodb->commit();

    } catch (Exception $e) {

      $photodb->rollBack();
      $db->rollBack();
      //      throw $e;
      $this->view->status = false;

      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      return;
    }


    $this->redirect($classified);
  }

//  } PhotoController
}
