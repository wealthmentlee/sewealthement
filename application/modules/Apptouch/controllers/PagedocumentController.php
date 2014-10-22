<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 14.08.12
 * Time: 9:58
 * To change this template use File | Settings | File Templates.
 */

class Apptouch_PagedocumentController extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->page_id = $page_id = $this->_getParam('page_id');
    $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!$page_id) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this->pageObject = $page = Engine_Api::_()->getItem('page', $this->page_id);

    if (!$page->getIdentity()) {
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
    $this->document_id = $document_id = $this->_getParam('document_id');

    $this->params = array('page_id' => $this->page_id, 'ipp' => 10, 'p' => $this->_getParam('p', 1), 'category_id' => $this->_getParam('category_id', -1));

    $this->scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->pagedocument_api_key;
    $this->scribd_secret = Engine_Api::_()->getApi('settings', 'core')->pagedocument_secret_key;

    if (empty($this->scribd_api_key) || empty($this->scribd_secret)) {
      $this->isAllowedPost = false;
    }

    $this->scribd = Engine_Api::_()->loadClass('Pagedocument_Plugin_Scribd');
    $this->scribd->setParams($this->scribd_api_key, $this->scribd_secret);

    $this->isCreationAllowed = true;

  }

  public function indexIndexAction()
  {
    if (!$this->isAllowedView) {
      $this->view->error = 1;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
      return;
    }

    $table = $this->getTable();

    $paginator = $table->getPaginator($this->params);

    if ($this->page_id)
      Engine_Api::_()->core()->setSubject(Engine_Api::_()->getItem('page', $this->page_id));

    $this->add($this->component()->navigation('pagedocument', true));

    if ($this->isAllowedPost)
      $this->add($this->component()->quickLinks('pagedocument_quick', true));

    $this->add($this->component()->itemList($paginator, 'browseItemList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function indexMineAction()
  {
    if (!$this->isAllowedView) {
      $this->view->error = 1;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
      return;
    }

    $table = $this->getTable();

    $this->params['user_id'] = $this->viewer->getIdentity();
    $paginator = $table->getPaginator($this->params);

    if ($this->page_id)
      Engine_Api::_()->core()->setSubject(Engine_Api::_()->getItem('page', $this->page_id));

    $this->add($this->component()->navigation('pagedocument', true));

    if ($this->isAllowedPost)
      $this->add($this->component()->quickLinks('pagedocument_quick', true));

    $this->add($this->component()->itemList($paginator, 'manageDocumentList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function indexEditAction()
  {
    $document = Engine_Api::_()->getItem('pagedocument', $this->_getParam('document_id'));
    $user = $document->getOwner();
    $page = $document->getPage();

    $form = new Pagedocument_Form_Edit();
    $form->document_title->setValue($document->document_title);
    $form->document_description->setValue($document->document_description);
    $form->category_id->setValue($document->category_id);

    $tags = $document->tags()->getTagMaps();
    $tagString = '';
    foreach ($tags as $tagmap) {
      if ($tagString !== '') $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }
    $form->document_tags->setValue($tagString);

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($values = $this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $document->document_title = $values['document_title'];
    $document->document_description = $values['document_description'];
    $document->category_id = $values['category_id'];
    $tags = preg_split('/[,]+/', $values['document_tags']);
    if ($tags) {
      $document->tags()->setTagMaps($user, $tags);
    }

    $document->save();

    return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'document'), 'page_view'));
  }

  public function indexCreateAction()
  {
    if (!$this->isAllowedPost)
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));

    $form = new Pagedocument_Form_Create();
    $form->removeElement('file');
    $form->addElement('File', 'file', array(
      'label' => 'Document File',
      'destination' => APPLICATION_PATH . '/public/temporary/',
      'order' => 4
    ));

    if (!$this->getRequest()->isPost() || !$form->isValid($values = $this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $table = $this->getTable();

    try {
      $document = $table->createRow();
      $document->user_id = $this->viewer->getIdentity();
      $document->category_id = $values['category_id'];
      $document->page_id = $this->page->getIdentity();
      $document->document_title = $values['document_title'];
      $document->document_description = $values['document_description'];
      $document->download_allow = $values['download_allow'];
      $document->secure_allow = $values['secure_allow'];
      $document->save();
      $tags = preg_split('/[,]+/', $values['tags']);
      if ($tags) {
        $document->tags()->setTagMaps($this->viewer, $tags);
      }

      $document->document_tag = $tags;

      $files = $this->getPicupFiles('file');

      if (!empty($values['file'])) {

      } elseif ($files) {

      }

    } catch (Exception $e) {
      throw $e;
    }
  }

  public function indexDeleteAction()
  {
    $document = Engine_Api::_()->getItem('pagedocument', $this->_getParam('document_id'));
    $page = $document->getPage();

    $form = new Engine_Form();
    $form->setTitle('pagedocument_Delete Document')
      ->setDescription('pagedocument_Delete_confirmation');
    $form->addElement('Button', 'submit', array(
      'label' => 'Confirm',
      'type' => 'submit',
    ));
    $form->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
    ));

    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $form->getDisplayGroup('buttons');

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!empty($document->doc_id)) {
      $this->scribd->my_user_id = $document->user_id;
      $this->scribd->delete($document->doc_id, $document->user_id);
    }

    $document->delete();

    return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'document'), 'page_view'), Zend_Registry::get('Zend_Translate')->_("pagedocument_Document deleted"), true);
  }

  protected function getTable()
  {
    return Engine_Api::_()->getDbTable('pagedocuments', 'pagedocument');
  }

  protected function getApi()
  {
    return Engine_Api::_()->getApi('core', 'pagedocument');
  }

  protected function getPageApi()
  {
    return Engine_Api::_()->getApi('core', 'page');
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
      'photo' => $photoUrl
    );

    return $customize_fields;
  }

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
}