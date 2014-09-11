<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ClubController.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_ClubController extends Core_Controller_Action_Standard
{
	protected $_data;

	protected $_active;

	protected  $_subject; 
	
	public function init()
	{	
		$this->view->object = $this->_getParam('object');
		$this->view->object_id = (int)$this->_getParam('object_id');
		$this->view->active = $this->_active = $this->_getParam('active');
		$this->_subject = Engine_Api::_()->getItem($this->view->object, $this->view->object_id);
		$this->_data = array('object' => $this->view->object, 'object_id' => $this->view->object_id);
	}
	
	public function requireAuth()
	{
		$subject = $this->_subject;
		if ($subject->getType() == 'page'){
			$func = 'isTeamMember';
		}else{
			$func = 'isOwner';
		}
		return ($subject->$func(Engine_Api::_()->user()->getViewer()) && Engine_Api::_()->like()->isAllowed($subject));
	}
			
	public function promoteAction()
	{
		if (!$this->requireAuth()) {
			$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => false,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('LIKE_AUTH_ERROR'))
			));
			return ;
		}
	}

	public function likeBoxAction()
	{
		$subject = $this->_subject;
		if (!Engine_Api::_()->like()->isAllowed($subject)) {
			$this->view->html = '';
			return ;
		}
		
    $this->_helper->layout->disableLayout();
    
    $this->view->base_url = 'http://' . $_SERVER['HTTP_HOST'];
    $this->view->icon_url = Engine_Api::_()->like()->getLogo();

		$likes = Engine_Api::_()->like()->getLikes($this->_subject);
    $likes->setItemCountPerPage(8);
		
		$this->view->likes = $likes;
		$this->view->subject = Engine_Api::_()->getItem($this->_data['object'], $this->_data['object_id']);
		$this->view->html = $this->view->render('_composeLikeBox.tpl');
	}

  public function likeButtonAction()
	{
		$subject = $this->_subject;
		if (!Engine_Api::_()->like()->isAllowed($subject)) {
      exit();
		}

    $layoutHelper = $this->_helper->layout;
    $layoutHelper->disableLayout();

    $this->view->base_url = $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->subject = $subject = Engine_Api::_()->getItem($this->_data['object'], $this->_data['object_id']);

    $urls = array();
    $likes_array = array();
    $likes = Zend_Paginator::factory(array());
    $this->view->actionName = 'like';
    $this->view->is_liked = false;
    $this->view->viewer_url = '';

    if ($viewer->getIdentity()){
      $params['friend_id'] = $viewer->getIdentity();
      $likes = Engine_Api::_()->like()->getLikes($this->_subject);
      $likes->setItemCountPerPage(2);
      if ($likes->getTotalItemCount() > 0){
        foreach($likes as $like){
          $urls[$like->user_id] = $baseUrl . $like->getHref();
          $likes_array[] = $like->toArray();
        }
      }
      if (!Engine_Api::_()->like()->isLike($subject)){
        $this->view->actionName = 'like';
        $this->view->is_liked = false;
      }
      else{
        $this->view->actionName = 'unlike';
        $likes_array[] = $viewer->toArray();
        $this->view->is_liked = true;
      }

      $this->view->viewer_url = $viewer->getHref();
    }

    $this->view->icon_url = Engine_Api::_()->like()->getLogo();

    $this->view->like_logo = $likes;
    $this->view->likes_json = Zend_Json_Encoder::encode($likes_array);
    $this->view->likes = $likes;

    $this->view->urls = $urls;
    $this->view->like_url = $baseUrl . $this->view->url(array('action' => 'like', 'object' => $subject->getType(), 'object_id' => $subject->getIdentity()), 'like_remote');
    $this->view->unlike_url = $baseUrl . $this->view->url(array('action' => 'unlike', 'object' => $subject->getType(), 'object_id' => $subject->getIdentity()), 'like_remote');

    $this->view->like_img = $baseUrl . $this->view->baseUrl() . '/application/modules/Like/externals/images/like_button.png';
    $this->view->unlike_img = $baseUrl . $this->view->baseUrl() . '/application/modules/Like/externals/images/unlike_button.png';

    $this->view->like_count = (int)Engine_Api::_()->like()->getLikeCount($subject);
    $return_url = $baseUrl . $this->view->url(array('action' => 'like', 'object' => $subject->getType(), 'object_id' => $subject->getIdentity()), 'like_login');
    $this->view->login_url = $baseUrl . $this->view->url(array(), 'like_login') . '?return_url=' . $return_url;
    $this->view->viewer_id = (int)$viewer->getIdentity();
		$this->view->html = $this->view->render('_composeLikeButton.tpl');
    $this->view->orientation  = ( $this->view->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' );
	}
	
  public function suggestAction()
  {
		if (!$this->requireAuth()) {
			$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => false,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('LIKE_AUTH_ERROR'))
			));
			
			return ;
		}
    
    $user_ids = $this->_getParam('user_ids', array());
    $object = $this->_subject;
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if (!$viewer->getIdentity() || !$object->getIdentity() || empty($user_ids)){
      return ;
    }

    $email_params = array(
      'user' => $viewer->getTitle(),
      'link' => $this->view->htmlLink("http://".$_SERVER['HTTP_HOST'].$object->getHref(), $object->getTitle())
    );

    foreach (Engine_Api::_()->getItemTable('user')->find($user_ids) as $user){
      if ($viewer->getIdentity() == $user->getIdentity()){
        continue;
      }
      
      Engine_Api::_()->getApi('mail', 'core')->sendSystem(
        $user,
        'like_suggest_'.$object->getType(),
        $email_params
      );

      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
        $user,
        $viewer,
        $object,
        'like_suggest',
        array('label' => $object->getTitle())
      );
    }
  }
	
  public function sendUpdateAction()
  {
    if (!$this->requireAuth()) {
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => false,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('LIKE_AUTH_ERROR'))
        ));
        return ;
    }
    $this->view->messageForm = $messageForm = new Like_Form_Message();
    $likes = Engine_Api::_()->like()->getLikes($this->_data);
    $likes->setItemCountPerPage($likes->getTotalItemCount());

    if (!$likes->getTotalItemCount()) {
      $messageForm->setDescription('')
        ->clearElements()
        ->addError(Zend_Registry::get('Zend_Translate')->_('like_No one has liked this page'));

      return ;
    }

    $messageForm->setAction($this->view->url(array('action' => 'send-update'), 'like_club'));
    $messageForm->getElement('object')->setValue($this->view->object);
    $messageForm->getElement('object_id')->setValue($this->view->object_id);
    $subject = $this->_subject;
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->getRequest()->isPost()) {
        return ;
    } else {
        if ($messageForm->isValid($this->getRequest()->getPost())) {
            $values = $messageForm->getValues();
        } else {
            $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => false,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('like_Your message was not send.'))
    ));
            return ;
        }
    }
    $convoTable = Engine_Api::_()->getDbTable('conversations', 'messages');
    $notificationTable = Engine_Api::_()->getDbTable('notifications', 'activity');

    //$recipients = array();
    foreach ($likes as $like) {
      if ($viewer->getIdentity() == $like->user_id) {
        continue ;
      }

      $recipient = $like->user_id;
      $user = Engine_Api::_()->getItem('user', $like->user_id);
      $notificationTable->addNotification($user, $viewer, $subject, 'like_send_update');

      if ($recipient){
        $convoTable->send($viewer, $recipient, $values['title'], $values['body']);
      }
    }

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => false,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('like_Your message successfully sent.'))
    ));
  }

  public function showLikeBoxAction()
  {
    $this->_helper->layout->disableLayout();

    $object_type = $this->_getParam('object', 'page');
    $object_id = $this->_getParam('object_id', 0);

    $this->view->like_box_url = $this->view->url(array(
      'action' => 'like-box',
      'object' => $object_type,
      'object_id' => $object_id
    ), 'like_box');

    $subject = $this->_subject;
		if (!$subject || !Engine_Api::_()->like()->isAllowed($subject)) {
			$this->view->html = '';
			return ;
		}

    $this->_helper->layout->disableLayout();

    $this->view->base_url = 'http://' . $_SERVER['HTTP_HOST'];
    $this->view->icon_url = Engine_Api::_()->like()->getLogo();

		$likes = Engine_Api::_()->like()->getLikes($this->_subject);
    $likes->setItemCountPerPage(8);

		$this->view->likes = $likes;
		$this->view->subject = Engine_Api::_()->getItem($this->_data['object'], $this->_data['object_id']);
		$this->view->html = $this->view->render('_composeLikeBox.tpl');
    $this->view->orientation  = ( $this->view->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' );
  }
}