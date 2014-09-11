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
    

class Suggest_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
  }

  public function indexAction()
  {    
    $suggest_id = $this->_getParam('suggest_id', 0);
    $table = Engine_Api::_()->getDbTable('suggests', 'suggest');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if ($suggest_id) {
      $suggest = $table->fetchRow($table->select()->where('suggest_id = ?', $suggest_id));
      if ($suggest->to_id == $viewer->getIdentity()) {
        $suggest->delete();
      }
    }

    $return_url = urldecode($this->_getParam('return_url'));
    if ($return_url) {
      return $this->_redirect($return_url, array('prependBase'=>0));
    }

    $api = Engine_Api::_()->getApi('core', 'suggest');
    $params = array('to_id' => $viewer->getIdentity());
    $this->view->paginator = $api->getAllSuggests($params);
  }

  public function suggestAction()
  {
    $uids = $this->_getParam('contacts', array());
	$return_url = urldecode($this->_getParam('return_url'));

    if (empty($uids)) {
      $this->_redirect($return_url, array('prependBase'=>0));
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
        $this->view->type = 'notice';
        $this->view->message = 'suggest_Your suggestions were successfully sent.';
      } else {
        $this->view->type = 'error';
        $this->view->message = 'suggest_Your suggestions were not sent.';
      }
    } else {
      $this->view->type = 'error';
      $this->view->message = 'suggest_Suggestion of this type was turned off by site admin.';
    }

    $this->_redirect($return_url, array('prependBase'=>0));
    return;
  }

  public function contactsAction()
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
    $this->view->return_url = $return_url = urldecode($this->_getParam('return_url'));
    
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
    
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('All of your friends are allready friends of this friend.'))
      ));
    }
  }

  public function rejectAction()
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

    $this->view->noRec = false;
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
      $this->view->noRec = true;
    } else {
      $this->view->item = $rec;
      $this->view->object_type = $object_type;
      $this->view->object_id = $rec->getIdentity();
      $this->view->wid = $wid;
      $this->view->html = $this->view->render($tpl);
    }
  }

  public function viewAction()
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

  public function acceptSuggestAction()
  {
    $object_type = $this->_getParam('object_type', '');
    $object_id = $this->_getParam('object_id', 0);
    $to_id = Engine_Api::_()->user()->getViewer()->getIdentity();

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

  public function suggestFbAction()
  {
    $this->view->content = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.fb.invite.text', 'Join me here there is a lot of stuff.'); 
    $this->view->type = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'My Community');
    $this->view->actionText = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.fb.invite.title', 'Invite Your Friends from Facebook');
    $this->view->appId = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook.appid');
  }

  public function suggestPhotoAction()
  {
    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) return $this->_forward('upload-photo', null, null, array('format' => 'json'));
    
    $object_id = (int)$this->_getParam('object_id', 0);
    $this->view->form = $form = new Suggest_Form_Photo();
    $form->setAction($this->view->url(array('controller' => 'index', 'action' => 'suggest-photo', 'object_id' => $object_id), 'suggest_general', array('reset' => true, 'prependBase' => false)));

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

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => false,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_($message))
    ));
  }
  
}