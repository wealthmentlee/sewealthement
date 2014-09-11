<?php



class Wall_LinkedinController extends Core_Controller_Action_User
{


  public function indexAction()
  {
    $redirect_uri = Engine_Api::_()->wall()->getUrl(array('format' => 'smoothbox'));

    $setting = Engine_Api::_()->getDbTable('settings', 'core');

    $config = array(
      'requestTokenUrl' => 'https://api.linkedin.com/uas/oauth/requestToken',
      'accessTokenUrl' => 'https://api.linkedin.com/uas/oauth/accessToken',
      'authorizeUrl' => 'https://api.linkedin.com/uas/oauth/authorize',
      'consumerKey' => $setting->getSetting('wall.service.linkedin.consumerkey'),
      'consumerSecret' => $setting->getSetting('wall.service.linkedin.consumersecret'),
      'callbackUrl' => $redirect_uri
    );


    try {

      $oauth = new Zend_Oauth_Consumer($config);

      $session = new Zend_Session_Namespace("wall_service_linkedin");
      $request = $this->getRequest();
      $viewer = Engine_Api::_()->user()->getViewer();

      if ($request->getParam('oauth_token') && isset($session->request_token)) {

        $token_request = new Zend_Oauth_Token_Request();
        $token_request->setParam(Zend_Oauth_Token::TOKEN_PARAM_KEY, $session->request_token);
        $token_request->setParam(Zend_Oauth_Token::TOKEN_SECRET_PARAM_KEY, $session->request_token_secret);

        $access = $oauth->getAccessToken($request->getParams(), $token_request);


        $client = $access->getHttpClient($config);
        $client->setUri('http://api.linkedin.com/v1/people/~:(id,first-name,last-name)');
        $client->setParameterGet('format', 'json');
        $client->setMethod(Zend_Http_Client::GET);

        if (!$client->request()->isSuccessful()){
          return $this->_forward('error', 'linkedin', 'wall', array('format' => 'smoothbox'));
        }


        $body = $client->request()->getBody();
        $profile_info = Zend_Json::decode($body);

        if (empty($profile_info)){
          return $this->_forward('error', 'linkedin', 'wall', array('format' => 'smoothbox'));
        }

        $object_id = @$profile_info['id'];
        $object_name = @$profile_info['firstName'] . ' ' .  @$profile_info['lastName'];


        $table = Engine_Api::_()->getDbTable('tokens', 'wall');

        $user_id = $viewer->getIdentity();
        $provider = 'linkedin';

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

        return $this->_forward('error', 'linkedin', 'wall', array('format' => 'smoothbox'));

      } else {

        $token_request = $oauth->getRequestToken(array(
          'scope' => 'rw_nus'
        ));

        $session->request_token = $token_request->getToken();
        $session->request_token_secret = $token_request->getTokenSecret();
        $oauth->redirect();

      }


    } catch (Exception $e){

      return $this->_forward('error', 'linkedin', 'wall', array('format' => 'smoothbox'));
    }

  }




  public function streamAction()
  {

    $this->view->enabled = false;

    $provider = 'linkedin';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $this->view->token = $token = $tokenTable->getUserToken($viewer, $provider);


    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;
    $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15);

    $params = array(
      'count' => $limit
    );
    if ($this->_getParam('next')){
      $params['before'] = (string) $this->_getParam('next');
    }
    if ($this->_getParam('since')){
      $params['after'] = (string) $this->_getParam('since');
      $this->view->getUpdate = true;
    }

    $stream = $service->stream($token, $params);



    $count = (!empty($stream) && !empty($stream['values'])) ? count($stream['values']) : 0;

    $last_tweet = ($stream && isset($stream['values'][$count-1]['timestamp'])) ? (string) $stream['values'][$count-1]['timestamp']-100 : null;
    $first_tweet = ($stream && isset($stream['values'][0]['timestamp'])) ? (string) $stream['values'][0]['timestamp']+100 : null;


    $this->view->viewall = $this->_getParam('viewall', false);
    $this->view->stream = $stream;
    $this->view->next = $last_tweet;
    $this->view->since = $first_tweet;
    $this->view->count = $count;
    $this->view->show_viewall = ($limit <= $count && !$this->_getParam('viewall', false));

    if ($this->_getParam('format') == 'json'){
      $this->view->html = $this->view->render('linkedin/items.tpl');
      return ;
    }

  }


  public function postAction()
  {
    $this->view->enabled = false;
    $this->view->result = true;

    $provider = 'linkedin';
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
    $provider = 'linkedin';
    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if ($token){
      $token->delete();
    }
  }


  public function likeAction()
  {
    $this->view->enabled = false;
    $this->view->result = false;

    $id = $this->toId($this->_getParam('id'));

    $provider = 'linkedin';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $this->view->token = $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;

    $this->view->result = $service->like($token, $id);

    $this->view->action = $service->getAction($token, $id);
    $this->view->body = $this->view->render('linkedin/item.tpl');

  }



  public function unlikeAction()
  {
    $this->view->enabled = false;
    $this->view->result = false;

    $id = $this->toId($this->_getParam('id'));

    $provider = 'linkedin';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $this->view->token = $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;

    $this->view->result = $service->unlike($token, $id);

    $this->view->action = $service->getAction($token, $id);
    $this->view->body = $this->view->render('linkedin/item.tpl');

  }

  public function commentAction()
  {
    $this->view->enabled = false;
    $this->view->result = false;

    $id = $this->toId($this->_getParam('id'));

    $provider = 'linkedin';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $this->view->token = $token = $tokenTable->getUserToken($viewer, $provider);

    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;

    $this->view->result = $service->addComment($token, $id, $this->_getParam('message'));

    $this->view->action = $service->getAction($token, $id);
    $this->view->body = $this->view->render('linkedin/item.tpl');

  }



  public function toId($id)
  {
    return $id;
  }

  public function checknewAction()
  {

    $this->view->enabled = false;

    $provider = 'linkedin';
    $service = Engine_Api::_()->wall()->getServiceClass($provider);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $this->view->token = $token = $tokenTable->getUserToken($viewer, $provider);


    if (!$token || !$token->check()){
      return ;
    }

    $this->view->enabled = true;
    $this->view->limit = $limit = 3;

    $params = array(
      'count' => $limit
    );
    if ($this->_getParam('next')){
      $params['before'] = (string) $this->_getParam('next');
    }
    if ($this->_getParam('since')){
      $params['after'] = (string) $this->_getParam('since');
    }

    $stream = $service->stream($token, $params);

    $this->view->count = $count = (!empty($stream) && !empty($stream['values'])) ? count($stream['values']) : 0;

    $this->view->ok = true;
    $this->view->title = $this->view->translate(array(
        '%d new update is available - click this to show it.',
        '%d new updates are available - click this to show them.',
        $count),
      $count);
  }





}