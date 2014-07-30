<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: FacebookController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Wall_FacebookController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {
    $this->view->status = false;

    $viewer = Engine_Api::_()->user()->getViewer();
    $session = new Zend_Session_Namespace("wall_service_facebook");
    $setting = Engine_Api::_()->getDbTable('settings', 'core');

    $config = array(
      'client_id' => $setting->getSetting('wall.service.facebook.clientid'),
      'client_secret' => $setting->getSetting('wall.service.facebook.clientsecret'),
    );

    $redirect_uri = Engine_Api::_()->wall()->getUrl(array('format' => 'smoothbox'));
    $redirect_uri = urlencode($redirect_uri);

    $code = $this->_getParam('code');

    if (empty($code)){

      $session->state = md5(uniqid(rand(), TRUE));

      $url = "http://www.facebook.com/dialog/oauth?client_id=". $config['client_id'] . "&redirect_uri=" . $redirect_uri . "&state=" . $session->state . "&scope=publish_stream,read_stream,offline_access,manage_pages&auth_type=reauthenticate";
      header("Location: $url");
      exit(1);


    } else if ($session->state == $this->_getParam('state')){

      $url = "https://graph.facebook.com/oauth/access_token?client_id=" . $config['client_id'] . "&redirect_uri=" . $redirect_uri . "&client_secret=" . $config['client_secret'] . "&code=" . $code;
      $response = Engine_Api::_()->wall()->getUrlContent($url, null, true);

      $access_token = null;
      if ($response){
        $params = null;
        parse_str($response, $params);
        if ($params && isset($params['access_token'])) {
          $access_token = $params['access_token'];
        }
      }

      if (!$access_token){
        return $this->_forward('error', 'twitter', 'wall', array('format' => 'smoothbox'));
      }

      $url = 'https://graph.facebook.com/me?access_token=' . $access_token;
      $content = Engine_Api::_()->wall()->getUrlContent($url, null, true);

      if (!$content){
        return $this->_forward('error', 'twitter', 'wall', array('format' => 'smoothbox'));
      }
      $profile_info = Zend_Json::decode($content);

      if (!$profile_info || isset($profile_info['error'])){
        return $this->_forward('error', 'twitter', 'wall', array('format' => 'smoothbox'));
      }


      $table = Engine_Api::_()->getDbTable('tokens', 'wall');

      $user_id = $viewer->getIdentity();
      $object_id = (isset($profile_info['id'])) ? $profile_info['id'] : null;
      $object_name = (isset($profile_info['name'])) ? $profile_info['name'] : null;
      $provider = 'facebook';

      $select = $table->select()
        ->where('user_id = ?', $viewer->getIdentity())
        ->where('provider = ?', $provider)
        ->where('object_id = ?', $object_id);

      $tokenRow = $table->fetchRow($select);

      if (!$tokenRow){
        $tokenRow = $table->createRow();
      }

      $this->view->task = $task = $this->_getParam('task');


      $tokenRow->user_id = $user_id;
      $tokenRow->object_id = $object_id;
      $tokenRow->object_name = $object_name;
      $tokenRow->provider = $provider;
      $tokenRow->oauth_token = $access_token;
      $tokenRow->oauth_token_secret = 0;
      $tokenRow->creation_date = date('Y-m-d H:i:s');
      $tokenRow->save();

      $this->view->tokenRow = $tokenRow;



      $service = Engine_Api::_()->wall()->getServiceClass('facebook');
      $this->view->fb_pages = $service->getPages($tokenRow);

    }



  }


  public function streamAction()
  {
    $this->view->enabled = false;

    $provider = 'facebook';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $this->view->token = $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;
    $this->view->limit = $limit = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15);

    $params = array(
      'limit' => $limit,
    );

    if ($this->_getParam('next')){
      $params['until'] = $this->_getParam('next');
    }
    if ($this->_getParam('since')){
      $params['since'] = $this->_getParam('since');
      $this->view->getUpdate = true;
    }

    $this->view->viewall = $this->_getParam('viewall', false);
    $this->view->stream = $stream = $service->stream($token, $params);
	
    $count = (empty($stream['data']) ? 0 : count($stream['data']));
    $this->view->show_viewall = ($limit <= $count ); //&& !$this->_getParam('viewall', false)


    $temp_data = null;
    $next = null;
    $previous = null;

    $paging = $this->getPaging($stream);
    $this->view->next = $paging['until'];
    $this->view->since = $paging['since'];

    if ($this->_getParam('format') == 'json'){
      $this->view->html = $this->view->render('facebook/items.tpl');
      return ;
    }




  }

  public function postAction()
  {
    $this->view->enabled = false;

    $provider = 'facebook';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;
    
    $service->postStatus($token, $this->_getParam('body'));




  }

  protected function getPaging($stream)
  {
    $until = null;
    $since = null;

    if (!empty($stream['paging'])){
      $matches = null;

      if (!empty($stream['paging']['previous'])){
        preg_match('/since=([0-9]*)/i', $stream['paging']['previous'], $matches);
        $since = (isset($matches[1])) ? $matches[1] : null;
      }
      if (!empty($stream['paging']['next'])){
        preg_match('/until=([0-9]*)/i', $stream['paging']['next'], $matches);
        $until = (isset($matches[1])) ? $matches[1] : null;
      }
    }
    return array(
      'until' => $until,
      'since' => $since
    );
  }

  public function errorAction()
  {
  }

  public function logoutAction()
  {
    $provider = 'facebook';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if ($token){
      $token->delete();
    }
    Engine_Api::_()->getDbTable('settings', 'user')->setSetting($viewer, 'wall-linked-pages', '');

  }


  public function checknewAction()
  {
    $this->view->enabled = false;

    $provider = 'facebook';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;
    $this->view->limit = $limit = 10;

    $params = array(
      'limit' => $limit,
    );

    if ($this->_getParam('since')){
      $params['since'] = $this->_getParam('since');
    }

    $this->view->stream = $stream = $service->stream($token, $params);

    $this->view->count = $count = (empty($stream['data']) ? 0 : count($stream['data']));


    $this->view->ok = true;
    $this->view->title = $this->view->translate(array(
      '%d new update is available - click this to show it.',
      '%d new updates are available - click this to show them.',
        $count),
      $count);
  }


  public function likeAction()
  {
    $this->view->enabled = false;
    $this->view->result = false;

    $id = $this->toId($this->_getParam('id'));

    $provider = 'facebook';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $this->view->token = $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;

    $url = 'https://graph.facebook.com/'.$id.'/likes?access_token=' . $token->oauth_token . '&format=json-strings';

    $client = new Zend_Http_Client($url, array());
    $response = $client->request(Zend_Http_Client::POST);
    if ($response->isSuccessful()){
      $this->view->result = $response->getBody();
    }

    $this->view->action = $this->getAction($token, $id);
    $this->view->body = $this->view->render('facebook/item.tpl');

  }

  public function unlikeAction()
  {
    $this->view->enabled = false;
    $this->view->result = false;

    $id = $this->toId($this->_getParam('id'));

    $provider = 'facebook';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $this->view->token = $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;

    $url = 'https://graph.facebook.com/'.$id.'/likes?access_token=' . $token->oauth_token . '&format=json-strings';

    $client = new Zend_Http_Client($url, array());
    $response = $client->request(Zend_Http_Client::DELETE);
    if ($response->isSuccessful()){
      $this->view->result = $response->getBody();
    }

    $this->view->action = $this->getAction($token, $id);
    $this->view->body = $this->view->render('facebook/item.tpl');

  }

  public function commentAction()
  {
    $this->view->enabled = false;
    $this->view->result = false;

    $id = $this->toId($this->_getParam('id'));

    $provider = 'facebook';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $this->view->token = $token = $tokenTable->getUserToken($viewer, $provider);


    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;

    $url = 'https://graph.facebook.com/'.$id.'/comments?access_token=' . $token->oauth_token . '&format=json-strings';

    $client = new Zend_Http_Client($url, array());
    $client->setParameterPost('message', $this->_getParam('message'));
    $response = $client->request(Zend_Http_Client::POST);
    if ($response->isSuccessful()){
      $this->view->result = $response->getBody();
    }

    $this->view->action = $this->getAction($token, $id);
    $this->view->body = $this->view->render('facebook/item.tpl');

  }

  public function getAction($token, $id)
  {
    $url = 'https://graph.facebook.com/'.$id.'/?access_token=' . $token->oauth_token . '&format=json-strings';

    $client = new Zend_Http_Client($url, array());
    $response = $client->request(Zend_Http_Client::GET);
    if ($response->isSuccessful()){
      return Zend_Json::decode($response->getBody());
    }
    return false;
  }

  public function toId($id)
  {
    if (empty($id)){
      return false;
    }
    $matches = array();
    preg_match_all('/[0-9]{0,}_[0-9]{0,}/im', $id, $matches);
    if (empty($matches) || empty($matches[0]) || empty($matches[0][0])){
      return false;
    }
    return $matches[0][0];
  }



  public function selectPageAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    Engine_Api::_()->wall()->setPageLink($viewer, $this->_getParam('page_id'), $this->toPageId($this->_getParam('fbpage_id')));
  }

  public function toPageId($id)
  {
    if (empty($id)){
      return false;
    }
    $matches = array();
    preg_match_all('/[0-9]{0,}/im', $id, $matches);
    if (empty($matches) || empty($matches[0]) || empty($matches[0][0])){
      return false;
    }
    return $matches[0][0];
  }



}