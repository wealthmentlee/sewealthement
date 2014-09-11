<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TwitterController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_TwitterController extends Core_Controller_Action_Standard
{


  public function indexAction()
  {

    $redirect_uri = Engine_Api::_()->wall()->getUrl(array('format' => 'smoothbox'));

    $setting = Engine_Api::_()->getDbTable('settings', 'core');

    $config = array(
      'siteUrl' => 'https://api.twitter.com/oauth',
      'consumerKey' => $setting->getSetting('wall.service.twitter.consumerkey'),
      'consumerSecret' => $setting->getSetting('wall.service.twitter.consumersecret'),
      'callbackUrl' => $redirect_uri
    );

    try {

    $oauth = new Zend_Oauth_Consumer($config);

    $session = new Zend_Session_Namespace("wall_service_twitter");
    $request = $this->getRequest();

    $viewer = Engine_Api::_()->user()->getViewer();

    if ($request->getParam('oauth_token') && isset($session->request_token)) {

        $token_request = new Zend_Oauth_Token_Request();
        $token_request->setParam(Zend_Oauth_Token::TOKEN_PARAM_KEY, $session->request_token);
        $token_request->setParam(Zend_Oauth_Token::TOKEN_SECRET_PARAM_KEY, $session->request_token_secret);

        $access = $oauth->getAccessToken($request->getParams(), $token_request);

        $table = Engine_Api::_()->getDbTable('tokens', 'wall');

        $user_id = $viewer->getIdentity();
        $object_id = $access->getParam('user_id');
        $object_name = $access->getParam('screen_name');
        $provider = 'twitter';

        $select = $table->select()
          ->where('user_id = ?', $viewer->getIdentity())
          ->where('provider = ?', $provider)
          ->where('object_id = ?', $object_id);

        $tokenRow = $table->fetchRow($select);

        if (!$tokenRow){
          $tokenRow = $table->createRow();
        }

        $tokenRow->user_id = $user_id;
        $tokenRow->object_id = $object_id;
        $tokenRow->object_name = $object_name;
        $tokenRow->provider = $provider;
        $tokenRow->oauth_token = $access->getToken();
        $tokenRow->oauth_token_secret = $access->getTokenSecret();
        $tokenRow->creation_date = date('Y-m-d H:i:s');
        $tokenRow->save();

        $this->view->tokenRow = $tokenRow;

        $this->view->task = $task = $this->_getParam('task');


    } else if ($request->getParam('denied')){

      return $this->_forward('error', 'twitter', 'wall', array('format' => 'smoothbox'));

    } else {

      $token_request = $oauth->getRequestToken();

      $session->request_token = $token_request->getToken();
      $session->request_token_secret = $token_request->getTokenSecret();
      $oauth->redirect();

    }


    } catch (Exception $e){
      return $this->_forward('error', 'twitter', 'wall', array('format' => 'smoothbox'));
    }

  }

  public function streamAction()
  {
    $this->view->enabled = false;

    $provider = 'twitter';
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
      'count' => $limit+2
    );
    if ($this->_getParam('next')){
      $params['max_id'] = (string) $this->_getParam('next');
    }
    if ($this->_getParam('since')){
      $params['since_id'] = (string) $this->_getParam('since');
      $this->view->getUpdate = true;
    }

    $this->view->params = $params;

    $stream = $service->stream($token, $params);
    $count = ($stream) ? count($stream) : 0;

    $last_tweet = ($stream && isset($stream[$count-1]['id_str'])) ? (string) $stream[$count-1]['id_str'] : null;
    $first_tweet = ($stream && isset($stream[0]['id_str'])) ? (string) $stream[0]['id_str'] : null;

    if ($count >= $limit){
      if (isset($stream[$count-1])){
        unset($stream[$count-1]);
        $count--;
      }
    }

    $this->view->viewall = $this->_getParam('viewall', false);
    $this->view->stream = $stream;
    $this->view->next = $last_tweet;
    $this->view->since = $first_tweet;
    $this->view->count = $count = (empty($stream) ? 0 : count($stream));
	  $this->view->show_viewall = ($limit <= $count); // && !$this->_getParam('viewall', false)

    if ($this->_getParam('format') == 'json'){
      $this->view->html = $this->view->render('twitter/items.tpl');
      return ;
    }

  }
  

  public function postAction()
  {
    $this->view->enabled = false;
    $this->view->result = true;

    $provider = 'twitter';
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

  public function errorAction()
  {
    
  }

  public function logoutAction()
  {
    $provider = 'twitter';
    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if ($token){
      $token->delete();
    }
  }


  public function favoriteAction()
  {
    $this->view->enabled = false;
    $this->view->result = true;

    $provider = 'twitter';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }
    $this->view->enabled = true;

    $service->favorite($token, $this->_getParam('id'));

  }

  public function unfavoriteAction()
  {
    $this->view->enabled = false;
    $this->view->result = true;

    $provider = 'twitter';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }
    $this->view->enabled = true;

    $service->favorite($token, $this->_getParam('id'));

  }

  public function destroyAction()
  {

    $this->view->enabled = false;
    $this->view->result = true;

    $provider = 'twitter';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }
    $this->view->enabled = true;

    $service->destroy($token, $this->_getParam('id'));


  }

  public function retweetAction()
  {
    $this->view->enabled = false;
    $this->view->result = true;

    $provider = 'twitter';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }
    $this->view->enabled = true;

    $service->retweet($token, $this->_getParam('id'));

  }

  public function replyAction()
  {
    $this->view->enabled = false;
    $this->view->result = true;

    $provider = 'twitter';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }
    $this->view->enabled = true;

    $service->reply($token, $this->_getParam('message'), $this->_getParam('id'));

  }

  public function checknewAction()
  {
    $this->view->enabled = false;

    $provider = 'twitter';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $this->view->token = $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;
    $this->view->limit = $limit = 10;

    $params = array(
      'count' => $limit+1
    );
    if ($this->_getParam('since')){
      $params['since_id'] = (string) $this->_getParam('since');
    }

    $stream = $service->stream($token, $params);
    $count = ($stream) ? count($stream) : 0;

    if ($count >= $limit){
      if (isset($stream[$count-1])){
        unset($stream[$count-1]);
        $count--;
      }
    }

    $this->view->stream = $stream;
    $this->view->count = $count = (empty($stream) ? 0 : count($stream));


    $this->view->ok = true;
    $this->view->title = $this->view->translate(array(
        '%d new update is available - click this to show it.',
        '%d new updates are available - click this to show them.',
        $count),
      $count);
  }





}