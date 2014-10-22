<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 24.07.12
 * Time: 11:29
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_WallController
  extends Apptouch_Controller_Action_Bridge
{


  public function indexChangePrivacyAction()
  {
    $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));

    if (!$action || !$action->canChangePrivacy(Engine_Api::_()->user()->getViewer())){
      return ;
    }

    $action->changePrivacy($this->_getParam('privacy'));


  }


  public function indexMuteAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $checkin = $this->_getParam('checkin', false);

    $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));
    if (!$action){
      return ;
    }
    $table = Engine_Api::_()->getDbTable('mute', 'wall');
    $viewer = Engine_Api::_()->user()->getViewer();

    $select = $table->select()
        ->where('user_id = ?', $viewer->getIdentity())
        ->where('action_id = ?', $action->getIdentity());

    $mute = $table->fetchRow($select);

    if (!$mute){

      $mute = $table->createRow();
      $mute->setFromArray(array(
        'user_id' => $viewer->getIdentity(),
        'action_id' => $action->getIdentity()
      ));
      $mute->save();

    }




  }

  public function indexViewAction()
  {
    return $this->redirect($this->view->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'view', 'action_id' => (int) $this->_getParam('id')), 'default', true));
  }

  public function indexUnmuteAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $checkin = $this->_getParam('checkin', false);

    $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));
    if (!$action){
      return ;
    }
    $table = Engine_Api::_()->getDbTable('mute', 'wall');
    $viewer = Engine_Api::_()->user()->getViewer();

    $select = $table->select()
        ->where('user_id = ?', $viewer->getIdentity())
        ->where('action_id = ?', $action->getIdentity());

    $mute = $table->fetchRow($select);

    if ($mute){
      $mute->delete();
    }


    $this->view->status = true;

    // Redirect if not json context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    else if ('json'===$this->_helper->contextSwitch->getCurrentContext())
    {
      $this->view->body = $this->getHelper('activity')->direct()->activity($action, array());
    }


    if ($action) {
      $this->view->action = $this->getHelper('activity')->direct()->activity($action, array());
    }


  }


  public function indexRemoveTagAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $checkin = $this->_getParam('checkin', false);

    $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));
    if (!$action){
      return ;
    }
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$action->canRemoveTag($viewer)){
      return ;
    }

    $table = Engine_Api::_()->getDbTable('tags', 'wall');

    $select = $table->select()
        ->where('action_id = ?', $action->getIdentity())
        ->where('object_type = ?', $viewer->getType())
        ->where('object_id = ?', $viewer->getIdentity());

    foreach ($table->fetchAll($select) as $tag){
      $tag->delete();
    }

    $this->view->status = true;

    // Redirect if not json context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    else if ('json'===$this->_helper->contextSwitch->getCurrentContext())
    {
      $this->view->body = $this->view->wallActivity($action, array(
        'checkin' => $checkin,
        'noList' => true,
        'comment_pagination' => $this->_getParam('comment_pagination'),
        'module' => $this->_script_module,
      ));
    }


    if ($action) {
      $this->view->action = $this->getHelper('activity')->direct()->activity($action, array());
    }


  }

  public function indexServicesRequestAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()){
      return ;
    }

    foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service){
      if ($this->_getParam($service)){
        $this->view->$service = false;
        $class = Engine_Api::_()->wall()->getServiceClass($service);
        if (!$class){
          continue ;
        }
        $token = Engine_Api::_()->getDbTable('tokens', 'wall')->getUserToken($viewer, $service);
        if (!$token){
          continue ;
        }
        if (!$token->check()){
          continue ;
        }
        $data = array_merge(array('enabled' => true), $token->publicArray());

        if ($service == 'facebook'){
          $data['fb_pages'] = $class->getPages($token);
        }

        $this->view->$service = $data;
      }
    }

  }

  public function indexServiceShareAction()
  {
    $provider = $this->_getParam('provider');
    $viewer = Engine_Api::_()->user()->getViewer();

    $setting_key = 'share_' . $provider . '_enabled';

    $setting = Engine_Api::_()->wall()->getUserSetting($viewer);

    if (isset($setting->{$setting_key})){
      $setting->setFromArray(array($setting_key => (int) $this->_getParam('status', 0)));
      $setting->save();
     }
  }



  /**
   * Facebook Controller
   */

  public function facebookIndexAction()
  {
    $tokenRow = null;

    $this->view->status = false;

    $viewer = Engine_Api::_()->user()->getViewer();
    $session = new Zend_Session_Namespace("wall_service_facebook");
    $setting = Engine_Api::_()->getDbTable('settings', 'core');

    $config = array(
      'client_id' => $setting->getSetting('wall.service.facebook.clientid'),
      'client_secret' => $setting->getSetting('wall.service.facebook.clientsecret'),
    );

    $redirect_uri = Engine_Api::_()->wall()->getUrl(array());
    $redirect_uri = urlencode($redirect_uri);

    $code = $this->_getParam('code');

    // if application
    if( Engine_Api::_()->apptouch()->isApp() && $this->_getParam('facebook_access_token', false)) {
      $facebook_access_token = $this->_getParam('facebook_access_token');
      $url = 'https://graph.facebook.com/me?access_token=' . $facebook_access_token;
      $content = Engine_Api::_()->wall()->getUrlContent($url, null, true);

      if (!$content){
        $this
          ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR1')))
          ->renderContent();
        return ;
      }
      $profile_info = Zend_Json::decode($content);

      if (!$profile_info || isset($profile_info['error'])){
        $this
          ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR2')))
          ->renderContent();
        return ;
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
      $tokenRow->oauth_token = $facebook_access_token;
      $tokenRow->oauth_token_secret = 0;
      $tokenRow->creation_date = date('Y-m-d H:i:s');
      $tokenRow->save();

      $this->view->tokenRow = $tokenRow;



      $service = Engine_Api::_()->wall()->getServiceClass('facebook');
      $this->view->fb_pages = $service->getPages($tokenRow);
      return;
    }

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
        $this
          ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR3')))
          ->renderContent();
        return ;
      }

      $url = 'https://graph.facebook.com/me?access_token=' . $access_token;
      $content = Engine_Api::_()->wall()->getUrlContent($url, null, true);

      if (!$content){
        $this
          ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR4')))
          ->renderContent();
        return ;
      }
      $profile_info = Zend_Json::decode($content);

      if (!$profile_info || isset($profile_info['error'])){
        $this
          ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR4')))
          ->renderContent();
        return ;
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


    $return_url = $this->_getParam('return_url', false);
    if ($return_url) {
      return $this->_helper->redirector->gotoUrl($return_url, array('prependBase' => false));
    }


    if ($tokenRow){

      $content = <<<CONTENT

  <script type="text/javascript">
    if (window.opener && window.opener.wall_form){
      window.opener.wall_form.callbackFromWindow("{$tokenRow->provider}");
    }
    if (window.opener && window.opener.ShareForm){
      window.opener.ShareForm.callbackFromWindow("{$tokenRow->provider}");
    }
    window.close();
  </script>

CONTENT;


      $this
        ->add($this->component()->html($content))
        ->renderContent();

      return ;

    }

    $this
      ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR5')))
      ->renderContent();
    return ;


  }



  public function facebookStreamAction()
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

  public function facebookPostAction()
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

  public function facebookErrorAction()
  {
  }

  public function facebookLogoutAction()
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



  /**
   * Twitter Controller
   */

  public function twitterIndexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $params = $this->_getAllParams();
    // for application
    if( Engine_Api::_()->apptouch()->isApp() && $params && !empty($params)) {

      if( empty($params['user_id']) || empty($params['oauth_token']) || empty($params['oauth_token_secret']) || empty($params['screen_name']) ) {
        return false;
      }

      $table = Engine_Api::_()->getDbTable('tokens', 'wall');

      $user_id = $viewer->getIdentity();
      $object_id = $params['user_id'];
      $object_name = $params['screen_name'];
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
      $tokenRow->oauth_token = $params['oauth_token'];
      $tokenRow->oauth_token_secret = $params['oauth_token_secret'];
      $tokenRow->creation_date = date('Y-m-d H:i:s');
      $tokenRow->save();

      return;
    }


    $tokenRow = null;

    $redirect_uri = Engine_Api::_()->wall()->getUrl(array());

    $setting = Engine_Api::_()->getDbTable('settings', 'core');

    $config = array(
      'siteUrl' => 'http://twitter.com/oauth',
      'consumerKey' => $setting->getSetting('wall.service.twitter.consumerkey'),
      'consumerSecret' => $setting->getSetting('wall.service.twitter.consumersecret'),
      'callbackUrl' => $redirect_uri
    );



    try {

    $oauth = new Zend_Oauth_Consumer($config);

    $session = new Zend_Session_Namespace("wall_service_twitter");
    $request = $this->getRequest();

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
      $this
        ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR')))
        ->renderContent();
      return ;

    } else {

      $token_request = $oauth->getRequestToken();

      $session->request_token = $token_request->getToken();
      $session->request_token_secret = $token_request->getTokenSecret();
      $oauth->redirect();

    }


    } catch (Exception $e){
      $this
        ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR')))
        ->renderContent();
      return ;
    }

    $return_url = $this->_getParam('return_url', false);
    if ($return_url) {
      return $this->_helper->redirector->gotoUrl($return_url, array('prependBase' => false));
    }



    if ($tokenRow){

      $content = <<<CONTENT

  <script type="text/javascript">
    if (window.opener && window.opener.wall_form){
      window.opener.wall_form.callbackFromWindow("{$tokenRow->provider}");
    }
    if (window.opener && window.opener.ShareForm){
      window.opener.ShareForm.callbackFromWindow("{$tokenRow->provider}");
    }
    window.close();
  </script>

CONTENT;


      $this
        ->add($this->component()->html($content))
        ->renderContent();

      return ;

    }

    $this
      ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR')))
      ->renderContent();
    return ;


  }

  public function twitterStreamAction()
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


  public function twitterPostAction()
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

  public function twitterErrorAction()
  {

  }

  public function twitterLogoutAction()
  {
    $provider = 'twitter';
    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if ($token){
      $token->delete();
    }
  }


  public function twitterFavoriteAction()
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

  public function twitterUnfavoriteAction()
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

  public function twitterDestroyAction()
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

  public function twitterRetweetAction()
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

  public function twitterReplyAction()
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

  public function twitterChecknewAction()
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


  /**
   * Linkedin Controller
   */


  public function linkedinIndexAction()
  {


    $tokenRow = null;

    $redirect_uri = Engine_Api::_()->wall()->getUrl(array());

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
          $this
            ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR')))
            ->renderContent();
          return ;
        }


        $body = $client->request()->getBody();
        $profile_info = Zend_Json::decode($body);

        if (empty($profile_info)){
          $this
            ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR')))
            ->renderContent();
          return ;
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

        $this
          ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR')))
          ->renderContent();
        return ;

      } else {

        $token_request = $oauth->getRequestToken(array(
          'scope' => 'rw_nus'
        ));

        $session->request_token = $token_request->getToken();
        $session->request_token_secret = $token_request->getTokenSecret();
        $oauth->redirect();

      }


    } catch (Exception $e){

      $this
        ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR')))
        ->renderContent();
      return ;
    }



    $return_url = $this->_getParam('return_url', false);
    if ($return_url) {
      return $this->_helper->redirector->gotoUrl($return_url, array('prependBase' => false));
    }


    if ($tokenRow){

      $content = <<<CONTENT

  <script type="text/javascript">
    if (window.opener && window.opener.wall_form){
      window.opener.wall_form.callbackFromWindow("{$tokenRow->provider}");
    }
    if (window.opener && window.opener.ShareForm){
      window.opener.ShareForm.callbackFromWindow("{$tokenRow->provider}");
    }
    window.close();
  </script>

CONTENT;


      $this
        ->add($this->component()->html($content))
        ->renderContent();

      return ;

    }

    $this
      ->add($this->component()->html($this->view->translate('WALL_STREAM_ERROR')))
      ->renderContent();
    return ;


  }

  public function linkedinStreamAction()
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


  public function linkedinPostAction()
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

  public function linkedinErrorAction()
  {

  }

  public function linkedinLogoutAction()
  {
    $provider = 'linkedin';
    $viewer = Engine_Api::_()->user()->getViewer();

    $tokenTable = Engine_Api::_()->getDbTable('tokens', 'wall');
    $token = $tokenTable->getUserToken($viewer, $provider);

    if ($token){
      $token->delete();
    }
  }


  public function linkedinLikeAction()
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



  public function linkedinUnlikeAction()
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

  public function linkedinCommentAction()
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

  public function linkedinChecknewAction()
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




  public function indexSuggestPeopleAction()
  {
    $select = Engine_Api::_()->wall()->getSuggestPeople(Engine_Api::_()->user()->getViewer(), array('search' => $this->_getParam('value')));
    $paginator = Zend_Paginator::factory($select);


    $data = array();

    $paginator->setItemCountPerPage(50);
    foreach (Engine_Api::_()->wall()->getItems($paginator->getCurrentItems()) as $item){
      $data[] = array(
        'type'  => $item->getType(),
        'id'    => $item->getIdentity(),
        'guid'  => $item->getGuid(),
        'label' => $item->getTitle(),
        'photo' => $this->view->itemPhoto($item, 'thumb.icon'),
        'url'   => $item->getHref(),
      );
    }

    if( $this->_getParam('sendNow', true) )
    {
      return $this->_helper->json($data);
    }
    else
    {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }

  }




}
