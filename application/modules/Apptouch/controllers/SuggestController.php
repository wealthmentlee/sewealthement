<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 24.07.12
 * Time: 11:31
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_SuggestController
  extends Apptouch_Controller_Action_Bridge
{
  public function handlerRequestAction()
  {
    $this->view->notification = $notification = $this->_getParam('notification');
    $this->view->suggest = $notification->getObject();
  }

  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if( !$this->_helper->requireUser()->isValid() ) return;
  }

  public function indexIndexAction()
  {
    $suggest_id = $this->_getParam('suggest_id', 0);
    $table = Engine_Api::_()->getDbTable('suggests', 'suggest');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if ($suggest_id) {
      $suggest = $table->fetchRow($table->select()->where('suggest_id = ?', $suggest_id));
      if ($suggest && $suggest->to_id == $viewer->getIdentity()) {
        $suggest->delete();
      }
    }

    $api = Engine_Api::_()->getApi('core', 'suggest');
    $params = array('to_id' => $viewer->getIdentity());
    $this->view->paginator = $api->getAllSuggests($params);
  }

  public function indexSuggestAction()
  {
    $uids = $this->_getParam('uids', array());

    if (empty($uids)) {
      $this->view->type = 'error';
      $this->view->message = $this->view->translate('suggest_Choose friends from list.');
      return ;
    }

    $object = array(
      'type' => $this->_getParam('object_type', ''),
      'id' => (int)$this->_getParam('object_id', 0)
    );

    $suggest_type = $this->_getParam('suggest_type', '');
    $api = Engine_Api::_()->getApi('core', 'suggest');
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($api->isAllowed($suggest_type)) {
      if ($api->suggest($viewer, $uids, $object)) {
        $this->view->status = true;
        $this->view->message = $this->view->translate('suggest_Your suggestions were successfully sent.');
      } else {
        $this->view->error = true;
        $this->view->message = $this->view->translate('suggest_Your suggestions were not sent.');
      }
    } else {
      $this->view->error = true;
      $this->view->message = $this->view->translate('suggest_Suggestion of this type was turned off by site admin.');
    }
    $this->redirect('refresh');
  }

  public function indexProfilePhotoAction()
  {
    $user = Engine_Api::_()->user()->getViewer();

    // Get photo
    $photo_id = $this->_getParam('photo_id');
    $photo = Engine_Api::_()->getItem('suggest_profile_photo', $photo_id);

    // Make form
    $this->view->form = $form = new User_Form_Edit_ExternalPhoto();
    $this->view->photo = $photo;

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process

    $db = $user->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {

      // if it is from your own profile album do not make copies of the image
      $user->photo_id = $photo->file_id;
      $user->save();

      $newStorageFile = Engine_Api::_()->getItem('storage_file', $user->photo_id);

      // else copy the photo into your own collection
      $user->setPhoto($photo);
      $newStorageFile = Engine_Api::_()->getItem('storage_file', $user->photo_id);

      // Insert activity
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update',
        '{item:$subject} added a new profile photo.', array('is_mobile' => true));

      // Hooks to enable albums to work
      $event = Engine_Hooks_Dispatcher::_()
        ->callEvent('onUserProfilePhotoUpload', array(
            'user' => $user,
            'file' => $newStorageFile,
          ));

      $attachment = $event->getResponse();
      if( !$attachment ) $attachment = $newStorageFile;

      // We have to attach the user himself w/o album plugin
      Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);

      $object_type = 'suggest_profile_photo';
      $object_id = $this->_getParam('photo_id', 0);
      $to_id = Engine_Api::_()->user()->getViewer()->getIdentity();

      $suggests = Engine_Api::_()->suggest()->getAllSuggests(array(
          'object_type' => $object_type,
          'object_id' => $object_id,
          'to_id' => $to_id
        ), false);

      foreach ($suggests as $suggest) {
        $suggest->delete();
      }

      $photo->delete();
      $db->commit();

      return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Set as profile photo'));
    }

    // Otherwise it's probably a problem with the database or the storage system (just throw it)
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }

  public function indexContactsAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $object_id = $this->_getParam('object_id', 0);

    $this->view->params = $params = array('object_id' => $object_id);
    $this->view->checkedItems = array();

    $this->view->callback = $callback = $this->_getParam('c');
    $this->view->title = $title = $this->_getParam('t', '');
    $this->view->module = $module = $this->_getParam('m');
    $this->view->list = $list = $this->_getParam('l');
    $this->view->not_logged_in = $not_logged_in = $this->_getParam('nli', 0);
    $this->view->p = $p = (int)$this->_getParam('p', 1);
    $this->view->contacts = $contacts = (array)$this->_getParam('contacts', array());
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->disabled_label = !empty($this->view->params['disabled_label']) ? $this->view->params['disabled_label'] : "";
    $this->view->ipp = $ipp = (int)$this->_getParam('ipp', 30);

    $this->view->items = $items = Engine_Api::_()->suggest()->getNotMutualFriends($params);
    $isFriended = (int)empty($items);

    if (isset($items['all']) && $items['potential']) {
      $all = $items['all'];
      $potential = $items['potential'];
      $isFriended *= (int)($all->getTotalItemCount()+$potential->getTotalItemCount());
    }

    if ($isFriended) {
      $table = Engine_Api::_()->getDbTable('rejected', 'suggest');
      $table->insert(array(
        'user_id' => $user_id,
        'object_type' => 'friend',
        'object_id' => $object_id,
        'date' => date('Y-m-d H:i:s')
      ));

      $this->redirect('parentRefresh', Zend_Registry::get('Zend_Translate')->_('All of your friends are allready friends of this friend.'));
    }
  }

  public function indexRejectAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $object_type = $this->_getParam('object_type', '');
    $object_id = $this->_getParam('object_id', 0);
    $except = $this->_getParam('except', array());
    $wid = $this->_getParam('wid', array());

    if (!$user_id || !$object_type || !$object_id) {
      return ;
    }

    $table = Engine_Api::_()->getDbTable('rejected', 'suggest');
    $table->insert(array(
      'user_id' => $user_id,
      'object_type' => $object_type,
      'object_id' => $object_id,
      'date' => date('Y-m-d H:i:s')
    ));

    Engine_Api::_()->suggest()->flushCache($user_id, $object_type);
    $tpl = 'widget/item.tpl';
    $recs = Engine_Api::_()->suggest()->getRecommendations($user_id, $object_type, $except);

    if ($object_type == 'album_photo') {
      $tpl = 'widget/photo.tpl';
    } elseif ($object_type == 'friend') {
      $tpl = 'widget/friend/item.tpl';
    } elseif ($object_type == 'profile_photo_suggest') {
      $tpl = 'widget/profile/item.tpl';
    }

    $this->view->noRec = 0;
    if ($object_type == 'friend' || $object_type == 'profile_photo_suggest') {
      if (!empty($recs)) {
        $rec = $recs[0];
      }
    } else {
      $recTable = Engine_Api::_()->getDbTable('recommendations', 'suggest');
      $select = $recTable->select()
        ->where('object_type = ?', $object_type)
        ->where('object_id = ?', $object_id);

      $row = $recTable->fetchRow($select);
      if ($row) {
        if (count($recs['admin']) > 0) {
          $rec = $recs['admin'][0];
        }
      } else {
        if (count($recs['user']) > 0) {
          $rec = $recs['user'][0];
        }
      }
    }

    if (!isset($rec)) {
      $this->view->noRec = 1;
    } else {
      $this->view->item = $rec;
      $this->view->object_type = $object_type;
      $this->view->object_id = $rec->getIdentity();
      $this->view->wid = $wid;
      $this->view->html = $this->view->render($tpl);
    }
  }

  public function indexViewAction()
  {
    $suggest_id = $this->_getParam('suggest_id', 0);

  if ($suggest_id == 0) {
    return $this->_forward('index');
  }
    $table = Engine_Api::_()->getDbTable('suggests', 'suggest');
    $this->view->suggest = $suggest = $table->find($suggest_id)->current();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (!$suggest || !$suggest->getIdentity() || $suggest->to_id != $viewer->getIdentity()) {
      return $this->_forward('index');
    }

    $this->view->object = $object = $suggest->getObject();
    $this->view->from = $suggest->getFrom();

    if (!Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject($object);
    }
  }

  public function indexAcceptSuggestAction()
  {
    $object_type = $this->_getParam('object_type', '');
    $object_id = $this->_getParam('object_id', 0);
    $to_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (!$object_id || !$object_type) {
      return $this->_redirect(
        $this->view->url(array(
          'controller' => 'index',
          'action' => 'index'
        ), 'suggest_general'),
        array(
          'prependBase' => false
        )
      );
    }

    $suggests = Engine_Api::_()->suggest()->getAllSuggests(array(
        'object_type' => $object_type,
        'object_id' => $object_id,
        'to_id' => $to_id
      ), false);

    foreach ($suggests as $suggest) {
      $suggest->delete();
    }

    $object = Engine_Api::_()->getItem($object_type, $object_id);

    if ($object_type == 'question'){
      $url = $this->view->url(array('question_id' => $object_id), 'question_view', true);
    } else {
      $url = $object->getHref(array('reset' => true));
    }

    return $this->_redirect($url, array('prependBase' => false));
  }

  public function indexSuggestFbAction()
  {
    $object_type = $this->_getParam('object_type');
    $object_id = $this->_getParam('object_id');

    $this->view->host_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];

    $this->view->object = Engine_Api::_()->getItem($object_type, $object_id);

    $this->view->type = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'My Community');
    $this->view->appId = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook.appid');

    $suggestApi = Engine_Api::_()->getApi('core', 'suggest');
    $this->view->init_fb_app = $suggestApi->checkInitFbApp();
  }

  public function indexSuggestPhotoAction()
  {
    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) return $this->_forward('upload-photo', null, null, array('format' => 'json'));

    $object_id = (int)$this->_getParam('object_id', 0);
    $this->view->form = $form = new Suggest_Form_Photo();
    $form->setAction($this->view->url(array('controller' => 'index', 'action' => 'suggest-photo', 'object_type' => 'user', 'object_id' => $object_id), 'suggest_general', array('reset' => true, 'prependBase' => false)));

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return ;
    }

    $object = array(
      'type' => 'suggest_profile_photo',
      'id' => (int)$this->_getParam('file_id', 0)
    );
    $uids = array($object_id);

    $suggest_type = 'suggest_profile_photo';
    $api = Engine_Api::_()->getApi('core', 'suggest');
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($api->isAllowed($suggest_type)) {
      if ($api->suggest($viewer, $uids, $object)) {
        $message = 'suggest_Your suggestions were successfully sent.';
      } else {
        $message = 'suggest_Your suggestions were not sent.';
      }
    } else {
      $message = 'suggest_Suggestion of this type was turned off by site admin.';
    }

    $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_($message));
  }

  public function indexDeletePhotoAction()
  {
    $photo_id = (int)$this->_getParam('photo_id', 0);
    if (!$photo_id) {
      return ;
    }

    $table = Engine_Api::_()->getDbtable('profilePhotos', 'suggest');
    $select = $table->select()->where('profilephoto_id = ?', $photo_id);
    $photo = $table->fetchRow($select);

    if (!$photo) {
      return ;
    }

    $photo->delete();
  }

  public function indexUploadPhotoAction()
  {
    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $table = Engine_Api::_()->getDbtable('profilePhotos', 'suggest');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $object_id = (int)$this->_getParam('object_id', 0);

      if (!$object_id) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Object ID is not defined.');
        return ;
      }

      $row = $table->createRow();
      $row->from_id = $viewer->getIdentity();
      $row->to_id = $object_id;
      $row->save();

      $row->setPhoto($_FILES['Filedata']);
      $photo_id = $row->getIdentity();

      $this->view->photo_url = $row->getPhotoUrl();
      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo_id;

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }


}
