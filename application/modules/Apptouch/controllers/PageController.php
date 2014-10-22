<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 24.07.12
 * Time: 11:28
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_PageController extends Apptouch_Controller_Action_Bridge
{
  /**
   *@var Page_Model_Page
   */
  protected $_page;

  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;

  /**
   * @var Payment_Model_Package
   */
  protected $_package;

  // Vars for page edit. may be tmp
  /**
   * @var $page Page_Model_Page
   */
  protected $pageObject;

  /**
   * @var $viewer User_Model_User
   */
  protected $viewer;

// Vars for page edit. may be tmp

  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');
  }

  public function indexIndexAction()
  {
    //        if (!$this->_helper->requireAuth()->setAuthParams('pages', null, 'view')->isValid()) return;
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
    $table = Engine_Api::_()->getItemTable('page');
    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $select = $table->select()->where("search = 1")->order($order . ' DESC');

    $user_id = $this->_getParam('user');
    if ($user_id) $select->where("owner_id = ?", $user_id);
    if ($this->_getParam('category_id')) $select->where("category_id = ?", $this->_getParam('category_id'));

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    // Prepare Data  {
    $select = $this->getSelectBrowse();
    // } Prepare Data

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($settings->getSetting('page.browse_count', 10));
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
      ->add($this->component()->itemSearch($form))
      ->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
    //With PAGE URL
    //     ------------------------- } New ----------------------------------
  }

  public function indexManageAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    //        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid()) return;
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
    $table = Engine_Api::_()->getItemTable('page');

    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $select = $table->select()
      ->where('user_id = ?', $user->getIdentity())
      ->order($order . ' DESC');


    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($settings->getSetting('album_page', 25));
    $paginator->setCurrentPageNumber($page);

    $this->setFormat('manage')
      ->add($this->component()->itemSearch($form))
      ->add($this->component()->itemList($paginator, "manageItemData", array('listPaginator' => true,)))//
//      ->add($this->component()->paginator($paginator))
      ->renderContent();

  }

  public function indexViewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $page_id = $this->_getParam('page_id');

    $pagesTable = Engine_Api::_()->getItemTable('page');
    $select = $pagesTable->select('where page_id=?', $page_id)->orWhere('name=?', $page_id)->limit(1);
    $page = $pagesTable->fetchRow($select);

    if (!$page) {
      return $this->redirect($this->view->url(array('action' => 'browse', 'format' => 'json'), 'page_browse'));
    }

    Engine_Api::_()->core()->setSubject($page);

    $isAllowedView = $page->authorization()->isAllowed($viewer, 'view');


    $content = $this->_getParam('content');
    $content_id = $this->_getParam('content_id');

    if ($content && $content_id && $isAllowedView) {
      $method = 'view' . ucfirst($content);
      if (method_exists($this, $method)) {
        $this->addPageInfo('contentTheme', 'd');
        $this->$method($content_id);
      }
    } else {
        $this
        ->addPageInfo('contentTheme', 'd');

      if($isAllowedView)
        $this->add($this->component()->quickLinks('profile'));

      $this->add($this->component()->subjectPhoto(), 0)
        ->add($this->component()->like(array('subject' => $this->view->subject())), 2);

      if($isAllowedView)
        $this->add($this->component()->tabs(), 5);

      $this->renderContent();
    }
  }

  /* *** Page Controller (page-team route) *************** */

  public function pageInit()
  {
    $page_id = $this->_getParam('page_id', 0);
    $this->pageObject = $page = Engine_Api::_()->getItem('page', $page_id);

    $this->setPageTitle("Edit Page");

    if (!$page) {
      $this->redirect($this->view->url(array(), 'page_browse', true));
    }
    if (!Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject($page);
    }
    $this->viewer = Engine_Api::_()->user()->getViewer();
    if ($page == null) {
      $this->redirect($this->view->url(array(), 'page_browse', true));
    }

    if (!$this->_helper->requireUser()->isValid() || !($page->isAdmin() || $this->_getParam('action') == 'add-favorites')) {
      $this->redirect($this->view->url(array(), 'page_browse', true));
    }

    /**
     * @var $settings Core_Model_DbTable_Settings
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();
    $isOwner = $page->isOwner($viewer);

    if ($this->_getParam('action') != 'delete' && !$page->isDefaultPackageEnabled()) {
      if ($isOwner) {
        $this->redirect($this->view->url(array( 'page_id' => $page->page_id), 'page_package_choose', true));
      } else {
        $this->redirect($this->view->url(array(), 'page_browse', true));
      }
    }

    if ($settings->getSetting('page.package.enabled', 0)) {
      $this->packageEnabled = true;
      $this->isOwner = $isOwner;
      $this->package =  $package = $page->getPackage();
      $this->currency = $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
      if ( null != ($subscription = Engine_Api::_()->getItemTable('page_subscription')->getSubscription($page->getIdentity(), true))) {
        $this->subscription_expired = $subscription->expiration_date;
      }
      if( !$package ) {
        $package = Engine_Api::_()->getDbTable('packages', 'page')->getDefaultPackage();
      }
      $this->isDefaultPackage = $package->isDefault();
    }

    $this->addPageInfo('contentTheme', 'd');
  }

  public function pageEditAction()
  {
    $page = $this->pageObject;

    $this->add($this->component()->html($this->_getEditMenu('info')));

    $form = new Page_Form_Edit(array('item' => $page));
    $coordinates = $this->_getParam('coordinates', false);

    $form->populate($page->toArray());

    $tags = $page->tags()->getTagMaps();
    $tagString = '';
    foreach ($tags as $tagmap) {
      if ($tagString !== '') $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }
    $form->tags->setValue($tagString);

    /*--Package info*/
    if ( isset($this->packageEnabled) && $this->packageEnabled ) {
      $element = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'd'));
      $title = $this->dom()->new_('h3', array(), $this->view->translate("PAGE_Package"));

      if( $this->package ) {
        $text = '<table><tr><th>' . $this->view->translate('Title') . '</th><td> : ' . $this->package->getTitle() . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('Price') . '</th><td> : ' . $this->view->locale()->toCurrency($this->package->price, $this->currency) . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('Expiration') . '</th><td> : ' . (($this->subscription_expired) ? $this->view->locale()->toDateTime($this->subscription_expired) : $this->view->translate('Never')) . '</td></tr>';
        $text .= '</table>';
        $text .= $this->package->getPackageDescription();


        if( $this->isOwner ) {
          $text .= '<a data-role="button" href="' . $this->view->url(array('page_id' => $page->getIdentity()), 'page_package_choose') . '">' . $this->view->translate('PAGE_Change Package') . '</a>';
        }
      } else {
        $text = $this->view->translate('PAGE_Your page does not have a package');
        if( $this->isOwner ) {
          $text .= '<a data-role="button" href="' . $this->view->url(array('page_id' => $page->getIdentity()), 'page_package_choose') . '">' . $this->view->translate('PAGE_Upgrade') . '</a>';
        }
      }

      $content = $this->dom()->new_('p', array(), $text);
      $element->append($title);
      $element->append($content);
      $this->add($this->component()->html($element));
    }
    /*--Package info*/

    $this->addPageInfo('setInfoJSON', json_encode($form->getSetInfo()));
    $this->addPageInfo('isMultiMode', count($form->getSetInfo()));

    $this->setFormat('edit');
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form));
      $this->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form));
      $this->renderContent();
      return;
    }

    $db = Engine_Api::_()->getDbTable('pages', 'page')->getAdapter();
    $db->beginTransaction();
    try {
      $values = $form->getValues();
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($page);
      $customfieldform->saveValues();
      $customfieldform->removeElement('submit');
      $address = array($values['country'], $values['state'], $values['city'], $values['street']);

      if ($address[0] == '' && $address[1] == '' && $address[2] == '' && $address[3] == '' && !$coordinates) {
        $page->deleteMarker();
      } elseif ($page->isAddressChanged($address) && !$coordinates) {
        $page->addMarkerByAddress($address);
      }

      if ($coordinates) {
        $coordinate_arr = explode(';', $coordinates);
        $pageMarker = $page->getMarker(true);
        $pageMarker->latitude = $coordinate_arr[0];
        $pageMarker->longitude = $coordinate_arr[1];
        $pageMarker->save();
      }

      $raw_tags = preg_split('/[,]+/', $values['tags']);
      $tags = array();
      foreach ($raw_tags as $tag) {
        $tag = trim(strip_tags($tag));
        if ($tag == "") {
          continue;
        }
        $tags[] = $tag;
      }

      $page->tags()->setTagMaps($this->viewer, $tags);

      $misTypes = array('http//', 'htp://', 'http://');
      $values['website'] = str_replace($misTypes, '', trim($values['website']));

      if (function_exists('mb_convert_encoding')) {
        $values['description'] = mb_convert_encoding($values['description'], 'UTF-8');
        $values['title'] = mb_convert_encoding(strip_tags($values['title']), 'UTF-8');
      } else {
        $values['title'] = Engine_String::strip_tags($values['title']);
      }

      $page->setFromArray($values);
      $page->displayname = $page->title;
      $page->keywords = $values['tags'];
      $page->set_id = $values['category'];
      $page->modified_date = date('Y-m-d H:i:s');

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Changes were successfully saved.'));

      $page->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->add($this->component()->form($form))
      ->renderContent();
  }

  public function pagePrivacyAction()
  {
    /**
     * @var $page Page_Model_Page
     */
    $page = $this->pageObject;
    $this->add($this->component()->html($this->_getEditMenu('privacy')));

    if (!$this->_helper->requireUser()->isValid()) return;

    $form = new Page_Form_Privacy(array('page' => $page));

    $auth = Engine_Api::_()->authorization()->context;

    $roles = array('team', 'likes', 'registered', 'everyone');
    foreach ($roles as $roleString) {
      $role = $roleString;

      if ($role === 'team') {
        $role = $page->getTeamList();
      } elseif ($role === 'likes') {
        $role = $page->getLikesList();
      }

      if (1 === $auth->isAllowed($page, $role, 'view') && !empty($form->auth_view)) {
        $form->auth_view->setValue($roleString);
      }
    }

    $roles = array('team', 'likes', 'registered');
    foreach ($roles as $roleString) {
      $role = $roleString;

      if ($role === 'team') {
        $role = $page->getTeamList();
      } elseif ($role === 'likes') {
        $role = $page->getLikesList();
      }

      if (1 === $auth->isAllowed($page, $role, 'comment') && !empty($form->auth_comment)) {
        $form->auth_comment->setValue($roleString);
      }
    }

    $pageApi = Engine_Api::_()->page();
    $page_features = $page->getAllowedFeatures();

    if ($pageApi->isModuleExists('pagealbum') && in_array('pagealbum', $page_features)) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if ($role === 'team') {
          $role = $page->getTeamList();
        } elseif ($role === 'likes') {
          $role = $page->getLikesList();
        }

        if (1 === $auth->isAllowed($page, $role, 'album_posting') && !empty($form->auth_album_posting)) {
          $form->auth_album_posting->setValue($roleString);
        }
      }
    }

    if ($pageApi->isModuleExists('pageblog') && in_array('pageblog', $page_features)) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if ($role === 'team') {
          $role = $page->getTeamList();
        } elseif ($role === 'likes') {
          $role = $page->getLikesList();
        }

        if (1 === $auth->isAllowed($page, $role, 'blog_posting') && !empty($form->auth_blog_posting)) {
          $form->auth_blog_posting->setValue($roleString);
        }
      }
    }

    if ($pageApi->isModuleExists('pagediscussion') && in_array('pagediscussion', $page_features)) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if ($role === 'team') {
          $role = $page->getTeamList();
        } elseif ($role === 'likes') {
          $role = $page->getLikesList();
        }

        if (1 === $auth->isAllowed($page, $role, 'disc_posting') && !empty($form->auth_disc_posting)) {
          $form->auth_disc_posting->setValue($roleString);
        }
      }
    }

    if ($pageApi->isModuleExists('pagedocument') && in_array('pagedocument', $page_features)) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if ($role === 'team') {
          $role = $page->getTeamList();
        } elseif ($role === 'likes') {
          $role = $page->getLikesList();
        }

        if (1 === $auth->isAllowed($page, $role, 'doc_posting') && !empty($form->auth_doc_posting)) {
          $form->auth_doc_posting->setValue($roleString);
        }
      }
    }

    if ($pageApi->isModuleExists('pageevent') && in_array('pageevent', $page_features)) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if ($role === 'team') {
          $role = $page->getTeamList();
        } elseif ($role === 'likes') {
          $role = $page->getLikesList();
        }

        if (1 === $auth->isAllowed($page, $role, 'event_posting') && !empty($form->auth_event_posting)) {
          $form->auth_event_posting->setValue($roleString);
        }
      }
    }

    if ($pageApi->isModuleExists('pagemusic') && in_array('pagemusic', $page_features)) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if ($role === 'team') {
          $role = $page->getTeamList();
        } elseif ($role === 'likes') {
          $role = $page->getLikesList();
        }

        if (1 === $auth->isAllowed($page, $role, 'music_posting') && !empty($form->auth_music_posting)) {
          $form->auth_music_posting->setValue($roleString);
        }
      }
    }

    if ($pageApi->isModuleExists('pagevideo') && in_array('pagevideo', $page_features)) {
      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if ($role === 'team') {
          $role = $page->getTeamList();
        } elseif ($role === 'likes') {
          $role = $page->getLikesList();
        }

        if (1 === $auth->isAllowed($page, $role, 'video_posting') && !empty($form->auth_video_posting)) {
          $form->auth_video_posting->setValue($roleString);
        }
      }
    }

    if ($pageApi->isModuleExists('store') && in_array('store', $page_features)) {
      $roles = array('owner', 'team');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if ($role === 'team') {
          $role = $page->getTeamList();
        }

        if (1 === $auth->isAllowed($page, $role, 'store_posting') && !empty($form->auth_store_posting)) {
          $form->auth_store_posting->setValue($roleString);
        }
      }
    }

    $form->populate($this->getRequest()->getParams());

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))->renderContent();
      return;
    }

    $db = Engine_Api::_()->getDbTable('pages', 'page')->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $page->setPrivacy($values);
      $page->search = $values['search'];

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Settings were successfully saved.'));

      $page->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->add($this->component()->form($form))->renderContent();
  }

  public function pagePhotoAction()
  {
    $this->add($this->component()->html($this->_getEditMenu('photo')));

    if (!$this->_helper->requireUser()->isValid()) return 0;

    /**
     * @var $page Page_Model_Page
     */
    $page = $this->pageObject;
    $form = new Page_Form_Photo();
    $form->getElement('Filedata')->setAttrib('onchange', '');

    if (empty($page->photo_id)) {
      $form->removeElement('remove');
    }

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))->renderContent();
      return 0;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))->renderContent();
      return 0;
    }

    if ($form->Filedata->getValue() !== null) {
      $db = Engine_Api::_()->getDbTable('pages', 'page')->getAdapter();
      $db->beginTransaction();

      try {
        $fileElement = $form->Filedata;

        $page->setPhoto($fileElement);

        $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Image was successfully proccessed.'));

        $page->save();
        $db->commit();
      } catch (Engine_Image_Adapter_Exception $e) {
        $db->rollBack();
        $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    } else if ($form->getValue('coordinates') !== '') {
      $storage = Engine_Api::_()->storage();

      $iProfile = $storage->get($page->photo_id, 'thumb.profile');
      $iSquare = $storage->get($page->photo_id, 'thumb.icon');

      // Read into tmp file
      $pName = $iProfile->getStorageService()->temporary($iProfile);
      $iName = dirname($pName) . '/nis_' . basename($pName);

      list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));

      $image = Engine_Image::factory();
      $image->open($pName)
        ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
        ->write($iName)
        ->destroy();

      $iSquare->store($iName);

      // Remove temp files
      @unlink($iName);
    }

    return $this->redirect($this->view->url(array('action' => 'photo', 'page_id' => $page->getIdentity(), 'format' => 'json'), 'page_team', true));
  }

  public function pageDeleteAction()
  {
    /**
     * @var $page Page_Model_Page
     */
    $page = $this->pageObject;
    $page_id = $page->getIdentity();

//      $this->attrPage('data-dom-cache', false);

    $form = new Page_Form_Delete();

    $form->setAction($this->view->url(array('action' => 'delete', 'page_id' => $page_id), 'page_team'));
    $description = sprintf(Zend_Registry::get('Zend_Translate')
      ->_('PAGE_DELETE_DESC'), $this->view->htmlLink($page->getHref(), $page->getTitle()));

    $form->setDescription($description);

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))->renderContent();
      return;
    }

    $db = Engine_Api::_()->getDbtable('pages', 'page')->getAdapter();
    $db->beginTransaction();

    try {
      if (null != ($subs = Engine_Api::_()->getItemTable('page_subscription')->getSubscription($page_id))) {
        $subs->delete();
      }

      if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')) {

        /**
         * @var $apiTable Store_Model_DbTable_Apis
         */
        $apiTable = Engine_Api::_()->getDbTable('apis', 'store');
        $apiTable->delete(array('page_id = ?' => $page_id));
      }

      $page->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($this->view->url(array('format' => 'json'), 'page_manage'));
  }

  private function _getEditMenu($active = 'info')
  {
    $info = '';
    $privacy = '';
    $photo = '';
    switch ($active) {
      case 'info' :
        $info = 'ui-btn-active';
        break;
      case 'privacy':
        $privacy = 'ui-btn-active';
        break;
      case 'photo':
        $photo = 'ui-btn-active';
        break;
      default:
        $info = 'ui-btn-active';
        break;
    }
    $page = $this->pageObject;
    $editMenu = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal', 'style' => 'text-align: left'));
    $editMenu->append($this->dom()->new_('a',
      array(
        'class' => $info,
        'data-role' => 'button',
        'data-icon' => 'info',
        'data-rel' => '',
        'href' => $this->view->url(array('action' => 'edit', 'page_id' => $page->getIdentity()), 'page_team', true)), $this->view->translate('Basic Information')))
      ->append($this->dom()->new_('a',
      array(
        'class' => $privacy,
        'data-role' => 'button',
        'data-icon' => 'lock',
        'data-shadow' => 0,
        'data-rel' => '',
        'href' => $this->view->url(array('action' => 'privacy', 'page_id' => $page->getIdentity()), 'page_team', true)), $this->view->translate('Privacy Settings')))
      ->append($this->dom()->new_('a',
      array(
        'class' => $photo,
        'data-role' => 'button',
        'data-icon' => 'person',
        'data-shadow' => 1,
        'data-rel' => '',
        'href' => $this->view->url(array('action' => 'photo', 'page_id' => $page->getIdentity()), 'page_team', true)), $this->view->translate('Page Photo')))
      ->append($this->dom()->new_('a',
      array(
        'data-role' => 'button',
        'data-icon' => 'page',
        'data-shadow' => 1,
        'data-rel' => '',
        'href' => $page->getHref()), $this->view->translate('View Page')))
      ->append($this->dom()->new_('a',
      array(
        'data-role' => 'button',
        'data-icon' => 'create',
        'data-shadow' => 1,
        'data-rel' => '',
        'href' => $this->view->url(array(), 'page_create', true)), $this->view->translate('Create Page')))
      ->append($this->dom()->new_('a',
      array(
        'data-role' => 'button',
        'data-icon' => 'delete',
        'data-shadow' => 1,
        'data-rel' => '',
        'href' => $this->view->url(array('action' => 'delete', 'page_id' => $page->getIdentity()), 'page_team', true)), $this->view->translate('Delete Page')));
    return '<br />' . $editMenu . '<br />';
  }

  /* *** Page Controller (page-team route) *************** */

  /* *** Package Controller *************** */
  public function packageInit()
  {
    $this->_session = new Zend_Session_Namespace('Page_Subscription');

    // If no user, redirect to home?
    if (!$this->_helper->requireUser()->isValid())
    {
      return $this->_redirector();
    }

    /**
     * @var $viewer User_Model_User
     */
    $viewer = Engine_Api::_()->user()->getViewer();

    // If there are no enabled packages
    if( Engine_Api::_()->getDbtable('packages', 'page')->getEnabledPackageCount() <= 0 )
    {
      return $this->_redirector();
    }


    $page_id = (int) $this->_getParam('page_id', $this->_session->page_id);

    /**
     * If no page, redirect to browse?
     *
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->getItem('page', $page_id);

    if ( $page && !$page->isOwner($viewer) )
    {
      return $this->_redirector();
    }

    $this->_page = $page;
    $this->_session->page_id = $page_id;
    $this->addPageInfo('contentTheme', 'd');
  }

  public function packageChooseAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $packages = Engine_Api::_()->getDbtable('packages', 'page')->getPackages(array('not_payed' => true));
    $page = $this->_page;
    $available_modules = Engine_Api::_()->getDbtable('modules', 'page')->getAvailableModules();

    $payed_packages = Engine_Api::_()->getDbtable('packages', 'page')->getPackages(array('payed' => true));

    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      $this->renderPackages($page, $payed_packages, $packages, $available_modules);
      return;
    }

    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'page');

    $params = $this->getRequest()->getPost();

    if( !($packageId = $params['package_id']) ||
      !($package = Engine_Api::_()->getItem('page_package', $packageId)) ) {
      $this->renderPackages($page, $payed_packages, $packages, $available_modules);
      return;
    }

    // When choose (not create)
    if( $page ) {
      $currentSubscription = $subscriptionsTable->fetchRow(array(
        'page_id = ?' => $page->getIdentity(),
        'active = ?' => true,
      ));

      // Cancel any other existing subscriptions
      $subscriptionsTable->cancelAll($page, 'User cancelled the subscription.', $currentSubscription);
    }

    // Insert the new temporary subscription
    $db = $subscriptionsTable->getAdapter();
    $db->beginTransaction();

    $page_id = $this->_session->page_id;

    if( !empty($params['is_active']) && $params['is_active'] ) {

      $subscription_id = $params['subscription_id'];
      $subscription = Engine_Api::_()->getItem('page_subscription', $subscription_id);
      $subscription->page_id = $page_id;
      if( $currentSubscription )
        $currentSubscription->cancel();

      $subscription->save();
      $subscription->upgradePage();
      $db->commit();
      return $this->redirect($this->view->url(array('action'=>'edit', 'page_id' => $page_id), 'page_team', true));
    } else {
      try {
        $subscription = $subscriptionsTable->createRow();
        $subscription->setFromArray(array(
          'package_id' => $package->getIdentity(),
          'page_id' => $page_id,
          'status' => 'initial',
          'active' => false, // Will set to active on payment success
          'creation_date' => new Zend_Db_Expr('NOW()'),
        ));
        $subscription->save();

        $subscription_id = $subscription->subscription_id;
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
    }


    // If the package is free, let's set it active now and cancel the other
    if( $package->isFree()) {
      if( $page && $currentSubscription ) {
        $currentSubscription->cancel();
      }

      $subscription->setActive(true);
      $subscription->onPaymentSuccess();

      if( !$page ) {
        return $this->redirect($this->view->url(array('id'=>$subscription->getIdentity()), 'page_create', true));
      }


      $this->_page = $subscription->getPage();
    }


    $this->_session->subscription_id = $subscription_id;

    // Check if the user is good (this will happen if they choose a free plan)

    if( $package->isFree() || !empty($params['is_active']) && $params['is_active'] ) {
      return $this->_finishPayment($package->isFree() ? 'free' : 'active');
    }

    // Otherwise redirect to the payment page
    return $this->redirect($this->view->url(array('action' => 'gateway', 'subscription_id' => $subscription_id, 'page_id' => $page_id), 'page_package', true));
  }

  public function packageGatewayAction()
  {
    // If there are no enabled gateways or packages, disable
    if( Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ) {
      return $this->redirect($this->view->url(array('action' => 'choose', 'page_id' => $this->_getParam('page_id', 0)), 'page_package', true));
    }

    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if( !$subscriptionId ||
      !($subscription = Engine_Api::_()->getItem('page_subscription', $subscriptionId))  ) {
      return $this->redirect($this->view->url(array('action' => 'choose', 'page_id' => $this->_getParam('page_id', 0)), 'page_package', true));
    }

    // Check subscription status
    if( $this->_checkSubscriptionStatus($subscription) ) {
      return;
    }

    // Get subscription
    if( //!$this->_page ||
      !($subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id)) ||
      !($subscription = Engine_Api::_()->getItem('page_subscription', $subscriptionId)) ||
      //$subscription->page_id != $this->_page->getIdentity() ||
      !($package = Engine_Api::_()->getItem('page_package', $subscription->package_id)) ) {
      return $this->redirect($this->view->url(array('action' => 'choose', 'page_id' => $this->_getParam('page_id', 0)), 'page_package', true));
    }

    // Unset certain keys
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);

    // Gateways
    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    $gatewaySelect = $gatewayTable->select()
      ->where('enabled = ?', 1)
      //->where('title = ?', 'PayPal')
    ;
    $gateways = $gatewayTable->fetchAll($gatewaySelect);

    $gatewayPlugins = array();
    foreach( $gateways as $gateway ) {
      $gatewayPlugins[] = array(
        'gateway' => $gateway,
        'plugin' => $gateway->getGateway(),
      );
    }
    $gateways = $gatewayPlugins;

    $form = new Engine_Form();
    $form->setAction($this->view->escape($this->view->url(array('action' => 'process', 'page_id' => $this->_getParam('page_id', 0)), 'page_package'), true));
    $form->setTitle('Please setup your subscription to continue:');
    $form->setDescription($package->getPackageDescription());
    foreach( $gateways as $gatewayInfo ) {
      $gateway = $gatewayInfo['gateway'];
      $plugin = $gatewayInfo['plugin'];
      $form->addElement('Button', 'execute_'.$gateway->gateway_id, array(
        'label' => $this->view->translate('Pay with %1$s', $this->view->translate($gateway->title)),
        'type' => 'submit',
        'onclick' => "$('#gateway_id').val(" . $gateway->gateway_id .");"
      ));
    }
    $form->addElement('Hidden', 'gateway_id', array('order' => 1));

    $desc = $this->view->translate('You have selected an account type that requires ' .
      'recurring subscription payments. You will be taken to a secure ' .
      'checkout area where you can setup your subscription. Remember to ' .
      'continue back to our site after your purchase to sign in to your ' .
      'account.');
    $this->add($this->component()->html('<h3>' . $this->view->translate('Pay for Access') . '</h3>'))
      ->add($this->component()->html($desc))
      ->add($this->component()->form($form))
      ->renderContent();
  }

  public function packageProcessAction()
  {
    // Get gateway
    $gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
    if( !$gatewayId ||
      !($gateway = Engine_Api::_()->getItem('payment_gateway', $gatewayId)) ||
      !($gateway->enabled) ) {
      return $this->redirect($this->view->url(array('action' => 'gateway', 'page_id' => $this->_getParam('page_id', 0)), 'page_package', true));
    }

    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if( !$subscriptionId ||
      !($subscription = Engine_Api::_()->getItem('page_subscription', $subscriptionId))  ) {
      return $this->redirect($this->view->url(array('action' => 'choose', 'page_id' => $this->_getParam('page_id', 0)), 'page_package', true));
    }

    /**
     * Get package
     *
     * @var $package Page_Model_Package
     */
    $package = $subscription->getPackage();
    if( !$package || $package->isFree() ) {
      return $this->redirect($this->view->url(array('action' => 'choose', 'page_id' => $this->_getParam('page_id', 0)), 'page_package', true));
    }

    // Check subscription?
    if( $this->_checkSubscriptionStatus($subscription) ) {
      return;
    }

    // Process

    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    if( !empty($this->_session->order_id) ) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if( $previousOrder && $previousOrder->state == 'pending' ) {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }

    /**
     * @var $user User_Model_User;
     */
    $user = Engine_Api::_()->user()->getViewer();
    $ordersTable->insert(array(
      'user_id' => $user->getIdentity(),
      'gateway_id' => $gateway->gateway_id,
      'state' => 'pending',
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'source_type' => 'page_subscription',
      'source_id' => $subscription->subscription_id,
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    // Unset certain keys
    unset($this->_session->package_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);


    // Get gateway plugin
    $gatewayPlugin = $gateway->getGateway();

    /**
     * @var $plugin Page_Plugin_Gateway_PayPal
     */
    $plugin = $gateway->getPlugin();

    // Get Page gateway plugin
    $str = str_replace('Payment', 'Page', get_class($plugin));
    $plugin = new $str( $gateway );


    // Prepare host info
    $schema = 'http://';
    if( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];


    // Prepare transaction
    $params = array();
    $params['vendor_order_id'] = $order_id;
    $params['return_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $order_id
      //. '?gateway_id=' . $this->_gateway->gateway_id
      //. '&subscription_id=' . $this->_subscription->subscription_id
      . '&state=' . 'return';
    $params['cancel_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $order_id
      //. '?gateway_id=' . $this->_gateway->gateway_id
      //. '&subscription_id=' . $this->_subscription->subscription_id
      . '&state=' . 'cancel';
    $params['ipn_url'] = $schema . $host
      . $this->view->url(array('action' => 'index', 'controller' => 'ipn'))
      . '?order_id=' . $order_id;
    //. '?gateway_id=' . $this->_gateway->gateway_id
    //. '&subscription_id=' . $this->_subscription->subscription_id;

    // Process transaction
    $transaction = $plugin->createPageSubscription($subscription, $package, $params);

    // Pull transaction params
    $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $transactionData = $transaction->getData();

    $form = new Engine_Form();
    $form->setTitle('Please wait...');
    $form->setAction($transactionUrl);
    $form->setMethod($transactionMethod);

    $order = 0;
    foreach( $transactionData as $key => $data ) {
      $form->addElement('Hidden', $key, array(
        'value' => $data,
        'order' => $order
      ));
      $order++;
    }

    $this->add($this->component()->form($form))
      ->renderContent();
  }

  public function packageReturnAction()
  {
    // Get order
    if( //!$this->_page ||
      !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
      !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
      $order->source_type != 'page_subscription' ||
      !($subscription = $order->getSource()) ||
      !($package = $subscription->getPackage()) ||
      !($gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id)) ) {

      return $this->redirect(array(), 'default', true);
    }

    // Get gateway plugin
    $gatewayPlugin = $gateway->getGateway();

    /**
     * @var $plugin Page_Plugin_Gateway_PayPal
     */
    $plugin = $gateway->getPlugin();

    // Get Store gateway plugin
    $str = str_replace('Payment', 'Page', get_class($plugin));
    $plugin = new $str( $gateway );

    // Process return
    unset($this->_session->errorMessage);
    try {
      $status = $plugin->onPageSubscriptionReturn($order, $this->_getAllParams());
    } catch( Page_Model_Exception $e ) {
      $status = 'failure';
      $this->_session->errorMessage = $e->getMessage();
    }

    if( $subscription->page_id == 0 && $status != 'failure') {
      return $this->redirect($this->view->url(array('id' => $subscription->subscription_id), 'page_create', true));
    }
    return $this->_finishPayment($status);
  }

  public function packageFinishAction()
  {
    $page = $this->_page;
    $status = $this->_getParam('state');
    $error = $this->_session->errorMessage;

    if( $page && $page->getIdentity() ) {
      $form = new Engine_Form();
      $form->setMethod('GET');
      $form->setAction($this->view->url(array('action'=>'edit', 'page_id'=>$page->getIdentity()), 'page_team'));
      if( $status == 'pending' ) {
        $form->setTitle('Payment Pending');
        $form->setDescription('Thank you for submitting your ' .
          'payment. Your payment is currently pending - your account ' .
          'will be activated when we are notified that the payment has ' .
          'completed successfully. Please return to our login page ' .
          'when you receive an email notifying you that the payment ' .
          'has completed.');
        $form->addElement('Button', 'submit', array(
          'label' => 'Back to Home',
          'type' => 'submit'
        ));
      } elseif( $status == 'active' ) {
        $form->setTitle('Payment Complete');
        $form->setDescription('Thank you! Your payment has ' .
          'completed successfully.');
        $form->addElement('Button', 'submit', array(
          'label' => 'Continue',
          'type' => 'submit'
        ));
      } else {
        $form->setTitle('Payment Failed');
        if( !$error )
          $error = 'Our payment processor has notified ' .
            'us that your payment could not be completed successfully. ' .
            'We suggest that you try again with another credit card ' .
            'or funding source.';

        $form->setDescription($error);
        $form->addElement('Button', 'submit', array(
          'label' => 'Back to Home',
          'type' => 'submit'
        ));
      }

      $this->add($this->component()->form($form));
    } else {
      $this->add($this->component()->html($this->view->translate('Your payment has been cancelled and not been charged. If this is not correct, please try again later.')));
    }

    $this->renderContent();
  }

  protected function _finishPayment($state = 'active')
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // No page?
//    if( !$this->_page ) {
//      return $this->_helper->redirector->gotoRoute(array(), 'page_create', true);
//    }

    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $page_id = $this->_session->page_id;
    $this->_session->unsetAll();
    $this->_session->page_id = $page_id;
    $this->_session->errorMessage = $errorMessage;

    // Redirect
    if( $state == 'free' ) {
      return $this->redirect($this->view->url(array('action'=>'edit', 'page_id' => $page_id), 'page_team'));
    } else {
      return $this->redirect($this->view->url(array('action' => 'finish', 'state' => $state), 'page_package', true));
    }
  }

  protected function _checkSubscriptionStatus(
    Zend_Db_Table_Row_Abstract $subscription = null)
  {
    if( !$this->_page ) {
      return false;
    }

    if( null === $subscription ) {
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'page');
      $subscription = $subscriptionsTable->fetchRow(array(
        'page_id = ?' => $this->_page->getIdentity(),
        'active = ?' => true,
      ));
    }

    if( !$subscription ) {
      return false;
    }

    if( $subscription->status == 'active' ||
      $subscription->status == 'trial' ) {
      if( !$subscription->getPackage()->isFree() ) {
        $this->_finishPayment('active');
      } else {
        $this->_finishPayment('free');
      }
      return true;
    } else if( $subscription->status == 'pending' ) {
      $this->_finishPayment('pending');
      return true;
    }

    return false;
  }

  protected function _redirector()
  {
    $this->_session->unsetAll();
    return $this->redirect($this->view->url(array(), 'page_browse', true));
  }

  protected function getNavigation($page = null)
  {
    $navigation = new Zend_Navigation();

    $navigation->addPages(array(
      array(
        'label'  => "Create Page",
        'route'  => 'page_create',
        'data_attrs' => ''
      ),
      array(
        'label'  => "View Page",
        'route'  => 'page_view',
        'page_id' => $page->url,
        'params' => array('page_id' => $page->url),
        'data_attrs' => ''
      ),
      array(
        'label'  => "Delete Page",
        'route'  => 'page_team',
        'action' => 'delete',
        'params' => array('page_id' => $page->page_id),
        'data_attrs' => ''
      )
    ));

    return $navigation;
  }

  protected function renderPackages($page, $payed_packages, $packages, $available_modules)
  {
    $this->setPageTitle($this->view->translate('pagetitle-page-choose-package'));

    if($page) {
      $this->add($this->component()->subjectPhoto($page))
        ->add($this->component()->crumb($this->getNavigation($page)));
    } else
      $this->add($this->component()->navigation('page_main', true));

    if( count($payed_packages) ) {
      $this->add($this->component()->html('<h3>'.$this->view->translate('PAGE_Subscribe Paid Page').'</h3>'))
        ->add($this->component()->html($this->view->translate('PAGE_PACKAGE_CHOOSE_PAID')));
      $yes = $this->view->translate('Yes');
      $no = $this->view->translate('No');
      foreach($payed_packages as $package) {
        $element = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'd'));
        $title = $this->dom()->new_('h3', array(), $package->getTitle());
        $text = '<table><tr><th>' . $this->view->translate('Price') . '</th><td> : ' . $this->view->locale()->toCurrency($package->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('PAGE_Auto Approved') . '</th><td> : ' . (($package->autoapprove) ? $yes : $no) . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('PAGE_Sponsored') . '</th><td> : ' . (($package->sponsored) ? $yes : $no) . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('PAGE_Featured') . '</th><td> : ' . (($package->featured) ? $yes : $no) . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('Billing') . '</th><td> : ' . $package->getPackageDescription() . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('PAGE_Column Change') . '</th><td> : ' . (($package->edit_columns) ? $yes : $no) . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('PAGE_Layout Editor') . '</th><td> : ' . (($package->edit_layout) ? $yes : $no) . '</td></tr>';

        foreach($available_modules as $key => $module) {
          $text .= '<tr><th>' . $this->view->translate($module) . '</th><td> : ' . ((in_array($key,is_array($package->modules) ? $package->modules : array())) ? $yes : $no) . '</td></tr>';
        }

        $text .= '</table>';
        $text .= $package->description;

        if( $page ) {
          $form = new Engine_Form();
          $form->setAttrib('class', '');
          $form->addElement('Hidden', 'package_id', array(
            'order' => 0,
            'value' => $package->getIdentity()
          ));
          $form->addElement('Hidden', 'subscription_id', array(
            'order' => 1,
            'value' => $package->subscription_id
          ));
          $form->addElement('Hidden', 'is_active', array(
            'order' => 2,
            'value' => 1
          ));
          $form->addElement('Button', 'submit', array(
            'order' => 3,
            'label' => 'Change Package',
            'type' => 'submit'
          ));

          $text .= $form->render() . '<br>' . $this->view->translate('or') . '<br>';
        }

        $create_btn = '<a data-role="button" href="' . $this->view->url(array('id' => $package->subscription_id), 'page_create') . '">' . $this->view->translate('Create New Page') . '</a>';
        $text .= $create_btn;

        $content = $this->dom()->new_('p', array(), $text);
        $element->append($title);
        $element->append($content);
        $this->add($this->component()->html($element));
      }
    }

    if(count($packages)) {
      $this->add($this->component()->html('<h3>'.$this->view->translate('PAGE_Subscribe Page').'</h3>'))
        ->add($this->component()->html($this->view->translate('PAGE_VIEWS_SCRIPTS_PACKAGE_CHOOSE_DESCRIPTION')));
      $yes = $this->view->translate('Yes');
      $no = $this->view->translate('No');
      foreach($packages as $package) {
        $element = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'd'));
        $title = $this->dom()->new_('h3', array(), $package->getTitle());
        $text = '<table><tr><th>' . $this->view->translate('Price') . '</th><td> : ' . $this->view->locale()->toCurrency($package->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('PAGE_Auto Approved') . '</th><td> : ' . (($package->autoapprove) ? $yes : $no) . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('PAGE_Sponsored') . '</th><td> : ' . (($package->sponsored) ? $yes : $no) . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('PAGE_Featured') . '</th><td> : ' . (($package->featured) ? $yes : $no) . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('Billing') . '</th><td> : ' . $package->getPackageDescription() . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('PAGE_Column Change') . '</th><td> : ' . (($package->edit_columns) ? $yes : $no) . '</td></tr>';
        $text .= '<tr><th>' . $this->view->translate('PAGE_Layout Editor') . '</th><td> : ' . (($package->edit_layout) ? $yes : $no) . '</td></tr>';

        foreach($available_modules as $key => $module) {
          $text .= '<tr><th>' . $this->view->translate($module) . '</th><td> : ' . ((in_array($key,is_array($package->modules) ? $package->modules : array())) ? $yes : $no) . '</td></tr>';
        }

        $text .= '</table>';
        $text .= $package->description;

        $form = new Engine_Form();
        $form->setAttrib('class', '');
        $form->addElement('Hidden', 'package_id', array(
          'order' => 0,
          'value' => $package->getIdentity()
        ));
        $form->addElement('Button', 'submit', array(
          'order' => 1,
          'label' => 'Continue',
          'type' => 'submit'
        ));
        $text .= $form->render();

        $content = $this->dom()->new_('p', array(), $text);
        $element->append($title);
        $element->append($content);
        $this->add($this->component()->html($element));
      }
    }

    $this->renderContent();
  }

  /* *** Package Controller *************** */
  /*----------------------------------------------
 * view content functions
 * -------------------------------------------
 * */
  public function viewPagealbum($id)
  {
    $album = Engine_Api::_()->getItem('pagealbum', $id);
    $paginator = $album->getCollectiblesPaginator();

    $this->add($this->component()->subjectPhoto($album))
      ->add($this->component()->html($album->description));
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
            'type'=>'pagealbum',
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

    $this->add($this->component()->gallery($paginator))
      ->add($this->component()->paginator($paginator))
      ->add($this->component()->comments(array('subject' => $album)))
      ->add($this->component()->navigation('pagealbum', true), -1)
      ->renderContent();
  }

  public function viewBlog($id)
  {
    $blog = Engine_Api::_()->getItem('pageblog', $id);
    $owner = $blog->getOwner();
    $viewer = Engine_Api::_()->user()->getViewer();
    $this
      ->add($this->component()->subjectPhoto($blog))
      ->add($this->component()->html($blog->title));
      $this->add($this->component()->date(array('title' => $this->view->translate('Posted by') . ' ' . $owner->getTitle() . ' ' . $this->view->timestamp($blog->creation_date), 'count' => null)));
    $controlGroup = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal', 'style' => 'text-align: center'));
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
            'type'=>'pageblog',
            'id' => $blog->getIdentity()), 'default', true)), $this->view->translate('Share')))

        ->append($this->dom()->new_('a',
        array(
          'data-role' => 'button',
          'data-icon' => 'flag',
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
            'module'=>'core',
            'controller'=>'report',
            'action'=>'create',
            'subject'=>$blog->getGuid(),
            'id' => $blog->getIdentity()), 'default', true)), $this->view->translate('Report')));

      $this->add($this->component()->html($controlGroup . '<br />'));
    }

    $blogBody = '<div class="blog_body">'. nl2br($blog->body) .'</div>';

    $this->add($this->component()->html($blogBody))
      ->add($this->component()->comments(array('subject' => $blog)))
      ->add($this->component()->navigation('pageblog', true), -1)
      ->renderContent();
  }

  public function viewDiscussion($id)
  {
    $topic = Engine_Api::_()->getItem('pagediscussion_pagetopic', $id);
    $page = $topic->getParentPage();
    $page_id = $page->getIdentity();
    $allowPost = Engine_Api::_()->getApi('core', 'pagediscussion')->isAllowedPost($page);
    $viewer = Engine_Api::_()->user()->getViewer();
    $paginator = $topic->getPostPaginator($this->_getParam('page'), 0);

    $option = array();
    $option['options'] = array();
    $option['options'][] = array(
      'label' => $this->view->translate('PAGEDISCUSSION_OPTIONS_BACK'),
      'attrs' => array(
        'href' => $this->view->url(array('page_id' => $page->url, 'tab' => 'discussion'), 'page_view', true),
        'class' => 'buttonlink'
      )
    );

    if ($viewer && $viewer->getIdentity()) {
      if ($allowPost && !$topic->closed) {
        $form = new Pagediscussion_Form_Post();
        $form->removeElement('cancel');
        $form->removeAttrib('onsubmit');
        $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'post', 'page_id' => $page_id, 'discussion_id' => $topic->getIdentity()), 'page_discussion'));

        $option['postForm'] = $form;
      }

      if ($topic->isWatching($viewer->getIdentity())) {
        $option['options'][] = array(
          'label' => $this->view->translate('PAGEDISCUSSION_OPTIONS_UNWATCHING'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'watch', 'page_id' => $page_id, 'discussion_id' => $topic->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
            'class' => 'buttonlink',
          )
        );
      } else {
        $option['options'][] = array(
          'label' => $this->view->translate('PAGEDISCUSSION_OPTIONS_WATCHING'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'watch', 'page_id' => $page_id, 'discussion_id' => $topic->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
            'class' => 'buttonlink',
          )
        );
      }

      if ($page->isTeamMember($viewer)) {
        if ($topic->sticky) {
          $option['options'][] = array(
            'label' => $this->view->translate('PAGEDISCUSSION_OPTIONS_UNSTICKY'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'sticky', 'page_id' => $page_id, 'discussion_id' => $topic->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
              'class' => 'buttonlink',
            )
          );
        } else {
          $option['options'][] = array(
            'label' => $this->view->translate('PAGEDISCUSSION_OPTIONS_STICKY'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'sticky', 'page_id' => $page_id, 'discussion_id' => $topic->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
              'class' => 'buttonlink',
            )
          );
        }

        if ($topic->closed) {
          $option['options'][] = array(
            'label' => $this->view->translate('PAGEDISCUSSION_OPTIONS_UNCLOSE'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'closed', 'page_id' => $page_id, 'discussion_id' => $topic->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
              'class' => 'buttonlink',
            )
          );
        } else {
          $option['options'][] = array(
            'label' => $this->view->translate('PAGEDISCUSSION_OPTIONS_CLOSE'),
            'attrs' => array(
              'href' => $this->view->url(array('action' => 'closed', 'page_id' => $page_id, 'discussion_id' => $topic->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
              'class' => 'buttonlink',
            )
          );
        }
      }

      if ($page->isTeamMember($viewer) || $topic->isOwner($viewer)) {
        $option['options'][] = array(
          'label' => $this->view->translate('PAGEDISCUSSION_OPTIONS_RENAME'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'rename', 'page_id' => $page_id, 'discussion_id' => $topic->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
            'class' => 'buttonlink'
          )
        );
        $option['options'][] = array(
          'label' => $this->view->translate('PAGEDISCUSSION_OPTIONS_DELETE'),
          'attrs' => array(
            'href' => $this->view->url(array('action' => 'delete', 'page_id' => $page_id, 'discussion_id' => $topic->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
            'class' => 'buttonlink smoothbox',
          )
        );
      }
    }

    $this->add($this->component()->subjectPhoto($page))
      ->add($this->component()->discussion($topic, $paginator, $option, 'browsePostData'))
      ->renderContent();
  }

  public function viewDocument($id)
  {
    $document = Engine_Api::_()->getItem('pagedocument', $id);
    $owner = $document->getOwner();
    $this->add($this->component()->navigation('pagedocument', true), -1)
      ->add($this->component()->subjectPhoto($document->getPage()))
      ->add($this->component()->date(array('title' => $this->view->translate('Posted by') . ' ' . $owner->getTitle() . ' ' . $this->view->timestamp($document->creation_date), 'count' => null)))
      ->add($this->component()->html($document->document_description))
      ->add($this->component()->comments(array('subject' => $document)))
      ->renderContent();
  }

  public function viewPage_event($id)
  {
    $event = Engine_Api::_()->getItem('pageevent', $id);
    $page = $event->getPage();
    $viewer = Engine_Api::_()->user()->getViewer();

    $values = array();
    $values[] = array(
      'title' => $this->view->translate('PAGEVENT_ONWER', $event->getOwner()->__toString()),
      'content' => array(
        array(
          'label' => $this->view->translate('Title'),
          'value' => $event->getTitle()
        ),
        array(
          'label' => $this->view->translate('Posted'),
          'value' => $this->view->timestamp($event->creation_date)
        ),
        array(
          'label' => $this->view->translate('Date'),
          'value' => $this->view->translate('%1$s at %2$s', $this->view->locale()->toDate(new Zend_Date(strtotime($event->starttime))), $this->view->locale()->toTime(new Zend_Date(strtotime($event->starttime)))) . '-' .
            $this->view->translate('%1$s at %2$s', $this->view->locale()->toDate(new Zend_Date(strtotime($event->endtime))), $this->view->locale()->toTime(new Zend_Date(strtotime($event->endtime))))
        ),
        array(
          'label' => $this->view->translate('PAGEEVENT_WHERE'),
          'value' => $event->location . ' ' . $this->view->htmlLink('http://maps.google.com/?q=' . urlencode($event->location), $this->view->translate('PAGEEVENT_MAP'), array('target' => 'blank'))
        ),
        array(
          'label' => $this->view->translate('Description'),
          'value' => $event->getDescription()
        ),
      )
    );

    $membership = $event->membership();

    // Attending members
    $attending = $membership->getMemberPaginator(2);
    $value = '';
    foreach ($attending as $member) {
      $value = $value . "<div class='pageevent_members'>" . $this->view->htmlLink($member->getHref(), $this->view->itemPhoto($member, 'thumb.icon') . '<br>' . $member->getTitle()) . "</div>";
    }
    $values[0]['content'][] = array(
      'label' => $this->view->translate('PAGEEVENT_MEMBERS_ATTENDING', array($attending->getTotalItemCount())),
      'value' => $value
    );

    // May be Attending members
    $maybe_attending = $membership->getMemberPaginator(1);
    if ($maybe_attending->getTotalItemCount()) {
      $value = '';
      foreach ($maybe_attending as $member) {
        $value = $value . "<div class='pageevent_members'>" . $this->view->htmlLink($member->getHref(), $this->view->itemPhoto($member, 'thumb.icon') . '<br>' . $member->getTitle()) . "</div>";
      }
      $values[0]['content'][] = array(
        'label' => $this->view->translate('PAGEEVENT_MEMBERS_MAYBE_ATTENDING', array($maybe_attending->getTotalItemCount())),
        'value' => $value
      );
    }

    // Not Attending members
    $not_attending = $membership->getMemberPaginator(0);
    if ($not_attending->getTotalItemCount()) {
      $value = '';
      foreach ($not_attending as $member) {
        $value = $value . "<div class='pageevent_members'>" . $this->view->htmlLink($member->getHref(), $this->view->itemPhoto($member, 'thumb.icon') . '<br>' . $member->getTitle()) . "</div>";
      }
      $values[0]['content'][] = array(
        'label' => $this->view->translate('PAGEEVENT_MEMBERS_NOT_ATTENDING', array($not_attending->getTotalItemCount())),
        'value' => $value
      );
    }

    // Status
    $member = $membership->getRow($viewer);

    if ($viewer->getIdentity() && (!$member || $member->resource_approved) && (!$event->approval || $member)) {
      $url = array(
        'route' => 'page_event',
        'action' => 'rsvp',
        'page_id' => $page->getIdentity(),
        'event_id' => $event->getIdentity(),
        'rsvp' => 2
      );

      $attend = $this->view->htmlLink($url, $this->view->translate('PAGEEVENT_ATTENDING'), array(
        'class' => ($member && $member->rsvp == 2) ? 'ui-btn-active' : '',
        'data-role' => 'button',
      ));

      $url['rsvp'] = 1;
      $maybe_atten = $this->view->htmlLink($url, $this->view->translate('PAGEEVENT_MAYBEATTENDING'), array(
        'class' => ($member && $member->rsvp == 1) ? 'ui-btn-active' : '',
        'data-role' => 'button',
      ));

      $url['rsvp'] = 0;
      $not_atten = $this->view->htmlLink($url, $this->view->translate('PAGEEVENT_NOTATTENDING'), array(
        'class' => ($member && $member->rsvp == 0) ? 'ui-btn-active' : '',
        'data-role' => 'button',
      ));

      $values[0]['content'][] = array(
        'label' => $this->view->translate('Status'),
        'value' => '<div data-role="controlgroup" data-type="horizontal" data-mini="true">' . $attend . $maybe_atten . $not_atten . '</div>'
      );
    }

    $this->add($this->component()->navigation('pageevent', true), -1)
      ->add($this->component()->quickLinks('pageevent_quick_view', true))
      ->add($this->component()->subjectPhoto($event))
      ->add(($this->component()->customComponent('fieldsValues', $values)))
      ->add($this->component()->comments(array('subject' => $event)));

    $this->renderContent();
  }

  public function viewPlaylist($id)
  {
    if (!$id) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $playlist = Engine_Api::_()->getItem('playlist', $id);

    if (!$playlist) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this
      ->add($this->component()->subjectPhoto($playlist));
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
            'type'=>$playlist->getType(),
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

      $this->add($this->component()->html('<br />' . $controlGroup . '<br />'));
    }

      $this->add($this->component()->playlist($playlist->getSongs()))
      ->add($this->component()->mediaControls())
      ->add($this->component()->navigation('pagemusic', true), -1)
      ->renderContent();
  }

  public function viewReview($id)
  {
    $review = Engine_Api::_()->getDbTable('pagereviews', 'rate')->findRow((int)$id);
    $page = $review->getPage();
    $owner = $review->getOwner();

    $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
    $select = $tbl_vote->select()
      ->where('review_id = ?', $review->getIdentity());
    $votes = $tbl_vote->fetchAll($select);

    $vote_list = array();
    foreach ($votes as $vote) {
      $vote_list[$vote->type_id] = $vote->rating;
    }
    $types = Engine_Api::_()->getApi('core', 'rate')->getPageTypes($review->page_id);
    foreach ($types as $key => $type) {
      if (isset($vote_list[$type->type_id])) {
        $types[$key]->value = $vote_list[$type->type_id];
      }
    }

    $html = '';
    foreach ($types as $type) {
      $html = $html . '<div class="review_stars">';
      $html = $html . $this->view->reviewRate($type->value);
      $html = $html . '<div class="title">' . $type->label . '</div>';
      $html = $html . '</div>';
    }

    $option = array(
      array(
        'label' => $this->view->translate('RATE_REVIEW_BACK'),
        'attrs' => array(
          'href' => $this->view->url(array('page_id' => $page->url, 'tab' => 'reviews'), 'page_view', true)
        )
      )
    );

    $this->add($this->component()->quickLinks('gutter'))
      ->add($this->component()->subjectPhoto($review->getPage()))
      ->add($this->component()->html($review->title))
      ->add($this->component()->date(array('title' => $this->view->translate('Posted by') . ' ' . $owner->getTitle() . ' ' . $this->view->timestamp($review->creation_date), 'count' => null)))
      ->add($this->component()->html($review->body))
      ->add($this->component()->html($html))
      ->add($this->component()->comments(array('subject' => $review)))
      ->add($this->component()->customComponent('navigation', $option), -1)
      ->renderContent();
  }

  public function viewVideo($id)
  {
    if (!$id) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $video = Engine_Api::_()->getItem('pagevideo', $id);

    if (!$video) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this->add($this->component()->subjectPhoto($video))
      ->add($this->component()->navigation('pagevideo', true), -1)
      ->add($this->component()->video($video))
      ->add($this->component()->html($video->getDescription()));
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
            'type'=>'pagevideo',
            'id' => $video->getIdentity()), 'default', true)), $this->view->translate('Share')))

        ->append($this->dom()->new_('a',
        array(
          'data-role' => 'button',
          'data-icon' => 'flag',
          'data-rel' => 'dialog',
          'href' => $this->view->url(array(
            'module'=>'core',
            'controller'=>'report',
            'action'=>'create',
            'subject'=>$video->getGuid(),
            'id' => $video->getIdentity()), 'default', true)), $this->view->translate('Report')));

      $this->add($this->component()->html($controlGroup . '<br />'));
    }

      $this->add($this->component()->comments(array('subject' => $video)))
      ->renderContent();
  }

  /*----------------------------------------------
 * view content functions
 * -------------------------------------------
 * */

  public function indexCreateAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams('page', null, 'create')->isValid()) return;

    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    if ($settings->getSetting('page.package.enabled', 0)) {
      $subscription_id = $this->_getParam('id', 0);
      $subscription = Engine_Api::_()->getItem('page_subscription', $subscription_id);
      if( !$subscription || $subscription->page_id != 0 )
        return $this->redirect($this->view->url(array('page_id' => 0), 'page_package_choose', true));
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('pages', 'page');

    if (!$settings->getSetting('page.package.enabled', 0)) {
      $allowed_pages_arr = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'allowed_pages');
      $allowed_pages = $allowed_pages_arr[0] - 6;
      $existing_pages_count = $table->select()->from(array($table->info('name')), array('count' => new Zend_Db_Expr("count('page_id')")))->where('user_id = ?', $viewer->getIdentity())->query()->fetch();
      if( $existing_pages_count['count'] >= $allowed_pages) {
        $this->add($this->component()->html('<h3>'.$this->view->translate('PAGE_MAXIMUM_ALLOWED_TITLE').'</h3>'))
          ->add($this->component()->html($this->view->translate('PAGE_MAXIMUM_ALLOWED_DESCRIPTION')))
          ->renderContent();
        return;
      }
    }

    $form = new Page_Form_Create();
    $table = Engine_Api::_()->getDbTable('pages', 'page');

    //$form->removeElement('token'); //if you have any questions about it ask for Ulan L :)
    //$form->removeElement('photo');

    $this->addPageInfo('isMultiMode', count($form->getSetInfo()));
    $this->addPageInfo('setInfoJSON', json_encode($form->getSetInfo()));
    $form->getSubForm('fields')->getElement('0_0_1')->setLabel('Category');

    $this->setFormat('create');
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

    $values = $form->getValues();

    if ($table->checkUrl($values['url'])) {
      $form->addError(Zend_Registry::get('Zend_Translate')->_('This URL is already taken by other page.'));
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $values['url'] = strtolower(trim($values['url']));
    $values['url'] = preg_replace('/[^a-z0-9-]/', '-', $values['url']);
    $values['url'] = preg_replace('/-+/', "-", $values['url']);

    $this->_createDefaultContent();

    // Process
    $table = Engine_Api::_()->getItemTable('page');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      // Create page
      $viewer = Engine_Api::_()->user()->getViewer();
      $values = array_merge($values, array(
        'parent_type' => $viewer->getType(),
        'parent_id' => $viewer->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      ));
      $page = $table->createRow();
      $page->setFromArray($values);
      $page->set_id = $values['category'];
      $page->displayname = $page->title;
      $page->name = $page->url;
      $page->save();

      $raw_tags = preg_split('/[,]+/', $values['tags']);
      $tags = array();
      foreach ($raw_tags as $tag) {
        $tag = trim(strip_tags($tag));
        if ($tag == "") {
          continue;
        }
        $tags[] = $tag;
      }
      $page->tags()->addTagMaps($viewer, $tags);
      unset($values['tags']);

      $page->keywords = implode(",", $tags);

      $page->membership()->addMember($viewer)->setUserApproved($viewer)->setResourceApproved($viewer)->setUserTypeAdmin($viewer);
      $page->setAdmin($viewer);
      $page->getTeamList()->add($viewer);

      $photo = $this->getPicupFiles('photo');

      // Set photo
      if (!empty($values['photo'])) {
        $page->setPhoto($form->photo);
      } else if (!empty($photo)) {
        $photo = $photo[0];
        $page->setPhoto($photo);
      }

      $page->createContent();

      // Add fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($page);
      $customfieldform->saveValues();
      $customfieldform->removeElement('submit');

      // Privacy
      $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'Registered Members',
        'likes' => 'Likes, Admins and Owner',
        'team' => 'Admins and Owner Only'
      );

      /**
       * @var $package Page_Model_Package
       * @var $authTb Authorization_Model_DbTable_Permissions
       */
      if ($settings->getSetting('page.package.enabled', 0)) {
        if ($subscription && $subscription->page_id == 0) {
          $package = $subscription->getPackage();
        } else {
          $package = Engine_Api::_()->getItemTable('page_package')->getDefaultPackage();
        }

        $page->package_id = $package->getIdentity();
        $page->featured = $package->featured;
        $page->sponsored = $package->sponsored;
        $page->approved = $package->autoapprove;
        $page->enabled = true;

        $view_options = array_intersect_key($availableLabels, array_flip($package->auth_view));
        $comment_options = array_intersect_key($availableLabels, array_flip($package->auth_comment));
        $posting_options = array_intersect_key($availableLabels, array_flip($package->auth_posting));
      } else {
        $authTb = Engine_Api::_()->authorization()->getAdapter('levels');
        $page->approved = (int) $authTb->getAllowed('page', $viewer, 'auto_approve');
        $page->featured = (int) $authTb->getAllowed('page', $viewer, 'featured');
        $page->sponsored = (int) $authTb->getAllowed('page', $viewer, 'sponsored');
        $page->enabled = 1;

        $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_view');
        $view_options = array_intersect_key($availableLabels, array_flip($view_options));

        $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_comment');
        $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

        $posting_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_posting');
        $posting_options = array_intersect_key($availableLabels, array_flip($posting_options));
      }

      if ($page->save()) {
        $values = array(
          'auth_view' => key($view_options),
          'auth_comment' => key($comment_options),
          'auth_album_posting' => key($posting_options),
          'auth_blog_posting' => key($posting_options),
          'auth_disc_posting' => key($posting_options),
          'auth_doc_posting' => key($posting_options),
          'auth_event_posting' => key($posting_options),
          'auth_music_posting' => key($posting_options),
          'auth_video_posting' => key($posting_options)
        );
        $page->setPrivacy($values);

        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($viewer, $page, 'page_create', null, array('is_mobile' => true));
        if ($action) {
          $activityApi->attachActivity($action, $page);
        }
      }

      if ($settings->getSetting('page.package.enabled', 0) && $subscription->page_id == 0) {
        $subscription->page_id = $page->page_id;
        $subscription->save();
      }

      // Commit
      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
    return $this->redirect($this->view->url(array('action' => 'edit', 'page_id' => $page->page_id), 'page_team'));
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
      ->add($this->component()->gallery($paginator))
      ->add($this->component()->paginator($paginator));
    if ($canEdit || $mine)
      $this->add($this->component()->quickLinks($this->getOptions(Engine_Api::_()->core()->getSubject(), "manage")));
    $this->renderContent();
  }

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
    $table = Engine_Api::_()->getItemTable('page');
    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $select = $table->select()->where("search = 1")->order($order . ' DESC');

    $user_id = $this->_getParam('user');
    if ($user_id)
      $select->where("user_id = ?", $user_id);
    else
      $select->where("user_id != ?", 0);

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }
    return $select;
  }

  public function browseItemData(Core_Model_Item_Abstract $item)
  {
    /**
     * @var $item Page_Model_Page
    */
    $customize_fields = array(
            'creation_date' => null,
            'descriptions' => array(
              $item->getAddress(),
              $this->view->itemRate('page', $item->getIdentity()) . ' &nbsp; ' . strtolower($item->getLikesCount() == 1 ? $item->getLikesCount() . ' ' . $this->view->translate('Like') : $item->getLikesCount() . ' ' . $this->view->translate('Likes'))
            )
    );
    return $customize_fields;
  }

  public function browsePostData(Core_Model_Item_Abstract $item)
  {
    $topic = $item->getParent();
    $page = $topic->getParentPage();

    $option = array();

    $canPost = Engine_Api::_()->getApi('core', 'pagediscussion')->isAllowedPost($page);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$topic->closed && $canPost && $viewer && $viewer->getIdentity()) {
      $option[] = array(
        'label' => $this->view->translate('PAGEDISCUSSION_POST_QUOTE'),
        'attrs' => array(
          'href' => $this->view->url(array('action' => 'quote', 'page_id' => $page->getIdentity(), 'post_id' => $item->getIdentity()), 'page_discussion', true),
          'data-icon' => 'chat'
        )
      );
    }

    if ($viewer && $viewer->getIdentity() && ($item->isOwner($viewer) || $page->isAdmin($viewer))) {
      $option[] = array(
        'label' => $this->view->translate('PAGEDISCUSSION_POST_EDIT'),
        'attrs' => array(
          'href' => $this->view->url(array('action' => 'edit-post', 'page_id' => $page->getIdentity(), 'post_id' => $item->getIdentity()), 'page_discussion', true),
          'data-icon' => 'edit'
        )
      );
      $option[] = array(
        'label' => $this->view->translate('PAGEDISCUSSION_POST_DELETE'),
        'attrs' => array(
          'href' => $this->view->url(array('action' => 'delete-post', 'page_id' => $page->getIdentity(), 'post_id' => $item->getIdentity()), 'page_discussion', true),
          'data-icon' => 'delete',
          'data-rel' => 'dialog'
        )
      );
    }

    $customize_fields = array(
      'options' => $option
    );

    return $customize_fields;
  }

  public function manageItemData(Core_Model_Item_Abstract $item)
  {
    $customize_fields = array(
//                    'description' => null,
//                    'creation_date' => null,
      'user_id' => $item->user_id,
      'manage' => $this->getOptions($item)
    );
    return $customize_fields;
  }

  private function prepareManageOptions()
  {
    return array(

    );
  }

  private function _createDefaultContent()
  {
    $pageTable = Engine_Api::_()->getDbTable('pages', 'page');
    $page = "default";

    $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('page_id = ?', $page));
    $contentTable = Engine_Api::_()->getDbtable('content', 'page');

    $contentDefault = $contentTable->fetchAll($contentTable->select()->where('page_id=?', $pageObject->getIdentity()));

    if (count($contentDefault) == 0) {
      $pageTable->createContentFirstTime($pageObject->getIdentity());
    }
  }

  public function tabStaff($active = false)
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $page_id = $request->getParam('page_id');

    if (!Engine_Api::_()->core()->hasSubject()) {
      if (!$page_id)
        return;
      else {
        $page = Engine_Api::_()->getItem('page', $page_id);
        Engine_Api::_()->core()->setSubject($page);
      }
    }
    else
      $page = Engine_Api::_()->core()->getSubject('page');

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$page->authorization()->isAllowed($viewer, 'view')) {
      return;
    }

    $table = Engine_Api::_()->getDbtable('users', 'user');
    $tableName = $table->info('name');

    $select = $table->select()
      ->setIntegrityCheck(false);

    $prefix = $table->getTablePrefix();

    $select
      ->from($tableName)
      ->joinLeft($prefix . "page_membership", $prefix . "page_membership.user_id = {$tableName}.user_id", array('title', 'type'))
      ->where($prefix . "page_membership.resource_id = {$page->page_id}")
      ->order($prefix . "page_membership.type ASC");

    if ($active && $this->_getParam('search', false)) {
      $select->where('displayname LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $team = Zend_Paginator::factory($select);

    if ($team->getTotalItemCount() < 0) {
      return;
    }

    $team->setCurrentPageNumber($request->getParam('page', 1));
    $team->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));

    $this->_childCount = $team->getTotalItemCount();

    return $team;
  }

  public function tabLinks($active = false)
  {
    $page = Engine_Api::_()->core()->getSubject('page');

    $table = Engine_Api::_()->getDbtable('links', 'core');
    $select = $table->select()
      ->where('parent_type = ?', $page->getType())
      ->where('parent_id = ?', $page->page_id)
      ->where('search = ?', 1)
      ->order('creation_date DESC');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));

    if ($active) {
      $this->add($this->component()->itemList($paginator, 'pageLinksList'), 10);
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabAlbums($active = false)
  {
    $page = Engine_Api::_()->core()->getSubject('page');

    if( !in_array('pagealbum', $page->getAllowedFeatures()) ) {
      return false;
    }

    $select = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum')->getSelect(array('page_id' => $page->page_id));

    if ($active && $this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $form = $this->getSearchForm();
    $form->setMethod('get');
    $form->getElement('search')->setValue($this->_getParam('search'));

    if ($active) {
      $customizer = 'browseItemList';

      if ($page->isAdmin())
        $customizer = 'manageAlbumList';

      $this->add($this->component()->itemSearch($form), 9)
        ->add($this->component()->navigation('pagealbum', true), 8)
        ->add($this->component()->itemList($paginator, $customizer, array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))), 10)
//        ->add($this->component()->paginator($paginator), 20)
      ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabBlogs($active = false)
  {
    $page = Engine_Api::_()->core()->getSubject('page');

    if( !in_array('pageblog', $page->getAllowedFeatures()) ) {
      return false;
    }

    $select = Engine_Api::_()->getDbTable('pageblogs', 'pageblog')
      ->getSelect(array('page_id' => $page->page_id));
    if ($active && $this->_getParam('search', false)) {
      $select->where('title LIKE ? OR body LIKE ?', '%' . $this->_getParam('search') . '%');
    }
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    if ($active) {
      $customizer = 'browseItemList';

      if ($page->isAdmin())
        $customizer = 'manageBlogList';

      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));

      $this->add($this->component()->itemSearch($form), 9)
        ->add($this->component()->navigation('pageblog', true), 8)
        ->add($this->component()->itemList($paginator, $customizer, array('listPaginator' => true,)), 10)
//        ->add($this->component()->paginator($paginator), 20)
      ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabDiscussion($active = false)
  {
    $page = Engine_Api::_()->core()->getSubject('page');

    if( !in_array('pagediscussion', $page->getAllowedFeatures()) ) {
      return false;
    }

    $select = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion')->select()
      ->where('page_id = ?', $page->page_id)
      ->order('sticky DESC')
      ->order('modified_date DESC');

    if ($active && $this->_getParam('search', false)) {
      $select->where('title LIKE ? ', '%' . $this->_getParam('search') . '%');
    }
    $perPage = Engine_Api::_()->getApi('settings', 'core')
      ->getSetting('pagediscussion.perpage.list', 10);

    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage($perPage);

    if ($active) {
      $customizer = 'browseItemList';

      if ($page->isAdmin())
        $customizer = 'manageDiscussionList';

      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));

      $this->add($this->component()->itemSearch($form), 9)
        ->add($this->component()->navigation('pagediscussion', true), 8)
        ->add($this->component()->itemList($paginator, $customizer, array('listPaginator' => true,)), 10)
//        ->add($this->component()->paginator($paginator), 20)
      ;
    }

    return $paginator->getTotalItemCount();
  }

  public function tabContact($active = false)
  {
    $subject = Engine_Api::_()->core()->getSubject('page');
    $page_id = $subject->getIdentity();

    if( !in_array('pagecontact', $subject->getAllowedFeatures()) ) {
      return false;
    }

    if (!in_array('pagecontact', (array)$subject->getAllowedFeatures())) {
      return false;
    }

    $topicsTbl = Engine_Api::_()->getDbTable('topics', 'pagecontact');
    $topics = $topicsTbl->getTopics($page_id);

    $descriptionTbl = Engine_Api::_()->getDbTable('descriptions', 'pagecontact');
    $description = $descriptionTbl->getDescription($page_id);
    $form = new Pagecontact_Form_Contact($page_id);
    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'pagecontact', 'controller' => 'index', 'action' => 'send', 'page_id' => $page_id), 'default'));
    $form->loadDefaultDecorators();
    $form->setDescription($description);
    $form->getDecorator('Description')->setOption('escape', false);
    $form->getElement('send')->setAttrib('type', 'submit');

    if ($active)
      $this->add($this->component()->form($form), 10);

    return true;
  }

  public function tabFields($active = false)
  {
    $subject = Engine_Api::_()->core()->getSubject();

    $field = array(
      'content' => array()
    );
    $field['title'] = $this->view->translate("Page Details");

    if ($subject->getTitle()) {

      $field['content'][] = array(
        'label' => $this->view->translate("Title"),
        'value' => $subject->getTitle()
      );
      $subject->getTitle();
    }
    if ($subject->getDescription()) {
      $field['content'][] = array(
        'label' => $this->view->translate("Description"),
        'value' => $subject->getDescription(false, false, false)
      );
    }
    if ($subject->isAddress()) {
      $field['content'][] = array(
        'label' => $this->view->translate("Address"),
        'value' => $subject->getAddress()
      );
    }
    if ($subject->website) {
      $field['content'][] = array(
        'label' => $this->view->translate("Website"),
        'value' => $subject->getWebsite()
      );
    }
    if ($subject->phone) {

      $field['content'][] = array(
        'label' => $this->view->translate("Phone"),
        'value' => $subject->phone
      );
    }
    if ($active) {

      $fieldsValues = $this->component()->fieldsValues();

      if( $fieldsValues && !empty($fieldsValues['params']) ) {
        foreach( $fieldsValues['params'] as $params ) {
          foreach( $params as $param ) {
            $field['content'][] = $param;
          }
        }
      }

      $fieldsValues = array($field);

      $this->add($this->component()->customComponent('fieldsValues', $fieldsValues), 10);
    }
    return true;
  }

  public function tabEvent($active = false)
  {
    $page = Engine_Api::_()->core()->getSubject('page');

    if( !in_array('pageevent', $page->getAllowedFeatures()) ) {
      return false;
    }

    $pageevent_ids = $this->getPageeventIds($page->page_id);

    $selectEvent = Engine_Api::_()->getDbTable('pageevents', 'pageevent')->select()
      ->where('page_id = ?', $page->page_id);
    if (!empty($pageevent_ids)) {
      $selectEvent->where('pageevent_id IN(?)', $pageevent_ids);
    } else {
      $selectEvent->where('pageevent_id = 0');
    }
    $selectEvent->where('endtime > FROM_UNIXTIME(?)', time());

    if ($active && $this->_getParam('search', false)) {
      $selectEvent->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $selectEvent->order('starttime ASC');
    $paginator = Zend_Paginator::factory($selectEvent);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage'), 10);

    if ($active) {
      $customizer = 'browseItemList';

      if ($page->isAdmin())
        $customizer = 'manageEventList';

      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));

      $this->add($this->component()->itemSearch($form), 9)
        ->add($this->component()->navigation('pageevent', true), 8)
        ->add($this->component()->itemList($paginator, $customizer, array('listPaginator' => true,)), 10)
//        ->add($this->component()->paginator($paginator), 20)
      ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabFaq($active = false)
  {
    $page = Engine_Api::_()->core()->getSubject('page');

    if( !in_array('pagefaq', $page->getAllowedFeatures()) ) {
      return false;
    }

    $select = Engine_Api::_()->getDbTable('faqs', 'pagefaq')->select();
    $select->where('page_id = ?', $page->page_id);
    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));


    if ($active) {
      $faq = array();
      foreach ($paginator as $item) {
        $element = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'd'));
        $question = $this->dom()->new_('h3', array(), $item->question);
        $answer = $this->dom()->new_('p', array(), $item->answer);
        $element->append($question);
        $element->append($answer);
        $faq[] = $element;
      }

      $this
        ->add($this->component()->html($faq), 20);
    }

    return $paginator->getTotalItemCount();
  }

  public function tabMusic($active = false)
  {
    $page = Engine_Api::_()->core()->getSubject('page');

    if( !in_array('pagemusic', $page->getAllowedFeatures()) ) {
      return false;
    }

    $select = Engine_Api::_()->getDbTable('playlists', 'pagemusic')->getSelect(array('page_id' => $page->page_id));
    if ($active && $this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 10));

    if ($active) {
      $customizer = 'browseItemList';

      if ($page->isAdmin())
        $customizer = 'manageMusictList';

      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));

      $this->add($this->component()->itemSearch($form), 9)
        ->add($this->component()->navigation('pagemusic', true), 8)
        ->add($this->component()->itemList($paginator, $customizer, array('listPaginator' => true,)), 10)
//        ->add($this->component()->paginator($paginator), 20)
      ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabVideo($active = false)
  {
    $page = Engine_Api::_()->core()->getSubject('page');

    if( !in_array('pagevideo', $page->getAllowedFeatures()) ) {
      return false;
    }

    $select = Engine_Api::_()->getDbTable('pagevideos', 'pagevideo')->getSelect(array('page_id' => $page->page_id));
    if ($active && $this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    if ($active) {
      $customizer = 'browseItemList';

      if ($page->isAdmin())
        $customizer = 'manageVideoList';

      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));

      $this->add($this->component()->itemSearch($form), 9)
        ->add($this->component()->navigation('pagevideo', true), 8)
        ->add($this->component()->itemList($paginator, $customizer, array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))), 10)
//        ->add($this->component()->paginator($paginator), 20)
      ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabStore($active = false)
  {
    $page = Engine_Api::_()->core()->getSubject('page');

    if( !in_array('store', $page->getAllowedFeatures()) ) {
      return false;
    }

    $select = Engine_Api::_()->getDbTable('products', 'store')->getSelect(array(
      'page_id' => $page->page_id,
      'order' => 'DESC',
      'owner' => $page->getStorePrivacy(),
      'quantity' => true
    ));
    if ($active && $this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    if ($active) {
      $isGatewayEnabled = (Engine_Api::_()->getDbTable('apis', 'store')->getEnabledGatewayCount( $page->getIdentity() ) <= 0) ? false : true;

      if ($page->getStorePrivacy() && !$isGatewayEnabled) {
        $this->add($this->component()->html(
          $this->view->translate('There are currently no ' .
            'enabled payment gateways. You must %1$sadd one%2$s before this ' .
            'page is available.', '<a href="' .
            $this->view->escape($this->view->url(array('action' => 'gateway', 'page_id' => $this->view->subject()->getIdentity()), 'store_settings', true)) .
            '">', '</a>')
        ));
      }

      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));

      $this->add($this->component()->itemSearch($form), 9)
        ->add($this->component()->itemList($paginator, 'browseStoreList', array('listPaginator' => true,)), 10)
//        ->add($this->component()->paginator($paginator), 20)
      ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabReviews($active = false)
  {
      if(!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('rate'))
          return false;
    $page = Engine_Api::_()->core()->getSubject('page');

    if( !in_array('rate', $page->getAllowedFeatures()) ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
    $reviewTbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');
    $select = $reviewTbl->select()
      ->setIntegrityCheck(false)
      ->from(
      array('r' => $reviewTbl->info('name')),
      new Zend_Db_Expr('r.*, IF(r.user_id=' . (int)$viewer->getIdentity() . ',1,0) AS `is_owner`'))
      ->joinLeft(array('v' => $tbl_vote->info('name')), 'v.review_id = r.pagereview_id', 'AVG(v.rating) AS rating')
      ->where('r.page_id = ?', $page->page_id)
      ->group('r.pagereview_id')
      ->order('is_owner DESC')
      ->order('r.creation_date DESC');
    if ($active && $this->_getParam('search', false)) {
      $select->where('title LIKE ? OR body LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    if ($active) {
      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));

      $this->add($this->component()->itemSearch($form), 9)
        ->add($this->component()->itemList($paginator, 'manageReviewList'), 10);

      $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');
      if ($tbl->isAllowedPost($page->page_id, $viewer)) {
        $this->add($this->component()->navigation('review_create', true), 8);
      }
    }

    if( $paginator->count() )
      return $paginator->getTotalItemCount();
    return true;
  }

  public function tabMap($active = false)
  {
    $subject = Engine_Api::_()->core()->getSubject('page');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return false;
    }

    $page_id = $subject->getIdentity();

    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $select = $table
      ->select()->setIntegrityCheck(false)
      ->from(array('page' => 'engine4_page_pages'))
      ->joinLeft(array('marker' => 'engine4_page_markers'), 'marker.page_id = page.page_id', array('marker_id', 'latitude', 'longitude'))
      ->where('page.page_id = ?', $page_id);

    $page = $table->fetchRow($select);

    $markers = array();
    $markers['markers'] = array();

    if (!$page->marker_id) {
      return false;
    }

    if ($page->marker_id > 0) {
      $markers['markers'][] = array(
        //'marker_id' => $page->marker_id,
        'lat' => $page->latitude,
        'lng' => $page->longitude,
        //'pages_id' => $page->page_id,
        //'pages_photo' => $page->getPhotoUrl('thumb.normal'),
        //'title' => $page->getTitle(),
        //'desc' => Engine_String::substr($page->getDescription(),0,200),
        //'url' => $page->getHref()
      );

      $markers['bounds'] = Engine_Api::_()->getApi('gmap', 'page')->getMapBounds($markers['markers']);
    }

    if( $active ) {
      $this->add($this->component()->map($markers), 10);
    }

    return true;
  }

  public function tabOffers($active = false)
  {
    $subject = Engine_Api::_()->core()->getSubject('page');

    if( !in_array('offers', $subject->getAllowedFeatures()) ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!($subject instanceof Page_Model_Page)) {
      return false;
    }

    $params = array(
      'page_id' => $subject->getIdentity(),
      'filter' => 'upcoming',
      'page_num' => $this->_getParam('page', 1)
    );

    if ( $active && $this->_getParam('search', false)) {
      $params['searchText'] = $this->_getParam('search');
    }

    $tbl = Engine_Api::_()->getDbTable('offers', 'offers');
    $paginator = $tbl->getOffersPaginator($params);

    if( $active ) {
      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));

      $this->add($this->component()->navigation('offer_profile_page', true), 8)
        ->add($this->component()->itemSearch($form), 9)
        ->add($this->component()->itemList($paginator, 'browseOffersList', array('listPaginator' => true,)), 10)
//        ->add($this->component()->paginator($paginator), 20)
      ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabDonation($active = false)
  {
    $page = Engine_Api::_()->core()->getSubject('page');
    if( !in_array('donation', $page->getAllowedFeatures()) ) {
      return false;
    }
    if( $active ) {
      $settings = Engine_Api::_()->getDbTable('settings', 'core');
      $subject = Engine_Api::_()->core()->getSubject('page');
      $viewer = Engine_Api::_()->user()->getViewer();
      $currency = $settings->getSetting('payment.currency', 'USD');
      $itemCountPerPage = $this->_getParam('itemCountPerPage', 10);

      if(!$subject->approved){
        return false;
      }

      if ( !($subject instanceof Page_Model_Page) ){
        return false;
      }

      if (!$subject->authorization()->isAllowed($viewer, 'view')) {
        return false;
      }

      if(!($subject->isDonation() || $subject->isOwner($viewer)) || !$subject->isAllowDonation()){
        return false;
      }

      $p = 1;
      $type = $this->_request->getParam('type', 'charity');
      if( $type != 'charity' && $type != 'project') {
        return false;
      }

      $count_params = array(
        'page_id' => $subject->getIdentity(),
        'status' => 'active',
        'approved' => 1,
      );

      if(!$settings->getSetting('donation.enable.charities',1)){
        $type = 'project';
        $count_params['type'] = $type;
      } elseif(!$settings->getSetting('donation.enable.projects',1)){
        $type = 'charity';
        $count_params['type'] = $type;
      }

      $navigation = Engine_Api::_()->getApi('menus', 'apptouch')->getNavigation('donation_page');

      $table = Engine_Api::_()->getDbTable('donations','donation');
      $donations = $table->getDonationsPaginator(array(
        'page_id' => $subject->getIdentity(),
        'ipp' => $itemCountPerPage,
        'page' => $p,
        'type' => $type,
        'status' => 'active',
        'approved' => 1,
      ));

      if($type == 'charity')
        $function = 'BrowseCharityList';
      else
        $function = 'BrowseProjectList';

      $this->add($this->component()->navigation($navigation), 10);

      $this->add($this->component()->itemList($donations, $function), 11);
    }

    return true;
  }

  public function browseItemList(Core_Model_Item_Abstract $item)
  {
    $photo_type = 'thumb.normal';
    if(Engine_Api::_()->apptouch()->isTabletMode() && ($this->_getParam('tab', false) == 'albums' || $this->_getParam('tab', false) == 'video')) {
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
      'photo' => $photoUrl
    );

    return $customize_fields;
  }

  public function browseStoreList(Core_Model_Item_Abstract $item)
  {
    $customize_fields = array(
      'title' => $item->getTitle(),
      'creation_date' => null,
      'descriptions' => array($this->view->translate(
        array('%s item available', '%s items available', (int)$item->getQuantity()),
        $this->view->locale()->toNumber($item->getQuantity()))),
      'counter' => $this->view->getPrice($item)
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageAlbum Customizer Functions ---------------------------------
  public function manageAlbumList(Core_Model_Item_Abstract $item)
  {
    $photo_type = 'thumb.normal';
    if(Engine_Api::_()->apptouch()->isTabletMode()) {
      $photo_type = 'thumb.profile';
    }

    $page_id = $item->getPage()->getIdentity();
    $options = array();

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
      'title' => $item->getTitle(),
      'descriptions' => array(
        $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date) . ' ' . $this->view->translate('By') . ' ' . $item->getOwner()->getTitle()
      ),
      'photo' => $photoUrl,
      'manage' => $options,
      'creation_date' => null,
      'counter' => strtoupper($this->view->translate(array('%s photo', '%s photos', $item->count()), $this->view->locale()->toNumber($item->count()))),
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageAlbum Customizer Functions ---------------------------------

  //=------------------------------------------ PageBlog Customizer Functions ---------------------------------
  public function manageBlogList(Core_Model_Item_Abstract $item)
  {
    $options = array();
    $page_id = $item->getPage()->getIdentity();

    $options[] = array(
      'label' => $this->view->translate('Edit Entry'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'edit', 'page_id' => $page_id, 'blog_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_blog', true),
        'class' => 'buttonlink icon_album_edit'
      )
    );

    $options[] = array(
      'label' => $this->view->translate('Delete Blog'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'delete', 'page_id' => $page_id, 'blog_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_blog', true),
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
      'manage' => $options
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageBlog Customizer Functions ---------------------------------

  //=------------------------------------------ PageDiscussion Customizer Functions -------------------------------
  public function manageDiscussionList(Core_Model_Item_Abstract $item)
  {
    $options = array();
    $page_id = $item->getParentPage()->getIdentity();

    $options[] = array(
      'label' => $this->view->translate('Rename'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'rename', 'page_id' => $page_id, 'discussion_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
        'class' => 'buttonlink'
      )
    );

    $options[] = array(
      'label' => $this->view->translate('Delete'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'delete', 'page_id' => $page_id, 'discussion_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
        'class' => 'buttonlink smoothbox',
      )
    );

    $options[] = array(
      'label' => $this->view->translate($item->closed ? 'Open' : 'Close'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'closed', 'page_id' => $page_id, 'discussion_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
        'class' => 'buttonlink',
      )
    );


    $options[] = array(
      'label' => $this->view->translate($item->sticky ? 'Remove Sticky' : 'Make Sticky'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'sticky', 'page_id' => $page_id, 'discussion_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_discussion', true),
        'class' => 'buttonlink',
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
      'manage' => $options
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageDiscussion Customizer Functions -------------------------------

  //=------------------------------------------ PageDocument Customizer Functions ---------------------------------
  public function manageDocumentList(Core_Model_Item_Abstract $item)
  {
    $options = array();
    $page_id = $item->getPage()->getIdentity();

    $options[] = array(
      'label' => $this->view->translate('Edit'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'edit', 'page_id' => $page_id, 'document_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_document', true),
        'class' => 'buttonlink icon_album_edit'
      )
    );

    $options[] = array(
      'label' => $this->view->translate('Delete'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'delete', 'page_id' => $page_id, 'document_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_document', true),
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
      'manage' => $options
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageDocument Customizer Functions ---------------------------------

  //=------------------------------------------ PageEvent Customizer Functions ---------------------------------
  public function manageEventList(Core_Model_Item_Abstract $item)
  {
    $options = array();

    $options[] = array(
      'label' => $this->view->translate('Edit'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'edit', 'page_id' => $item->getPage()->getIdentity(), 'event_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_event', true),
        'class' => 'buttonlink icon_album_edit'
      )
    );

    $options[] = array(
      'label' => $this->view->translate('Delete'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'delete', 'page_id' => $item->getPage()->getIdentity(), 'event_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_event', true),
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
      'manage' => $options
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageEvent Customizer Functions ---------------------------------

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
      'manage' => $options
    );

    return $customize_fields;
  }

  //=------------------------------------------ PageVideo Customizer Functions ---------------------------------

  //=------------------------------------------ Review Customizer Functions ---------------------------------
  public function manageReviewList(Core_Model_Item_Abstract $item)
  {
    $options = array();
    $page_id = $item->getPage()->getIdentity();
    $viewer = Engine_Api::_()->user()->getViewer();

    $isAllowedRemove = Engine_Api::_()->getApi('core', 'rate')
      ->isAllowRemoveReview($page_id, $viewer);

    if ($item->isOwner($viewer)) {
      $options[] = array(
        'label' => $this->view->translate('Edit'),
        'attrs' => array(
          'href' => $this->view->url(array('action' => 'edit', 'page_id' => $page_id, 'pagereview_id' => $item->getIdentity()), 'page_review'),
          'class' => 'buttonlink'
        )
      );
    }

    if ($isAllowedRemove) {
      $options[] = array(
        'label' => $this->view->translate('Delete'),
        'attrs' => array(
          'href' => $this->view->url(array('action' => 'remove', 'page_id' => $page_id, 'pagereview_id' => $item->getIdentity()), 'page_review'),
          'class' => 'buttonlink',
        )
      );
    }

    if (isset($item->photo_id)) {
      $photoUrl = $item->getPhotoUrl('thumb.normal');
    } else {
      $photoUrl = $item->getOwner()->getPhotoUrl('thumb.normal');
    }

    $customize_fields = array(
      'title' => $item->getTitle(),
      'descriptions' => array(
        '<div class="small_rate_star">' . $this->view->reviewRate($item->rating, true) . '</div>'
      ),
      'photo' => $photoUrl,
      'manage' => $options,
      'counter' => round($item->rating, 2)
    );

    return $customize_fields;
  }

//=------------------------------------------ Review Customizer Functions ---------------------------------

//=------------------------------------------ Links Customizer Functions ---------------------------------
  public function pageLinksList(Core_Model_Item_Abstract $item)
  {
    $options = array();

    if ($item->isDeletable()) {
      $options[] = array(
        'label' => $this->view->translate('delete link'),
        'attrs' => array(
          'href' => $this->view->url(array('module' => 'core', 'controller' => 'link', 'action' => 'delete', 'link_id' => $item->link_id,), 'default'),
          'class' => 'buttonlink'
        )
      );
    }

    $customize_fields = array(
      'manage' => $options,
    );

    return $customize_fields;
  }

//=------------------------------------------ Links Customizer Functions ---------------------------------

//=------------------------------------------ Offers Customizer Functions ---------------------------------
  public function browseOffersList(Core_Model_Item_Abstract $item)
  {
    if( Engine_Api::_()->offers()->availableOffer($item, true) != 'Unlimit' ) {
      $desc = $this->view->translate('OFFERS_offer_time_left') .  Engine_Api::_()->offers()->availableOffer($item, true);
    } else {
      if(!$item->coupons_unlimit) {
        $desc = $this->view->translate('OFFERS_offer_available') . ' ' . $this->view->translate('%s coupons', $item->coupons_count);
      }
    }
    $customize_fields = array(
      'counter' => $this->view->translate('OFFERS_offer_discount') . '' . $item->discount.''. $this->view->translate($item->discount_type),
      'descriptions' => array($desc)
    );

    return $customize_fields;
  }
  //=------------------------------------------ Offers Customizer Functions ---------------------------------

  public function getPageeventIds($page_id)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $db = Engine_Db_Table::getDefaultAdapter();

    $selectPageevent = $db->select()->from(array('pe' => 'engine4_page_events'));
    $selectPageevent = $selectPageevent->joinInner(array('aa' => 'engine4_authorization_allow'), 'aa.resource_id = pe.pageevent_id
                                       AND aa.resource_type = \'pageevent\' AND aa.action = \'view\'');
    if ($viewer->getIdentity()) {
      $selectPageevent = $selectPageevent->joinLeft(array('pl' => 'engine4_page_lists'), 'pl.list_id = aa.role_id');
      $selectPageevent = $selectPageevent->joinLeft(array('pli' => 'engine4_page_listitems'), 'pl.list_id = pli.list_id
                                          AND pli.child_id = ' . $viewer->getIdentity());
      $selectPageevent = $selectPageevent->where('pe.page_id = (?) AND (aa.role IN (\'everyone\', \'registered\') OR pli.child_id IS NOT NULL)', $page_id);
      $selectPageevent = $selectPageevent->group('pe.pageevent_id');
    }
    else {
      $selectPageevent = $selectPageevent->where('pe.page_id = (?)', $page_id);
      $selectPageevent = $selectPageevent->where('aa.role IN (\'everyone\')');
      $selectPageevent = $selectPageevent->group('pe.pageevent_id');
    }
    return $db->fetchCol($selectPageevent);
  }

  public function BrowseCharityList(Core_Model_Item_Abstract $item)
  {
    $customize_fields = array(
      'title' => $this->view->string()->chunk($this->view->string()->truncate($item->getTitle(), 25), 10),
      'descriptions' => array(
        $this->view->translate('DONATION_Raised:') . ' ' . $this->view->locale()->toCurrency((double)$item->raised_sum, $this->view->currency),
//        '<br><button class="btn btn-small" onclick="$.mobile.changePage(\'' . $this->view->url(array('object' => $item->getType(),'object_id' => $item->getIdentity()),'donation_donate',true) . '\')">' . $this->view->translate('DONATION_Donate') . '</button>'
      ),
    );
    return $customize_fields;
  }
  public function BrowseProjectList(Core_Model_Item_Abstract $item)
  {
    $customize_fields = array(
      'title' => $this->view->string()->chunk($this->view->string()->truncate($item->getTitle(), 25), 10),
      'descriptions' => array(),
    );
    $descriptions = $this->view->translate('DONATION_Raised:') . ' ' . $this->view->locale()->toCurrency((double)$item->raised_sum, $this->view->currency) .
      '<br>' . $this->view->translate('DONATION_Target:') . ' ' . $this->view->locale()->toCurrency((double)$item->target_sum, $this->view->currency);
    if (strtotime($item->expiry_date)) {
      $descriptions .= '<br>'.$this->view->translate('DONATION_Limited:');

      $left = Engine_Api::_()->getApi('core', 'donation')->datediff(new DateTime($item->expiry_date), new DateTime(date("Y-m-d H:i:s")));
      $month = (int)$left->format('%m');
      $day = (int)$left->format('%d');
      if($month > 0) {
        $descriptions .= ' ' . $this->view->translate(array("%s month", "%s months", $month), $month);
      }
      $descriptions .= ' ' .  $this->view->translate(array("%s day left", "%s days left", $day), $day);
    }
    $customize_fields['descriptions'][] = $descriptions;
//
//    $customize_fields['descriptions'][] = '<br><button class="btn btn-small" onclick="$.mobile.changePage(\'' . $this->view->url(array('object' => $item->getType(),'object_id' => $item->getIdentity()),'donation_donate',true) . '\')">' . $this->view->translate('DONATION_Donate') . '</button>';

    return $customize_fields;
  }
}
