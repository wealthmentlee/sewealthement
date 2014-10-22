<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: ArticleController.php 03.02.12 12:21 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_ArticleController extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if( !$this->_helper->requireAuth()->setAuthParams('article', null, 'view')->isValid() ) return;

    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($article_id = (int) $this->_getParam('article_id')) &&
        null !== ($article = Engine_Api::_()->getItem('article', $article_id)) )
      {
        Engine_Api::_()->core()->setSubject($article);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
      'create',
      'delete',
      'edit',
      'manage',
      'success',
      'publish'
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'delete' => 'article',
      'edit' => 'article',
      'success' => 'article',
      'publish' => 'article',
      'view' => 'article',
    ));
  }

  public function indexIndexAction()
  {
    $paginator = $this->indexLoadArticlePaginator();
    $params = $this->indexGetQueryParams();
    $viewer = Engine_Api::_()->user()->getViewer();

    $can_create = Engine_Api::_()->authorization()->isAllowed('article', $viewer, 'create');

    $showphoto = $this->_getParam('showphoto', 1);
    $showdetails = $this->_getParam('showdetails', 1);
    $showmeta = $this->_getParam('showmeta', 1);
    $showdescription = $this->_getParam('showdescription', 1);

    if (isset($params['category']))
    {
      $category = Engine_Api::_()->article()->getCategory($params['category']);
      if ($category instanceof Article_Model_Category)
      {
        $title = $this->view->translate('%s Articles', $this->view->translate($category->getTitle()));
        $this->getElement()->setTitle($title);
      }
    }

    if (!empty($params['tag']))
    {
      $tagObject = Engine_Api::_()->getItem('core_tag', $params['tag']);
    }

    if (!empty($params['user']))
    {
      $userObject = Engine_Api::_()->user()->getUser($params['user']);
    }

    $form = $this->getSearchForm();
    $form->getElement('search')->setValue($this->_getParam('search'));

    $this->add($this->component()->navigation('article_main', true))
      ->add($this->component()->itemSearch($form))
      ->add($this->component()->itemList($paginator, null, array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ;
    if($can_create)
      $this->add($this->component()->quickLinks('article_quick', true));
    $this->renderContent();
  }

  public function indexManageAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $values = array();
    $values['user'] = $viewer;
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('article.page', 10);
    if( $this->_getParam('search', false) ) {
      $values['keyword'] = $this->_getParam('search');
    }

    // Get paginator
    $paginator = Engine_Api::_()->article()->getArticlesPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $can_create = Engine_Api::_()->authorization()->isAllowed('article', $viewer, 'create');

    $form = $this->getSearchForm();
    $form->getElement('search')->setValue($this->_getParam('search'));

    $this->add($this->component()->navigation('article_main', true))
      ->add($this->component()->itemSearch($form))
      ->add($this->component()->itemList($paginator, 'indexManageList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ;
    if($can_create)
      $this->add($this->component()->quickLinks('article_quick', true));
    $this->renderContent();
  }

  public function indexCreateAction()
  {
    //if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('article', null, 'create')->isValid())
      return $this->redirect($this->view->url(array(), 'article_manage'));
    $viewer = Engine_Api::_()->user()->getViewer();
    $form = new Article_Form_Create();
    // set up data needed to check quota
    $values['user_id'] = $viewer->getIdentity();
    $paginator = $this->_helper->api()->getApi('core', 'article')->getArticlesPaginator($values);


    $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'max');
    $current_count = $paginator->getTotalItemCount();

    if( !$this->getRequest()->isPost() ) {
      $this->add($this->component()->navigation('article_main', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->add($this->component()->navigation('article_main', true))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $table = Engine_Api::_()->getItemTable('article');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $featured = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'featured') ? 1 : 0;
      $sponsored = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'sponsored') ? 1 : 0;

      // Create article
      $values = array_merge($form->getValues(), array(
        'owner_type' => $viewer->getType(),
        'owner_id' => $viewer->getIdentity(),
        'featured' => $featured,
        'sponsored' => $sponsored,
      ));

      $article = $table->createRow();
      $article->setFromArray($values);
      $article->save();

      $photo = $this->getPicupFiles('photo');
      // Set photo

      if (!empty($values['photo'])) {
        $article->setPhoto($form->photo);
      } else if (!empty($photo)) {
        $photo = $photo[0];
        $article->setPhoto($photo);
      }


      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $tags = array_filter(array_map("trim", $tags));
      $article->tags()->addTagMaps($viewer, $tags);


      $customfieldform = $form->getSubForm('customField');
      $customfieldform->setItem($article);
      $customfieldform->saveValues();

      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      $auth_keys = array(
        'view' => 'everyone',
        'comment' => 'registered',
      );

      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
        $authMax = array_search($auth_value, $roles);

        foreach( $roles as $i => $role )
        {
          $auth->setAllowed($article, $role, $auth_key, ($i <= $authMax));
        }
      }


      // Add activity only if article is published
      if ($article->isPublished()) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $article, 'article_new', null, array('is_mobile' => true));
        if($action!=null){
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $article);
        }
      }
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    // Commit
    $db->commit();

    return $this->redirect($this->view->url(array('article_id' => $article->article_id), 'article_success', true));
  }

  public function indexSuccessAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $article = Engine_Api::_()->core()->getSubject('article');

    if( $viewer->getIdentity() != $article->owner_id ) {
      return $this->redirect($this->view->url(array(), 'article_manage', true));
    }

    $href = $this->view->translate('or') . '<br>'. $this->view->htmlLink($article->getHref(), $this->view->translate('continue to view this article'), array('data-role' => 'button'));
    $form = $this->getSuccessForm();

    if( !$this->getRequest()->isPost() ) {
      $this->add($this->component()->form($form))
        ->add($this->component()->html($href))
        ->add($this->component()->navigation('article_main', true))
        ->add($this->component()->quickLinks('article_quick', true))
        ->renderContent();
      return;
    }

    if( $this->getRequest()->getPost('confirm') != true ) {
      $this->add($this->component()->form($form))
        ->add($this->component()->html($href))
        ->add($this->component()->navigation('article_main', true))
        ->add($this->component()->quickLinks('article_quick'))
        ->renderContent();
      return;
    }

    return $this->redirect($this->view->url(array('controller' => 'photo', 'action' => 'upload', 'subject' => $article->getGuid()), 'article_extended', true));
  }

  public function indexEditAction()
  {
    //if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $article = Engine_Api::_()->core()->getSubject('article');

    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'edit')->isValid())
    {
      return $this->redirect($this->view->url(array(), 'article_manage', true));
    }

    $form = new Article_Form_Edit(array(
      'item' => $article
    ));

    // only for create
    $form->removeElement('photo');

    $form->populate($article->toArray());

    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    $auth_keys = array(
      'view' => 'everyone',
      'comment' => 'registered',
    );

    // Save article entry
    if( !$this->getRequest()->isPost() ) {

      // prepare tags
      $articleTags = $article->tags()->getTagMaps();
      $tagString = '';
      foreach( $articleTags as $tagmap ) {
        if( $tagString !== '' ) $tagString .= ', ';
        $tagString .= $tagmap->getTag()->getTitle();
      }

      $form->tags->setValue($tagString);

      foreach ($auth_keys as $auth_key => $auth_default) {
        $auth_field = 'auth_'.$auth_key;

        foreach( $roles as $i => $role ) {
          if (isset($form->$auth_field->options[$role]) && 1 === $auth->isAllowed($article, $role, $auth_key)) {
            $form->$auth_field->setValue($role);
          }
        }
      }

      if ($article->isPublished()) {
        $form->removeElement('published');
      }

      $this->add($this->component()->navigation('article_main', true))
        ->add($this->component()->form($form))
        ->add($this->component()->quickLinks('article_quick', true))
        ->renderContent();
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->add($this->component()->navigation('article_main', true))
        ->add($this->component()->form($form))
        ->add($this->component()->quickLinks('article_quick', true))
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
      $article->setFromArray($values);
      $article->modified_date = date('Y-m-d H:i:s');

      $article->tags()->setTagMaps($viewer, $tags);
      $article->save();

      // Save custom fields
      $customfieldform = $form->getSubForm('customField');
      $customfieldform->setItem($article);
      $customfieldform->saveValues();

      // CREATE AUTH STUFF HERE
      $values = $form->getValues();

      // CREATE AUTH STUFF HERE
      foreach ($auth_keys as $auth_key => $auth_default) {
        $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
        $authMax = array_search($auth_value, $roles);

        foreach( $roles as $i => $role ) {
          $auth->setAllowed($article, $role, $auth_key, ($i <= $authMax));
        }
      }

      // Add activity only if article is published
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($article);
      if (count($action->toArray())<=0 && $article->isPublished()) {

        if( $viewer->getIdentity() != $article->owner_id) {
          $owner = Engine_Api::_()->user()->getUser($article->owner_id);
        }
        else {
          $owner = $viewer;
        }

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $article, 'article_new', null, array('is_mobile' => true));
        if($action!=null){
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $article);
        }
      }

      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($article) as $action ) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();


      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
      $form->addNotice($savedChangesNotice);
      $customfieldform->removeElement('submit');

    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->add($this->component()->form($form))
      ->add($this->component()->navigation('article_main', true))
      ->add($this->component()->quickLinks('article_quick', true))
      ->renderContent();
  }

  public function indexDeleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $article = Engine_Api::_()->core()->getSubject('article');

    //if( $viewer->getIdentity() != $article->owner_id && !$this->_helper->requireAuth()->setAuthParams($article, null, 'edit')->isValid())
    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'delete')->isValid())
      return $this->redirect($this->view->url(array(), 'article_manage'));

    $form = $this->getDeleteForm($article);

    if(!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->add($this->component()->navigation('article_main', true))
        ->add($this->component()->quickLinks('article_quick', true))
        ->renderContent();
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->add($this->component()->form($form))
        ->add($this->component()->navigation('article_main', true))
        ->add($this->component()->quickLinks('article_quick', true))
        ->renderContent();
      return;
    }

    $article->delete();
    return $this->redirect($this->view->url(array(), 'article_manage', true));

  }

  public function indexPublishAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $article = Engine_Api::_()->core()->getSubject('article');

    // only owner can publish
    if( $viewer->getIdentity() != $article->owner_id) {
      return $this->redirect($this->view->url(array(), 'article_manage', true));
    }

    $approval = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'approval');
    if ($approval) {
      return $this->redirect($this->view->url(array(), 'article_manage', true));
    }

    if ($article->isPublished()) {
      return $this->redirect($this->view->url(array(), 'article_manage', true));
    }


    $table = $article->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $article->published = 1;
      $article->save();

      // Add activity only if article is published
      if ($article->isPublished()) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $article, 'article_new', null, array('is_mobile' => true));
        if($action!=null){
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $article);
        }
      }
    } catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $db->commit();

    return $this->redirect($article->getHref());
  }

  public function indexViewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $article = Engine_Api::_()->core()->getSubject('article');

    //return $this->_forward('requireauth', 'error', 'core');
    // require log in --- and -- not logged in => show log in screen
    if ( !Engine_Api::_()->getApi('settings', 'core')->getSetting('article.public', 1) && !$this->_helper->requireUser()->isValid() ) {
      return $this->redirect($this->view->url(array(), 'article_home'));
    }

    // logged in && no view permission => show no permission
    if ( $this->_helper->requireUser()->checkRequire() && !$this->_helper->requireAuth()->setAuthParams($article, null, 'view')->isValid()) {
      return $this->redirect($this->view->url(array(), 'article_home'));
    }
    else if (!$this->_helper->requireUser()->checkRequire()) {
      if (!$this->_helper->requireAuth()->setAuthParams($article, null, 'view')->checkRequire()) {
        return $this->redirect($this->view->url(array(), 'article_home'));
      }
    }

    $owner = Engine_Api::_()->user()->getUser($article->owner_id);

    $canEdit = $this->_helper->requireAuth()->setAuthParams($article, null, 'edit')->checkRequire();
    $canUpload = $this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->checkRequire();
    $canDelete = $this->_helper->requireAuth()->setAuthParams($article, null, 'delete')->checkRequire();
    $canPublish = $article->isOwner($viewer) && !$article->isPublished();

    $approval = 0;
    if ($viewer->getIdentity()) {
      $approval = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'approval');
    }
    $archiveList = Engine_Api::_()->article()->getArchiveList(array('user_id'=>$article->owner_id,'published'=>1));

    $article->view_count++;
    $article->save();

    // get tags
    $articleTags = $article->tags()->getTagMaps();
    $userTags = $article->tags()->getTagsByTagger($article->getOwner());

    // get archive list
    $archive_list = $this->indexHandleArchiveList($archiveList);

    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($article);

    // album material
    $album = $article->getSingletonAlbum();
    $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('article.gallery', 4));

    if($article->category_id !=0)
      $category = Engine_Api::_()->article()->getCategory($article->category_id);
    $userCategories = Engine_Api::_()->article()->getUserCategories($this->view->article->owner_id);

    // related articles
    $relatedArticles = Engine_Api::_()->article()->getRelatedArticles($article);

    $this->setFormat('profile');
  }


  public function photoInit()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
        null !== ($photo = Engine_Api::_()->getItem('article_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($article_id = (int) $this->_getParam('article_id')) &&
        null !== ($article = Engine_Api::_()->getItem('article', $article_id)) )
      {
        Engine_Api::_()->core()->setSubject($article);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
      'upload',
      'upload-photo',
      'edit',
      'delete',
      'manage'
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'list' => 'article',
      'upload' => 'article',
      'view' => 'article_photo',
      'edit' => 'article_photo',
      'delete' => 'article_photo',
      'manage' => 'article',
    ));
    $this->addPageInfo('contentTheme', 'd');
  }

  public function photoListAction()
  {
    $article = Engine_Api::_()->core()->getSubject();
    $album = $article->getSingletonAlbum();

    $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $viewer = Engine_Api::_()->user()->getViewer();
    $owner = Engine_Api::_()->getItem('user', $article->owner_id);

    $canUpload = $this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->checkRequire();

    $album->view_count++;
    $album->save();

    $this->add($this->component()->navigation('article_main', true))
      ->add($this->component()->subjectPhoto($article))
      ->add($this->component()->gallery($paginator))
      ->add($this->component()->navigation('article_photos', true))
      ->add($this->component()->quickLinks('article_main', true))
      ->renderContent();
  }

  public function photoManageAction()
  {
    $article = Engine_Api::_()->core()->getSubject();

    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->isValid() )
      return $this->redirect($article->getHref());

    $viewer = Engine_Api::_()->user()->getViewer();


    // Prepare data
    $album = $article->getSingletonAlbum();
    $paginator = $album->getCollectiblesPaginator();

    $paginator->setCurrentPageNumber($this->_getParam('page',1));
    $paginator->setItemCountPerPage($paginator->getTotalItemCount());

    $form = $this->getPhotoManageForm($paginator);

    if( !$this->getRequest()->isPost() ) {
      $this->add($this->component()->navigation('article_main', true))
        ->add($this->component()->subjectPhoto($article))
        ->add($this->component()->html($form))
        ->add($this->component()->navigation('article_photos', true))
        ->add($this->component()->quickLinks('article_main', true))
        ->renderContent();
      return;
    }

    $table = $article->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $values = $this->getRequest()->getPost();
      if( !empty($values['cover']) ) {
        $article->photo_id = $values['cover'];
        $article->save();
      }


      // Process
      foreach( $paginator as $photo ) {
        $value = $values['article_photo_' . $photo->getIdentity()];
        if( isset($value['delete']) && $value['delete'] == '1' ) {
          $photo->delete();
        } else {
          $photo->title = $value['title'];
          $photo->description = $value['description'];
          $photo->save();
        }
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($this->view->url(array('controller'=>'photo', 'action' => 'list', 'subject' => $article->getGuid()), 'article_extended', true));
  }

  public function photoUploadAction()
  {
    $article = Engine_Api::_()->core()->getSubject();
    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->isValid() ) {
      return $this->redirect($article->getHref());
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $album = $article->getSingletonAlbum();

    $article_id = $article->article_id;
    $form = new Article_Form_Photo_Upload();
    $form->removeElement('file');
    $form->addElement('File', 'file', array(
      'label' => 'Add Photos',
      'order' => 0,
      'isArray' => true
    ));
    $form->file->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    if( !$this->getRequest()->isPost() ) {
      $this->add($this->component()->navigation('article_main', true))
        ->add($this->component()->subjectPhoto($article))
        ->add($this->component()->form($form))
        ->add($this->component()->navigation('article_photos', true))
        ->add($this->component()->quickLinks('article_main', true))
        ->renderContent();
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->add($this->component()->navigation('article_main', true))
        ->add($this->component()->subjectPhoto($article))
        ->add($this->component()->form($form))
        ->add($this->component()->navigation('article_photos', true))
        ->add($this->component()->quickLinks('article_main', true))
        ->renderContent();
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('article_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $article, 'article_photo_upload', null, array('is_mobile' => true,
        'count' => count($values['file'])
      ));

      $params = array(
        // We can set them now since only one album is allowed
        'collection_id' => $album->getIdentity(),
        'album_id' => $album->getIdentity(),

        'article_id' => $article->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      $picupFiles = $this->getPicupFiles('file');
      if (empty($picupFiles))
        $photos = $form->file->getFileName();
      else
        $photos = $picupFiles;

      $count = 0;
      if( is_array($photos) ) {
        foreach( $photos as $file ) {
          $photo = $table->createRow();
          $photo->setFromArray($params);
          $photo->save();
          $photo->setPhoto($file);

          if ($article->photo_id == 0) {
            $article->photo_id = $photo->file_id;
            $article->save();
          }

          if( $action instanceof Activity_Model_Action && $count < 8 ) {
            $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
          }

          $count++;
        }
      } else {
        $photo = $table->createRow();
        $photo->setFromArray($params);
        $photo->save();
        $photo->setPhoto($photos);

        if ($article->photo_id == 0) {
          $article->photo_id = $photo->file_id;
          $article->save();
        }

        if( $action instanceof Activity_Model_Action && $count < 8 ) {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }


    return $this->redirect($article->getHref());
  }

  public function profileInit()
  {
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if( !Engine_Api::_()->core()->hasSubject() ) {
      $id = $this->_getParam('article_id');
      if( null !== $id ) {
        $subject = Engine_Api::_()->getItem('article', $id);
        if( $subject && $subject->getIdentity() ) {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('article');

    if (Engine_Api::_()->core()->hasSubject()) {
      $this->_helper->requireAuth()->setNoForward()->setAuthParams(
        $subject,
        Engine_Api::_()->user()->getViewer(),
        'view'
      );
    }
  }

  public function profileIndexAction()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->isPublished()) {
      // hack to work around SE v4.1.8 User::isAdmin bug "Registry is already initialized"
      try {
        $is_admin = $viewer->isAdmin();
      }
      catch (Exception $ex)
      {
        $is_admin = Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin', null, 'view');
      }


      if (!$is_admin && !$subject->getOwner()->isSelf($viewer)) {
        return $this->redirect($this->view->url(array(), 'article_home', true));
      }
    }

    // Increment view count
    if( !$subject->getOwner()->isSelf($viewer) ) {
      $subject->view_count++;
      $subject->save();
    }

    $info = '';
    $info .= $this->view->translate('Posted by %s', $subject->getOwner()->toString());
    $info .= ' | ' . $this->view->locale()->toDateTime($subject->creation_date);
    $info .= ' | ' . $this->view->translate(array('%s comment', '%s comments', $subject->comment_count), $this->view->locale()->toNumber($subject->comment_count));
    $info .= ' | ' . $this->view->translate(array('%s view', '%s views', $subject->view_count), $this->view->locale()->toNumber($subject->view_count));

    $desc = "<br><div class='article_profile_description'>" . $subject->description . "</div>";
    $body = '<br><div class="article_profile_body">' . $subject->body . '</div>';

    $this->addPageInfo('contentTheme', 'd');
    $this->add($this->component()->navigation('article_main', true))
      ->add($this->component()->subjectPhoto())
      ->add($this->component()->rate(array('subject' => $this->view->subject())))
      ->add($this->component()->like(array('subject' => $this->view->subject())))
      ->add($this->component()->html($info))
      ->add($this->component()->html($desc))
      ->add($this->component()->html($body))
      ->add($this->component()->tabs());

    $this->renderContent();
  }

  public function tabPhotos( $active = false )
  {
    $subject = Engine_Api::_()->core()->getSubject();

    // album material
    $album = $subject->getSingletonAlbum();
    $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber(1);
    $paginator->setItemCountPerPage($this->_getParam('max', 8));

    if( $active ) {
      $this
        ->add($this->component()->gallery($paginator), 20)
        ->add($this->component()->navigation('article_photos', true), 21)
        ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabComments($active = false)
  {
    $subject = Engine_Api::_()->core()->getSubject();

    if( $active ) {
      $this->add($this->component()->comments(array('subject' => $subject)), 10);
    }

    return true;
  }

  public function tabDetails($active = false)
  {
    $subject = Engine_Api::_()->core()->getSubject();

    if( !($subject instanceof Article_Model_Article) ) {
      return false;
    }

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);

    $fieldValues = $this->view->fieldValueLoop($subject, $fieldStructure);

    if (!$fieldValues) {
      return false;
    }

    if( $active ) {
      $this->add($this->component()->html($fieldValues), 10);
    }

    return true;
  }

  public function indexManageList(Core_Model_Item_Abstract $item)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'photo');
    $approval = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'approval');

    $desc = $this->view->translate('Published');

    $options = array();

    $options[] = array(
      'label' => $this->view->translate('Edit Article'),
      'attrs' => array(
        'href' => $this->view->url(array('action'=>'edit', 'article_id' => $item->article_id), 'article_specific', true),
        'class' => 'buttonlink'
      )
    );

    if( $allowed_upload ) {
      $options[] = array(
        'label' => $this->view->translate('Add Photos'),
        'attrs' => array(
          'href' => $this->view->url(array('controller' => 'photo', 'action' => 'upload', 'subject' => $item->getGuid()), 'article_extended', true),
          'class' => 'buttonlink'
        )
      );

    }
    $options[] = array(
      'label' => $this->view->translate('Delete Article'),
      'attrs' => array(
        'href' => $this->view->url(array('action'=>'delete', 'article_id' => $item->article_id), 'article_specific', true),
        'class' => 'buttonlink'
      )
    );

    if( !$item->published ) {
      if( $approval ) {
        $options[] = array(
          'label' => $this->view->translate('Status: Draft'),
          'attrs' => array(
            'href' => "alert(". $this->view->translate('Administrator will manually review and publish this article.') ."); return false;",
            'class' => 'buttonlink',
          )
        );
      } else {
        $options[] = array(
          'label' => $this->view->translate('Publish Article'),
          'attrs' => array(
            'href' => $this->view->url(array('article_id' => $item->article_id), 'article_publish', true),
            'class' => 'buttonlink',
          )
        );
      }

      $desc = $this->view->translate('This article has not been published yet.');
    }

    $customize_fields = array(
      'manage' => $options,
      'descriptions' => array($desc)
    );

    return $customize_fields;
  }

  protected function indexLoadArticlePaginator()
  {
    $queryParams = $this->indexGetQueryParams();
    $forcedParams = $this->indexGetForcedParams();

    $params = array_merge($queryParams, $forcedParams);

    $paginataor = Engine_Api::_()->article()->getArticlesPaginator($params);

    return $paginataor;

  }

  protected function indexGetForcedParams()
  {
    $force_params = array(
      'published' => 1,
      'search' => 1,
      'limit' => $this->_getParam('max', 10),
      'preorder' => (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('article.sorting', 0),
      'keyword' => $this->_getParam('search')
    );

    return $force_params;
  }

  protected function indexGetQueryParams()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    foreach (array('action','module','controller','rewrite') as $key) {
      unset($params[$key]);
    }

    $params = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($params);

    return $params;
  }

  public function indexHandleArchiveList($results)
  {
    $archive_list = array();

    foreach ($results as $result)
    {
      $article_date = strtotime($result['period']);

      $date_info = Radcodes_Lib_Helper_Date::archive($article_date);
      $date_start = $date_info['date_start'];

      if( !isset($archive_list[$date_start]) )
      {
        $archive_list[$date_start] = $date_info;
        $archive_list[$date_start]['count'] = $result['total'];
      }
      else
      {
        $archive_list[$date_start]['count'] += $result['total'];
      }
    }

    return $archive_list;
  }

  protected function getSuccessForm()
  {
    $form = new Engine_Form();
    $form->setTitle('Article Posted');
    $form->setDescription('Your article was successfully saved. Would you like to add some photos to it?');
    $form->addElement('Hidden', 'confirm', array(
      'value' => true
    ));

    $form->addElement('Button', 'submit', array(
      'label' => 'Add Photos',
      'type' => 'submit'
    ));

    return $form;
  }

  protected function getDeleteForm($article)
  {
    $desc = $this->view->translate('Are you sure that you want to delete the article with the title "<a href="%3$s">%1$s</a>" last modified %2$s? It will not be recoverable after being deleted.', $article->title,$this->view->timestamp($article->modified_date),$article->getHref());
    $form = new Engine_Form();
    $form->setTitle('Delete Article?');
    $form->setDescription($desc);

    // Buttons
    $form->addElement('Button', 'submit', array(
      'label' => 'Delete Article',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $form->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => $this->view->url(array('action' => 'manage'), 'article_general', true),
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $form->getDisplayGroup('buttons');

    $form->loadDefaultDecorators();
    $form->getDecorator('Description')->setOption('escape', false);
    return $form;
  }

  protected function  getPhotoManageForm(Zend_Paginator $paginator)
  {
    $article = Engine_Api::_()->core()->getSubject();

    $html = "<form method='post'><h3>" . $this->view->translate('Manage Photos') . "</h3>";
    $html .= "<ul>";
    foreach($paginator as $photo) {
      $html .= "<li>" . $this->view->itemPhoto($photo, 'thumb.normal');

      $html .= "<label for='article_photo_" . $photo->getIdentity(). "-title'>" . $this->view->translate('Title') . "</label>";
      $html .= "<input type='text' name='article_photo_" . $photo->getIdentity(). "[title]' id='article_photo_" . $photo->getIdentity() . "-title' value='" . $photo->title . "'>";

      $html .= "<label for='article_photo_" . $photo->getIdentity(). "-description'>" . $this->view->translate('Image Description') . "</label>";
      $html .= "<textarea name='article_photo_" . $photo->getIdentity(). "[description]' id='article_photo_" . $photo->getIdentity() . "-description' cols='120' rows='2'>" . $photo->description . "</textarea>";

      $html .= "<input type='checkbox' value='1' name='article_photo_" . $photo->getIdentity(). "[delete]' id='article_photo_" . $photo->getIdentity() . "-delete'>";
      $html .= "<label for='article_photo_" . $photo->getIdentity(). "-delete'>" . $this->view->translate('Delete Photo') . "</label>";

      $html .= "<input type='radio' name='cover' value=" . $photo->file_id ." id='cover-" . $photo->getIdentity() .  "'";
      if($article->photo_id == $photo->file_id) {
        $html .= " checked='checked'";
      }
      $html .= ">";
      $html .= "<label for='cover-" . $photo->getIdentity() . "'>" . $this->view->translate('Main Photo') . "</label>";

      $html .= "</li><br>";
    }
    $html .= "</ul>";

    $html .= "<button type='submit'>" . $this->view->translate('Save Changes') .  "</button>";
    $html .= "</form>";

    return $html;
  }
}
