<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Twitter.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Service_Twitter extends Wall_Plugin_Service_Abstract
{

  public function check(Wall_Model_Token $token)
  {
    try {
	
	  $setting = Engine_Api::_()->getDbTable('settings', 'core');	
	
      $config = array(
        'consumerKey' => $setting->getSetting('wall.service.twitter.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.twitter.consumersecret'),
      );

      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);
      //http://api.twitter.com/1/account/totals.json
      $client = $access->getHttpClient($config);
      $client->setUri('https://api.twitter.com/1.1/account/verify_credentials.json');
      $client->setMethod(Zend_Http_Client::GET);
      //print_die($client->request() );
      $this->is_enabled = (bool)$client->request()->isSuccessful();
      return $this->is_enabled;

    } catch (Exception $e) {

    }
    $this->is_enabled = false;
    return $this->is_enabled;
  }


  public function postAction(Wall_Model_Token $token, Activity_Model_Action $action, User_Model_User $user)
  {

    $fields = Engine_Api::_()->wall()->getActionFields($action);


    $space = 140;
    $space = $space - strlen($fields['link'])-1;
    $body = '';
    if (!empty($fields['message'])){
      $body = $fields['message'];
    } else if (!empty($fields['title'])){
      $body = $fields['title'];
    }
    $message = substr($body, 0, $space);
    $message .= ' ' . $fields['link'];



    try {

	  $setting = Engine_Api::_()->getDbTable('settings', 'core');	
	
      $config = array(
        'consumerKey' => $setting->getSetting('wall.service.twitter.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.twitter.consumersecret'),
      );

      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);

      $client = $access->getHttpClient($config);
      $client->setUri('https://api.twitter.com/1.1/statuses/update.json');
      $client->setMethod(Zend_Http_Client::POST);
      //$client->setParameterPost('status', $message);
      $client->setParameterPost('status', $message );


    Engine_Hooks_Dispatcher::getInstance()
      ->callEvent('onWallPostAction', array(
       'name' => $this->getName(),
       'token' => $token
     ));


      return $client->request()->getBody();

    } catch (Exception $e) { }

    return false;

  }

  public function stream(Wall_Model_Token $token, $params = array())
  {
    try {

	  $setting = Engine_Api::_()->getDbTable('settings', 'core');
	
      $config = array(
        'consumerKey' => $setting->getSetting('wall.service.twitter.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.twitter.consumersecret'),
      );

      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);

      $url = 'https://api.twitter.com/1.1/statuses/home_timeline.json';

      $client = $access->getHttpClient($config);
      $client->setUri($url);
      $client->setMethod(Zend_Http_Client::GET);

      foreach ($params as $key => $value){
        $client->setParameterGet($key, $value);
      }

      $body = $client->request()->getBody();

      if (!$body){
        return ;
      }
      $body = Zend_Json::decode($body);
      if (!$body){
        return ;
      }

      return $body;

    } catch (Exception $e) {}

    return ;
  }

  public function postStatus(Wall_Model_Token $token, $message)
  {
    try {

	  $setting = Engine_Api::_()->getDbTable('settings', 'core');
	
      $config = array(
        'consumerKey' => $setting->getSetting('wall.service.twitter.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.twitter.consumersecret'),
      );

      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);

      $client = $access->getHttpClient($config);
      $client->setUri('https://api.twitter.com/1.1/statuses/update.json');
      $client->setMethod(Zend_Http_Client::POST);
      $client->setParameterPost('status', $message);


    Engine_Hooks_Dispatcher::getInstance()
      ->callEvent('onWallPostStatus', array(
       'name' => $this->getName(),
       'token' => $token
     ));


      return $client->request()->getBody();

    } catch (Exception $e) { }

    return false;

  }


  public function favorite(Wall_Model_Token $token, $id)
  {
    try {

      $setting = Engine_Api::_()->getDbTable('settings', 'core');

      $config = array(
        'consumerKey' => $setting->getSetting('wall.service.twitter.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.twitter.consumersecret'),
      );

      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);

      $client = $access->getHttpClient($config);
      $client->setUri('https://api.twitter.com/1.1/favorites/create.json');
      $client->setParameterPost('id',$id);
      $client->setMethod(Zend_Http_Client::POST);

      return $client->request()->getBody();

    } catch (Exception $e) { }

    return false;

  }

  public function unfavorite(Wall_Model_Token $token, $id)
  {
    try {

      $setting = Engine_Api::_()->getDbTable('settings', 'core');

      $config = array(
        'consumerKey' => $setting->getSetting('wall.service.twitter.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.twitter.consumersecret'),
      );

      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);

      $client = $access->getHttpClient($config);
      $client->setUri('https://api.twitter.com/1.1/favorites/destroy.json');
      $client->setParameterPost('id', $id);
      $client->setMethod(Zend_Http_Client::POST);

      return $client->request()->getBody();

    } catch (Exception $e) { }

    return false;

  }


  public function destroy(Wall_Model_Token $token, $id)
  {
    try {

      $setting = Engine_Api::_()->getDbTable('settings', 'core');

      $config = array(
        'consumerKey' => $setting->getSetting('wall.service.twitter.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.twitter.consumersecret'),
      );

      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);

      $client = $access->getHttpClient($config);
      $client->setUri('https://api.twitter.com/1.1/statuses/destroy/'. $id .'.json');
      $client->setMethod(Zend_Http_Client::POST);

      return $client->request()->getBody();

    } catch (Exception $e) { }

    return false;

  }


  public function retweet(Wall_Model_Token $token, $id)
  {
    try {

      $setting = Engine_Api::_()->getDbTable('settings', 'core');

      $config = array(
        'consumerKey' => $setting->getSetting('wall.service.twitter.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.twitter.consumersecret'),
      );

      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);

      $client = $access->getHttpClient($config);
      $client->setUri('https://api.twitter.com/1.1/statuses/retweets/' . $id . '.json' );
      $client->setMethod(Zend_Http_Client::POST);

      return $client->request()->getBody();

    } catch (Exception $e) { }

    return false;

  }

  public function reply(Wall_Model_Token $token, $message, $status_id)
  {
    try {

      $setting = Engine_Api::_()->getDbTable('settings', 'core');

      $config = array(
        'consumerKey' => $setting->getSetting('wall.service.twitter.consumerkey'),
        'consumerSecret' => $setting->getSetting('wall.service.twitter.consumersecret'),
      );

      $access = new Zend_Oauth_Token_Access();
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_PARAM_KEY, $token->oauth_token);
      $access->setParam(Zend_Oauth_Token_Access::TOKEN_SECRET_PARAM_KEY, $token->oauth_token_secret);

      $client = $access->getHttpClient($config);
      $client->setUri('https://api.twitter.com/1.1/statuses/update.json');
      $client->setMethod(Zend_Http_Client::POST);
      $client->setParameterPost('status', $message);
      $client->setParameterPost('in_reply_to_status_id', $status_id);

      return $client->request()->getBody();

    } catch (Exception $e) { }

    return false;

  }

//


}
 
