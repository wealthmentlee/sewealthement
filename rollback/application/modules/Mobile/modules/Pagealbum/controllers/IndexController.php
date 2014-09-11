<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Pagealbum_IndexController extends Core_Controller_Action_Standard
{
  private $_subject;

  public function init()
  {
    $page_id = (int)$this->_getParam('page_id');
    $subject = null;
    $navigation = new Zend_Navigation();

    if ($page_id){
      $subject = Engine_Api::_()->getDbTable('pages', 'page')->findRow($page_id);
    }

    if ($subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)){
      $subject = null;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if ($subject){

      Engine_Api::_()->core()->setSubject($subject);

      $navigation->addPage(array(
        'label' => 'Browse Albums',
        'route' => 'page_album',
        'action' => 'index',
        'params' => array(
          'page_id' => $subject->getIdentity()
        )
      ));

      if ($subject->authorization()->isAllowed($viewer, 'posting')){

        $navigation->addPage(array(
          'label' => 'Manage Albums',
          'route' => 'page_album',
          'action' => 'manage',
          'params' => array(
            'page_id' => $subject->getIdentity()
          )
        ));

      }

    }

    $this->_subject = $this->view->subject = $subject;
    $this->view->navigation = $navigation;

  }

  public function indexAction()
  {
    if (!$this->_subject){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $params = array(
      'page_id' => $this->_subject->getIdentity(),
      'ipp' => 10,
      'p' => $this->_getParam('page')
    );

    $this->view->paginator = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum')->getAlbums($params);

  }

  public function manageAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->_subject || !$viewer->getIdentity()){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $params = array(
      'page_id' => $this->_subject->getIdentity(),
      'ipp' => 10,
      'p' => $this->_getParam('page'),
      'user_id' => $viewer->getIdentity());

    $this->view->paginator = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum')->getAlbums($params);

  }

  public function viewAction()
  {
    $album_id = (int)$this->_getParam('album_id');
    $album = null;

    $table = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');

    if ($album_id){
      $album = $table->findRow($album_id);
    }

    if (!$album){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $this->view->album = $album;
    $this->view->subject = $album->getParent();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

    $paginator->setCurrentPageNumber($this->_getParam('page'));

    Engine_Api::_()->core()->setSubject($album);

  }

  public function viewPhotoAction()
  {
    $photo_id = (int)$this->_getParam('photo_id');
    $photo = null;

    if ($photo_id){
      $photo = Engine_Api::_()->getDbTable('pagealbumphotos', 'pagealbum')->findRow($photo_id);
    }

    if (!$photo){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $this->view->photo = $photo;
    $this->view->album = $photo->getParent();
    $this->view->subject = $this->view->album->getParent();

  }

  public function deleteAction()
  {
    if (!$this->_subject || !Engine_Api::_()->getApi('core', 'page')->isAllowedPost($this->_subject)){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $page_id = $this->_subject->getIdentity();
    $album = (int)$this->_getParam('album');

    $this->view->form = $form = new Engine_Form;

    $form->setTitle('Delete Album')
      ->setDescription('Are you sure you want to delete this album?')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');

    $form->addElement('Button', 'submit', array(
      'label' => 'Delete Event',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $form->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => urldecode($this->_getParam('return_url')),
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');

    $form->setAction($this->view->url(array(
      'action' => 'delete',
      'page_id' => $page_id,
      'album' => $album,
      'return_url' => $this->_getParam('return_url')
    ), 'page_album'));

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    $table = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');

    $album = $table->fetchRow($table->select()
      ->where("page_id = {$page_id}")
      ->where("pagealbum_id = {$album}"));

    if (!$album){
      return ;
    }

    $select = $album->getCollectiblesSelect();
    $photo_id = Engine_Api::_()->getDbTable('pagealbumphotos', 'pagealbum')->getAdapter()->fetchOne($select);

    $db = $table->getAdapter();
    $db->beginTransaction();

    try{
      if (!empty($photo_id)){
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
    catch (Exception $e){
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Album was deleted.')),
      'return_url'=>urldecode($this->_getParam('return_url')),
    ));

  }



}