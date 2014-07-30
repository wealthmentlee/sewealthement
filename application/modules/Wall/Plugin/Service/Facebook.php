<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Facebook.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Service_Facebook extends Wall_Plugin_Service_Abstract
{
  public function check(Wall_Model_Token $token)
  {
    if (!is_null($this->is_enabled)){
      return $this->is_enabled;
    }

    $url = 'https://graph.facebook.com/me?access_token=' . $token->oauth_token;
    $content = Engine_Api::_()->wall()->getUrlContent($url, null, true);

    if (!$content){
      $this->is_enabled = false;
      return $this->is_enabled;
    }
    $profile_info = Zend_Json::decode($content);

    if (!$profile_info || isset($profile_info['error'])){
      $this->is_enabled = false;
      return $this->is_enabled;
    }

    $this->is_enabled = true;
    return $this->is_enabled;

  }

  public function postAction(Wall_Model_Token $token, Activity_Model_Action $action, User_Model_User $user)
  {
    $fields = Engine_Api::_()->wall()->getActionFields($action);

    $data = array();

    if (!empty($fields['message'])){
      $data['message'] = $fields['message'];
    }
    if (!empty($fields['link'])){
      $data['link'] = $fields['link'];
    }
    if (!empty($fields['title'])){
      $data['name'] = $fields['title'];
    }
    if (!empty($fields['description'])){
      $data['description'] = $fields['description'];
    }
    if (!empty($fields['picture'])){
      $data['picture'] = $fields['picture'];
    }
/*    if (!empty($fields['host'])){
      $data['source'] = $fields['host'];
    }*/



    $url = "https://graph.facebook.com/me/feed?access_token=" . $token->oauth_token . "&format=json-strings";

    $result = Engine_Api::_()->wall()->getUrlContent($url, $data, true);
    $result = Zend_Json::decode($result);


    Engine_Hooks_Dispatcher::getInstance()
      ->callEvent('onWallPostAction', array(
       'name' => $this->getName(),
       'token' => $token
     ));


    return $result;

  }


  public function postStatus(Wall_Model_Token $token, $message)
  {
    $url = "https://api.facebook.com/method/stream.publish?access_token=" . $token->oauth_token . "&format=json-strings";

    $result = Engine_Api::_()->wall()->getUrlContent($url, array('message' => $message), true);
    $result = Zend_Json::decode($result);

    Engine_Hooks_Dispatcher::getInstance()
      ->callEvent('onWallPostStatus', array(
       'name' => $this->getName(),
       'token' => $token
     ));

    return $result;
  }

  public function stream(Wall_Model_Token $token, $params = array())
  {
    $url = 'https://graph.facebook.com/me/home?access_token=' . $token->oauth_token . '&format=json-strings';

    foreach ($params as $key => $value){
      $url .= "&$key=$value";
    }

    $content = Engine_Api::_()->wall()->getUrlContent($url, null, true);

    if (!$content){
      return false;
    }
    $stream = Zend_Json::decode($content);
    return $stream;

  }


/*  public function isActive()
  {
    $setting = Engine_Api::_()->getDbTable('settings', 'core');
    $clientid = $setting->getSetting('wall.service.facebook.clientid');
    $clientsecret = $setting->getSetting('wall.service.facebook.clientsecret');

    if (empty($clientid) || empty($clientsecret)){
      return false;
    }
    return true;

  }*/




  public function getPages($tokenRow)
  {
    try {

      $url = 'https://graph.facebook.com/me/accounts?access_token=' . $tokenRow->oauth_token . '&format=json-strings';

      $client = new Zend_Http_Client($url, array());
      $response = $client->request(Zend_Http_Client::GET);
      if ($response->isSuccessful()){
        $data = Zend_Json::decode($response->getBody());
        if (!empty($data) && !empty($data['data'])){

          $pages = array();
          foreach ($data['data'] as $item){
            if (!empty($item['category']) && $item['category'] == 'Application'){
              continue ;
            }
            $pages[] = array(
              'fbpage_id' => $item['id'],
              'user_id' => $tokenRow->user_id,
              'title' => $item['name'],
              'access_token' => $item['access_token']
            );
          }

          if (!empty($pages)){

            $fbPageTable = Engine_Api::_()->getDbTable('fbpages', 'wall');
            $fbPageTable->delete(array(
              'user_id = ?' => $tokenRow->user_id,
            ));


            foreach ($pages as $item){
              $fbPageTable->createRow()->setFromArray($item)->save();
            }

            $fb_pages = array();
            foreach ($fbPageTable->fetchAll(array('user_id = ?' => $tokenRow->user_id)) as $item){
              $fb_pages[] = array(
                'fbpage_id' => $item->fbpage_id,
                'title' => $item->title,
              );
            }

            return $fb_pages;

          }

        }

      }

    } catch (Exception $e){
    }

  }




}