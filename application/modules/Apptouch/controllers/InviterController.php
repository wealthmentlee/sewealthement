<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 24.07.12
 * Time: 11:36
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_InviterController extends Apptouch_Controller_Action_Bridge
{
    /*********** Index controller ***********************/
    public function indexInit()
    {
    }

    public function indexIndexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->addPageInfo('fb_app_id', $settings->getSetting('inviter.facebook.consumer.key', false));
        $auth_table = Engine_Api::_()->getDbTable('permissions', 'authorization');
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        if (!$auth_table->isAllowed('inviter', $viewer, 'use')) {
            return $this->_redirect($host_url . $this->view->url(array(), 'default'));
        }

        $fb_settings = Engine_Api::_()->inviter()->getFacebookSettings($this->view);

        $session = new Zend_Session_Namespace('inviter');

        $this->view->success = $session->__get('success', false);
        $this->view->message = $session->__get('message', false);
        $session->__set('success', false);
        $session->__set('message', false);

        $providers = Engine_Api::_()->inviter()->getIntegratedProviders();
        $count = count($providers);

        $this->add($this->component()->html($this->_getInviterMenu()));
        $this->add($this->component()->inviter($providers));

        $form_write = new Inviter_Form_Write();
        $form_write->getElement('submit_contacts')->setAttrib('onClick', '');
        $this->add($this->component()->navigation('main'));
        $this->add($this->component()->form($form_write));
        $this->renderContent();
    }

    public function indexContactsAction()
    {
        $this->setPageTitle('Contacts');
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
        $viewer = $this->_helper->api()->user()->getViewer();

        $provider = '';
        $session = new Zend_Session_Namespace('inviter');

        if ($session->__isset('provider')) {
            $provider = $session->__get('provider');
        }
        elseif (isset($_REQUEST['provider']) && $_REQUEST['provider'] == 'facebook') {
            $session->__set('provider', $_REQUEST['provider']);
            $provider = $session->__get('provider');
        }

        $provider = $providerApi->checkProvider($provider);
        $provider = strtolower(str_replace('.', '', $provider));
        $provider = strtolower(str_replace('!', '', $provider));

        $host = $_SERVER['HTTP_HOST'];

        if ($providerApi->checkIntegratedProvider($provider)) {
            /**
             * @var $tokensTbl Inviter_Model_DbTable_Tokens
             */
            $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
            $token = $tokensTbl->getUserToken($viewer->getIdentity(), $provider);

            if ($token === false && $session->__isset('account_info')) {
                $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
                $token = $tokensTbl->getUserTokenByArray($access_token_params);
            }

            try {
                $contact_list = $providerApi->getNoneMemberContacts($token, $provider, 5000);
            } catch (Exception $e) {
                $this->add($this->component()->html("<h3>" . "This service is not available now. Please try later" . "</h3>"))->renderContent();
                return;
            }

            if ($contact_list === false) {
                return $this->redirect($host . $this->view->url(array(), 'inviter_general'));
            }

            switch ($provider) {
                case 'twitter':
                    $key = 'id';
                    $email = 'email';
                    break;
                case 'hotmail':
                    $key = 'nid';
                    $email = 'id';
                    break;
                //        case 'facebook':
                case 'yahoo':
                case 'lastfm':
                case 'gmail':
                case 'linkedin':
                case 'foursquare':
                case 'mailru':
                    $key = 'nid';
                    $email = 'id';
                    break;
                default:
                    $key = 'id';
                    $email = 'id';
                    break;
            }

            $contacts = array();
            foreach ($contact_list as $contact_info) {
                $contact_info['email'] = $contact_info[$email];
                $contacts[$contact_info[$key]] = $contact_info;
            }

            if (count($contacts) == 0) {
                return $this->redirect($host . $this->view->url(array(), 'inviter_general'));
            }
            $this->add($this->component()->inviterContactsList($contacts, null, array('provider' => $provider)))->renderContent();
            return;
        }
    }

    public function indexInvitationsendAction()
    {
        $contact_ids = $this->_getParam('contacts');
        $contact_ids = (is_array($contact_ids)) ? $contact_ids : explode('&', $contact_ids);

        $message = $this->_getParam('message');
        $translate = Zend_Registry::get('Zend_Translate');
        $session = new Zend_Session_Namespace('inviter');
        $viewer = Engine_Api::_()->user()->getViewer();
        /**
         * @var $providerApi Inviter_Api_Provider
         */
        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

        if (!Engine_Api::_()->authorization()->isAllowed('inviter', null, 'use') && count($contact_ids) == 0) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('INVITER_No contacts specified');
            return;
        }

        $session->__set('contact_ids', $contact_ids);
        $session->__set('message', $message);

        $provider = $session->__get('provider');
        $provider = $providerApi->checkProvider($provider);
        if ($providerApi->checkIntegratedProvider($provider)) {

            /**
             * @var $tokensTbl Inviter_Model_DbTable_Tokens
             */
            $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
            $token = $tokensTbl->getUserToken($viewer->getIdentity(), $provider);

            if ($token === false && $session->__isset('account_info')) {
                $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
                $token = $tokensTbl->getUserTokenByArray($access_token_params);
            }

            if ($provider == 'twitter') {
                $valid_msg_length = $providerApi->checkTwitterMessageLength($message);

                if (!$valid_msg_length) {
                    $this->view->message = $translate->_('INVITER_There was an error sending your message: The text length of your message is over the limit.');
                    return;
                }
            }
            $captcha_value = $this->getRequest()->getParam('captcha_value', false);
            $captcha_token = $this->getRequest()->getParam('captcha_token', false);

            $error_msg = $providerApi->sendInvites($token, $provider, $contact_ids, null, $captcha_value, $captcha_token);

            if ($provider == 'twitter') {
                if (isset($error_msg['twitter_step'])) {
                    while (isset($error_msg['twitter_step'])) {
                        $error_msg = $providerApi->sendInvites($token, $provider, $contact_ids, null, $captcha_value, $captcha_token);
                    }
                }
            }
            if (isset($error_msg['captcha_token']) && $provider == 'orkut') {
                $this->view->img_url = $img_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->baseUrl() . $providerApi->getOrkutCaptcha($token, $error_msg['captcha_url']);
                $this->view->captcha_token = $error_msg['captcha_token'];
                return;
            }
        }
        else {
            $error_msg = 'Unknown provider!';
        }

        if (true !== $error_msg) {
            $this->view->status = false;
            $this->view->msg = $error_msg;
        }
        else {
            $this->view->status = true;
            $this->view->msg = $translate->_('INVITER_Invitations sucessfully have been sent to your contacts.');
            $this->view->url = $this->view->url(array(), 'inviter_general');
        }
    }

    public function indexWriteContactsAction()
    {
        $recipients = $this->_getParam('recipients');
        $message = $this->_getParam('message');

        if (is_string($recipients)) {
            $recipients = preg_split("/[\s,]+/", $recipients);
        }

        if (is_array($recipients)) {
            $recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
        }

        $validate = new Zend_Validate_EmailAddress();
        $contacts = array();

        foreach ($recipients as $recipient)
        {
            $exploded = explode('@', $recipient);

            if ($validate->isValid($recipient) && is_array($exploded) && count($exploded) == 2) {
                $contacts[$recipient] = trim($exploded[0]);
            }
        }

        $translate = Zend_Registry::get('Zend_Translate');

        if (empty($contacts)) {
            $this->view->status = 0;
            $this->view->message = $translate->_(array(
                "INVITER_Failed!, incorrect email adress has been written.",
                "Failed!, incorrect email adresses have been written.",
                count($recipients)));
            return;
        }

        $viewer = $this->_helper->api()->user()->getViewer();
        $session = new Zend_Session_Namespace('inviter');
        $session->__set('sender', $viewer->email);
        $session->__set('contacts', $contacts);

        $page_id = $this->_getParam('page_id', null);
        if ($page_id) {
            $sent = (int)Engine_Api::_()->getApi('openinviter', 'inviter')->sendPageEmails($session, $message, $contacts, $page_id);
        } else {
            $sent = (int)Engine_Api::_()->getApi('openinviter', 'inviter')->sendEmails($session, $message, $contacts);
        }

        if ($sent > 0) {
            $this->view->status = 1;
            $this->view->message = $translate->_(array("INVITER_Invitation has been sent successfully.", "Invitations have been sent successfully.", $sent));
            $session->unsetAll();
            return;
        }

        $this->view->status = 2;
        $this->view->message = ($page_id)
            ? $translate->_("PAGE_INVITER_Written contact's already member.")
            : $translate->_(array("INVITER_Written contact's already member.", "Written contacts're already members.", count($recipients)));
        return;
    }

    private function _getInviterMenu()
    {
        $menu = $this->dom()->new_('div',
            array(
                'data-role' => 'controlgroup',
                'data-mini' => 'true',
                'data-type' => 'horizontal',
                'style' => 'text-align: center;')
        );
        $menu->append($this->dom()->new_('a',
            array(
                'id' => 'inviter-show-import',
                'class' => 'badges-tab-button ui-btn-active',
                'data-role' => 'button',
                'data-shadow' => true),
            $this->view->translate('INVITER_Import Your Contacts')))
            ->append($this->dom()->new_('a',
            array(
                'id' => 'inviter-show-write',
                'class' => 'badges-tab-button',
                'data-role' => 'button',
                'data-shadow' => true),
            $this->view->translate('INVITER_Write Your Contacts')));
        return '<br \>' . $menu;
    }

    /*********** Index controller ***********************/

    /*********** Oauth controller ***********************/
    private $config = array();
    private $tokensTbl;
    private $provider;
    private $providerApi;

    public function oauthInit()
    {
        $this->provider = $this->_getParam('provider', 'twitter');
        $this->tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
        $this->providerApi = Engine_Api::_()->getApi('provider', 'inviter');
        $this->config = $this->providerApi->getProviderConfig($this->provider);

        // set default callback url
        $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'default');
        $this->_setCallbackUrl($url);

        $session = new Zend_Session_Namespace('inviter');

        if ($this->_getParam('signup', false)) {
            $session->__set('inviter_signup', 1);
        }
    }

    public function oauthRequestAction()
    {
        $new_token = $this->_getParam('new', false);
        $viewer = $this->_helper->api()->user()->getViewer();
        $session = new Zend_Session_Namespace('inviter');
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $tokenRow = $this->tokensTbl->findUserToken($viewer->getIdentity(), $this->providerApi->checkProvider($this->provider));


        //                            $this->renderContent();return;

        if ($tokenRow && !$new_token) {
            //if (!$new_token) {
            //$this->view->tokenRow = $tokenRow;
            $this->add($this->component()->form($this->_generateForm($tokenRow)))->renderContent();
            return;
        }

        if ($this->provider == 'facebook') {
            $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
            $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
            $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
            $fbApi->init($app_id, $secret);
            $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'default');
            $redirect_url = $host_url . $url;

            $login_url = $fbApi->getLoginUrl($redirect_url, 'email');
            if ($new_token && $tokenRow) {
                $access_token = $tokenRow->toArray();
                $redirect_url = $host_url .
                    $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => $this->provider, 'new' => null), 'default');
                $logout_url = $fbApi->getLogoutUrl($access_token['oauth_token'], $redirect_url);
                $tokenRow->delete();
                $this->_redirect($logout_url);
            }
            else
                $this->_redirect($login_url);

        }
//        elseif ($this->provider == 'twitter') {
//            $auth_url = 'https://twitter.com/oauth/authorize';
//            $auth_url = 'https://twitter.com/oauth/request_token';
//            return $this->redirect($auth_url);
//        }
        elseif ($this->provider == 'hotmail') {
            $this->_getHotmailRequest($tokenRow, $new_token);
        }
        elseif ($this->provider == 'lastfm') {
            $api_key = $settings->getSetting('inviter.lastfm.api.key');
            $auth_url = 'http://www.last.fm/api/auth/?api_key=' . $api_key;
            return $this->redirect($auth_url);
        }
        elseif ($this->provider == 'foursquare') {
            $client_id = $settings->getSetting('inviter.foursquare.consumer.key');

            $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'default');
            $redirect_url = $host_url . $url;

            $auth_url = 'https://foursquare.com/oauth2/authenticate?client_id=' . $client_id . '&response_type=code&redirect_uri=' . $redirect_url;
            if ($new_token) {
                return $this->_redirect($auth_url);
            }
            else
                return $this->_redirect($auth_url);
        }
        elseif ($this->provider == 'mail.ru') {
            $client_id = $settings->getSetting('inviter.mailru.id');
            $secret = $settings->getSetting('inviter.mailru.secret.key');

            $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => 'mailru'), 'default');
            $redirect_url = $host_url . $url;

            $auth_url = 'https://connect.mail.ru/oauth/authorize' . '?client_id=' . $client_id . '&response_type=code&redirect_uri=' . $redirect_url . '&scope=messages';

            $logout_url = 'http://auth.mail.ru/cgi-bin/logout?Page=' . urlencode($auth_url);
            if ($new_token)
                $this->_redirect($logout_url);
            else {
                $this->_redirect($auth_url);
            }

        }
        elseif ($this->provider == 'aol') {
            $aol_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_AOL');
            $aol_plugin->init();
            $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => 'aol', 'format' => null), 'default');
            $auth_url = $aol_plugin->getLoginUrl($redirect_url);
            $this->_redirect($auth_url);

        }
        $consumer = new Zend_Oauth_Consumer($this->config);

        try {
            if ($this->provider == 'gmail') {
                $token = $consumer->getRequestToken(array('scope' => 'http://www.google.com/m8/feeds/'));
            } elseif ($this->provider == 'orkut') {
                $token = $consumer->getRequestToken(array('scope' => 'http://orkut.gmodules.com/social/'));
            } else {
                $token = $consumer->getRequestToken();
            }
        }
        catch (Exception $e) {
            //            $this->add($this->component()->html("<h3>" . $e->getMessage() . "</h3>"))->renderContent();
            $this->add($this->component()->html("<h3>" . "This service is not available now. Please try later" . "</h3>"))->renderContent();
            return;
        }

        $session->__set('token_request', array(
            'oauth_token' => $token->getParam('oauth_token'),
            'oauth_token_secret' => $token->getParam('oauth_token_secret'),
            'oauth_callback_confirmed' => $token->getParam('oauth_callback_confirmed')
        ));

        //        return;

        return $this->redirect($consumer->getRedirectUrl());
    }

    public function oauthAccessAction()
    {
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $token = null;
        $viewer = $this->_helper->api()->user()->getViewer();
        $params = $this->_getAllParams();

        if (isset($params['denied']) && $params['denied']) {
            $this->add($this->component()->html(
                '<h3 style="padding: 15px 10px 0">' . $this->view->translate('Our Network') . '</h3>
                <p class="no_content" style="">' . $this->view->translate("OK, you've denied Our Network access to interact with your account!") . '</p>'
            ))->renderContent();
            return;
        }

        $url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'callback', 'provider' => $this->provider), 'default');

        $this->_setCallbackUrl($url);

        $session = new Zend_Session_Namespace('inviter');

        if ($this->provider == 'facebook') {
            $code = $this->_getParam('code', null);
            $error = $this->_getParam('error', false);
            if ($error) {
                $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'format' => null, 'provider' => 'facebook'), 'default');
                $this->_redirect($redirect_url);
            }
            if ($code) {
                $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'default');
                $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => 'facebook'), 'default');
                // '/inviter/oauth/access/format/smoothbox/provider/facebook';

                $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
                $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
                $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
                $fbApi->init($app_id, $secret);

                $token = $fbApi->getAccessToken($redirect_url, $code);

                if ((!$token))
                    $this->_redirect($host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => $this->provider), 'default'));
            }

        }
        elseif ($this->provider == 'hotmail') {
        }
        elseif ($this->provider == 'lastfm') {
            $token = $this->_getParam('token', null);
        }
        elseif ($this->provider == 'foursquare') {
            $code = $this->_getParam('code', null);
            if ($code) {
                $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => 'foursquare'), 'default');
                $redirect_url = $host_url . $url;
                $four_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_Foursquare');
                $four_plugin->init();
                $token = $four_plugin->getAccessToken($code, $redirect_url);
            }
        }
        elseif ($this->provider == 'mailru') {
            $old = $this->_getParam('old', null);
            if (!$old) {
                $code = $this->_getParam('code', null);
                if ($code) {
                    $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => 'mailru'), 'default');
                    $redirect_url = $host_url . $url;
                    $mail_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_MyMail');
                    $mail_plugin->init();
                    $token = $mail_plugin->getAccessToken($redirect_url, $code);
                }
            } else {
                $tokenRow = $this->tokensTbl->findUserToken($viewer->getIdentity(), $this->providerApi->checkProvider($this->provider));
                $token = $tokenRow->getParam('oauth_token');
            }
        }
        elseif ($this->provider == 'aol') {
            $code = $this->_getParam('statusCode', false);
            if ($code == 200) {
                $token = $this->_getParam('token_a', false);
            } else {
                exit('Invalid callback request. Oops. Sorry.');
            }
        }
        elseif (!empty($params) && $session->__isset('token_request')) {
            if ($this->provider == 'yahoo') {
                sleep(1);
            }
            $token_request_params = $session->__get('token_request');

            $requestToken = new Zend_Oauth_Token_Request();
            $requestToken->setParams($token_request_params);
            $consumer = new Zend_Oauth_Consumer($this->config);

            $token = $consumer->getAccessToken($params, $requestToken);
            $token_access_params = ($this->provider == 'twitter')
                ? array(
                    'oauth_token' => $token->getParam('oauth_token'),
                    'oauth_token_secret' => $token->getParam('oauth_token_secret'),
                    'user_id' => $token->getParam('object_id'),
                    'screen_name' => $token->getParam('object_name')
                )
                : array(
                    'oauth_token' => $token->getParam('oauth_token'),
                    'oauth_token_secret' => $token->getParam('oauth_token_secret')
                );

            $session->__set('access_token', Zend_Json::encode($token_access_params));

            // Now that we have an Access Token, we can discard the Request Token
            $session->__unset('token_request');

        }
        else {
            return;
            exit('Invalid callback request. Oops. Sorry.');
        }

        $account_info = $this->_getAccountInfo($token);
        //
        //        return;

        //return;

        // check fb session expired
        if ($account_info === false) {
            $params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => $this->provider);
            return $this->redirect($this->view->url($params, 'default'));
        }

        if (!$account_info) {
            $this->add($this->component()->html('<h3>"This service is not available now. Please try later."</h3>'))->renderContent();
            return;
        }

        if ($viewer->getIdentity() != 0) {
            // delete duplicates
            $this->tokensTbl->delete(array("user_id = {$account_info['user_id']}", "object_id = '{$account_info['object_id']}'", "provider = '{$account_info['provider']}'"));

            // update tokens
            $tokensSel = $this->tokensTbl->select()
                ->where('user_id = ?', $account_info['user_id'])
                ->where('provider = ?', $account_info['provider'])
                ->where('active = ?', 1);

            $otherTokens = $this->tokensTbl->fetchAll($tokensSel);
            foreach ($otherTokens as $otherToken) {
                $otherToken->active = 0;
                $otherToken->save();
            }

            $tokenRow = $this->tokensTbl->createRow();
            $tokenRow->setFromArray($account_info);

            $tokenRow->save();
        } else {
            unset($account_info['creation_date']);
            $session->__set('account_info', Zend_Json::encode($account_info));
        }
        $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'callback', 'provider' => $this->provider), 'default');
        $this->_redirect($host_url . $url);
    }

    public function oauthCallbackAction()
    {
        $viewer = $this->_helper->api()->user()->getViewer();
        $session = new Zend_Session_Namespace('inviter');
        $this->view->provider = $this->provider;

        /**
         * @var $providerApi Inviter_Api_Provider
         */
        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
        $token = $this->tokensTbl->getUserToken($viewer->getIdentity(), $providerApi->checkProvider($this->provider));

        if ($token === false && $session->__isset('account_info')) {
            $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
            $token = $this->tokensTbl->getUserTokenByArray($access_token_params);
        }

        $contacts = $providerApi->getContacts($token, $providerApi->checkProvider($this->provider));
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        if ($contacts === false) {
            $this->tokensTbl->delete(array("user_id = {$viewer->getIdentity()}", "provider = '{$this->provider}'", "active = 1"));
            return $this->redirect($host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => $this->provider), 'default'));
        } else {
            $session->__set('provider', $this->provider);
            $session->__set('user_id', $token->getParam('user_id'));
            return $this->redirect($host_url . $this->view->url(array(), 'inviter_contacts'));
        }

        //        $this->view->contact_count = count($contacts);
        //        $this->view->signup_page = (int)($session->__isset('inviter_signup') && $session->__get('inviter_signup'));

        //        $params = $this->_getAllParams();
        //        if ($params['provider'] == 'facebook') {
        //          if (isset($params['way']) && $params['way'] == '1') {
        //            $url = $this->view->url(array('module' => 'inviter', 'controller' => 'facebook', 'action' => 'response', 'state' => true), 'default');
        //            $this->_redirect($url);
        //          }
        //        }
    }

    private function _getAccountInfo($token)
    {
        $session = new Zend_Session_Namespace('inviter');
        $viewer = $this->_helper->api()->user()->getViewer();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $user_info = array(
            'user_id' => $viewer->getIdentity(),
            'provider' => $this->provider,
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'active' => 1,
        );

        switch ($this->provider)
        {
            case 'twitter' :

                $user_info['oauth_token'] = $token->getParam('oauth_token');
                $user_info['oauth_token_secret'] = $token->getParam('oauth_token_secret');
                $user_info['object_id'] = $token->getParam('user_id');
                $user_info['object_name'] = $token->getParam('screen_name');

                break;

            case 'linkedin':

                $client = $token->getHttpClient($this->config);
                $client->setUri('http://api.linkedin.com/v1/people/~:(id,first-name,last-name)');
                $client->setMethod(Zend_Http_Client::GET);
                $response = $client->request();

                $status = $response->getStatus();

                if ($status != 200) {
                    return false;
                }

                $content = $response->getBody();
                $xml = simplexml_load_string($content);

                $user_info['oauth_token'] = $token->getParam('oauth_token');
                $user_info['oauth_token_secret'] = $token->getParam('oauth_token_secret');
                $user_info['object_id'] = $xml->{'id'} . '';
                $user_info['object_name'] = $xml->{'first-name'} . ' ' . $xml->{'last-name'};

                break;

            case 'facebook':
                $res = $this->_getFacebookUserInfo($token);
                $user_info = array_merge($res, $user_info);
                break;

            case 'gmail':

                $client = $token->getHttpClient($this->config);
                $client->setUri('https://www.google.com/m8/feeds/contacts/default/thin');
                $client->setMethod(Zend_Http_Client::GET);
                $client->setParameterGet('max-results', 0);

                $response = $client->request();
                $status = $response->getStatus();

                if ($status != 200) {
                    return false;
                }

                $content = $response->getBody();
                $xml = simplexml_load_string($content);

                $user_info['oauth_token'] = $token->getParam('oauth_token');
                $user_info['oauth_token_secret'] = $token->getParam('oauth_token_secret');
                $user_info['object_id'] = $xml->{'author'}->{'email'} . '';
                $user_info['object_name'] = $xml->{'author'}->{'name'} . '';

                break;

            case 'orkut':

                $params = array();
                $params['userId'] = "@me";
                $params['groupId'] = "@self";

                $p = array();
                $p['method'] = 'people.get';
                $p['id'] = 'myself';
                $p['params'] = $params;

                $params_string = json_encode($p);

                $client = $token->getHttpClient($this->config);
                $client->setUri('http://www.orkut.com/social/rpc');
                $client->setMethod(Zend_Http_Client::POST);
                $client->setRawData($params_string);
                $client->setHeaders('Content-type', 'application/json');

                $response = $client->request();
                $status = $response->getStatus();

                if ($status != 200) {
                    return false;
                }

                $content = json_decode($response->getBody());

                $name = $content->data->name->familyName . ' ' . $content->data->name->givenName;
                $user_info['oauth_token'] = $token->getParam('oauth_token');
                $user_info['oauth_token_secret'] = $token->getParam('oauth_token_secret');
                $user_info['object_id'] = $content->data->id;
                $user_info['object_name'] = (trim($name) != "") ? $name : '_empty_';
                break;

            case 'yahoo':

                $client = $token->getHttpClient($this->config);
                $client->setUri('http://query.yahooapis.com/v1/yql');
                $client->setMethod(Zend_Http_Client::GET);
                $client->setParameterGet('q', 'select * from social.profile where guid = me');
                $client->setParameterGet('format', 'json');

                $response = $client->request();
                $status = $response->getStatus();

                if ($status != 200) {
                    return false;
                }

                $content = $response->getBody();
                $content = Zend_Json::decode($content, Zend_Json::TYPE_ARRAY);
                $yahoo_account = isset($content['query']['results']['profile']) ? $content['query']['results']['profile'] : array();
                if (!$yahoo_account) {
                    return false;
                }

                $emails = isset($yahoo_account['emails']) ? $yahoo_account['emails'] : array();
                $yahoo_email = false;
                foreach ($emails as $email) {
                    if (isset($email['primary']) && $email['primary']) {
                        $yahoo_email = $email['handle'];
                        break;
                    }
                }

                if (!$yahoo_email) {
                    return false;
                }

                $user_info['oauth_token'] = $token->getParam('oauth_token');
                $user_info['oauth_token_secret'] = $token->getParam('oauth_token_secret');
                $user_info['object_id'] = $yahoo_email;
                $user_info['object_name'] = $yahoo_account['nickname'];

                break;

            case 'hotmail':

                $return_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'inviter_ru') . '/provider/hotmail';
                $privacy_url = $host_url . '/core/help/privacy';

                $wll = Engine_Api::_()->loadClass('Inviter_Plugin_WindowsLiveLogin');
                $params = array('appid' => $settings->getSetting('inviter.hotmail.consumer.key', false),
                    'secret' => $settings->getSetting('inviter.hotmail.consumer.secret', false),
                    'securityalgorithm' => 'wsignin1.0',
                    'returnurl' => $return_url,
                    'policyurl' => $privacy_url
                );
                $wll = Inviter_Plugin_WindowsLiveLogin::initMe($params);
                $wll->setDebug(false);

                $token = $this->_getParam('ConsentToken', null);

                $ct = $wll->processConsentToken($token);
                if ($ct && !$ct->isValid()) {
                    $ct = null;
                }
                if ($ct) {
                    $cid = $ct->getLocationID();
                    $delegationToken = $ct->getDelegationToken();

                    $httpHeaders = array("Authorization: DelegatedToken dt=\"{$delegationToken}\"");
                    $options = array(
                        CURLOPT_URL => "https://livecontacts.services.live.com/users/@L@" . $cid . "/rest/LiveContacts",
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_HEADER => true,
                        CURLOPT_HTTPGET => true,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => $httpHeaders
                    );

                    $ch = curl_init();
                    curl_setopt_array($ch, $options);
                    $response = curl_exec($ch);

                    $xml_start = strpos($response, '<?xml');

                    $xml = substr($response, $xml_start);

                    $st = new SimpleXMLElement($xml);
                    $user = $st->Owner;

                    $token_access_params = array(
                        'oauth_token' => $delegationToken,
                        'oauth_token_secret' => $cid
                    );

                    $session->__set('access_token', Zend_Json::encode($token_access_params));
                    $session->__unset('token_request');

                    $user_info['oauth_token'] = $delegationToken;
                    $user_info['oauth_token_secret'] = $cid;
                    $user_info['object_id'] = $user->WindowsLiveID . '';
                    $user_info['object_name'] = $user->Profiles->Personal->DisplayName . '';
                }

                break;

            case 'lastfm':
                $api_key = $settings->getSetting('inviter.lastfm.api.key');
                $secret = $settings->getSetting('inviter.lastfm.secret');

                $lastfm = Engine_Api::_()->loadClass('Inviter_Plugin_Lastfm');

                $params = array();
                $params['token'] = $token;
                $params['method'] = 'auth.getsession';
                $params['api_key'] = $api_key;
                $signature = $lastfm->sig($params, $secret);
                $params['api_sig'] = $signature;

                $result = $lastfm->make_request($params);
                $name = $result->session->name . '';
                $sk = $result->session->key . '';

                $token_access_params = array(
                    'oauth_token' => $token,
                    'oauth_token_secret' => $sk
                );

                $session->__set('access_token', Zend_Json::encode($token_access_params));
                $session->__unset('token_request');

                $params = array();
                $params['method'] = 'user.getinfo';
                $params['api_key'] = $api_key;
                $params['user'] = $name;
                $result = $lastfm->make_request($params);
                $id = $result->user->id . '';

                $user_info['oauth_token'] = $token;
                $user_info['oauth_token_secret'] = $sk;
                $user_info['object_id'] = $id;
                $user_info['object_name'] = $name;

                break;

            //            case 'myspace':
            //                $res = $this->_getMyspaceUserInfo($token);
            //                $user_info = array_merge($res, $user_info);
            //                break;

            case 'foursquare':
                $four_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_Foursquare');
                $user = $four_plugin->getUser($token);
                $info = $four_plugin->getUserInfo($user);
                $info['oauth_token'] = $token;
                $user_info = array_merge($info, $user_info);
                break;

            case 'mailru':
                $mail_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_MyMail');
                $mail_plugin->init();
                $user = $mail_plugin->getUser($token);
                $info = $mail_plugin->getUserInfo($user);
                $info['oauth_token'] = $token;
                $user_info = array_merge($info, $user_info);
                break;

            case 'aol':
                $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => 'aol', 'format' => null), 'default');
                $aol_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_AOL');
                $aol_plugin->init();

                $info = $aol_plugin->getUser($token, null, $redirect_url);
                $info['oauth_token'] = $token;
                $user_info = array_merge($info, $user_info);
                break;

            default:
                break;
        }

        return $user_info;
    }

    private function _getFacebookRequest($tokenRow)
    {
        $new_token = $this->_getParam('new', false);
        $facebook = Inviter_Api_Provider::getFBInstance();

        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $next = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider);
        $cancel = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider, 'denied' => 1);

        if ($new_token) {
            $next['new'] = '0';
            $cancel['new'] = '0';

            $this->_redirect($facebook->getLogoutUrl(array(
                'display' => 'popup',
                'next' => $host_url . $this->view->url($next, 'default'),
                'cancel_url' => $host_url . $this->view->url($cancel, 'default')
            )), array('exit' => true));
        }

        try {
            $fb_user_id = Inviter_Api_Provider::getFBUserId();
        } catch (Exception $e) {
            $fb_user_id = 0;
        }

        $account_info = false;
        // Session based graph API call.
        if ($fb_user_id) {
            try {
                $access_token = $facebook->getAccessToken();

                $account_info = $facebook->api('/me');

            } catch (Exception $e) {
                $access_token = false;
            }
        }

        if ($account_info && $tokenRow && $account_info['id'] == $tokenRow->object_id) {
            return true;
        } elseif ($tokenRow) {
            $tokenRow->active = 0;
            $tokenRow->save();
        }

        if (isset($access_token) && $access_token) {
            $next['session'] = $access_token;
            $this->_redirect($host_url . $this->view->url($next, 'default'), array('exit' => true));
        } else {
            $this->_redirect($facebook->getLoginUrl(array(
                'display' => 'popup',
                'next' => $host_url . $this->view->url($next, 'default'),
                'cancel_url' => $host_url . $this->view->url($cancel, 'default')
            )), array('exit' => true));
        }

        return;
    }

    private function _getFacebookUserInfo($token)
    {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
        $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
        $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
        $fbApi->init($app_id, $secret);
        $me = $fbApi->getMe($token);

        $user_info = array();
        $user_info['oauth_token'] = $token;
        $user_info['oauth_token_secret'] = $secret;
        $user_info['object_id'] = $me->id;
        $user_info['object_name'] = $me->name;

        return $user_info;
    }

    private function _getHotmailRequest($tokenRow, $new_token)
    {
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

        $return_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'inviter_ru') . '/provider/hotmail';
        $privacy_url = $host_url . '/core/help/privacy';

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $params = array('appid' => $settings->getSetting('inviter.hotmail.consumer.key', false),
            'secret' => $settings->getSetting('inviter.hotmail.consumer.secret', false),
            'securityalgorithm' => 'wsignin1.0',
            'returnurl' => $return_url,
            'policyurl' => $privacy_url
        );
        $wll = Engine_Api::_()->loadClass('Inviter_Plugin_WindowsLiveLogin');

        $wll = Inviter_Plugin_WindowsLiveLogin::initMe($params);
        $wll->setDebug(false);
        $consenturl = $wll->getConsentUrl('ContactsSync.FullSync');

        if ($tokenRow && $new_token) {
            $tokenRow->delete();
        }

        $this->_redirect($consenturl, array('exit' => true));
    }

    private function _generateForm($tokenRow)
    {
        $form = new Engine_Form();

        $form->setDisableTranslator(true);

        $params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'callback');
        $form->setAction($this->view->url($params, 'default'));
        $form->setTitle(Zend_Registry::get('Zend_Translate')->_('INVITER_Confirm Account'));
        $form->getDecorator('Description')->setOption('escape', false);

        $object_name = ($tokenRow->provider != 'gmail' && $tokenRow->provider != 'yahoo') ? $tokenRow->object_name : "{$tokenRow->object_name} ({$tokenRow->object_id})";
        $form->setDescription($this->view->translate('INVITER_FORM_CONFIRM_ACCOUNT_DESC', $object_name));
        $provider = $tokenRow->provider;
        $form->addElement('Button', 'submit', array(
            'type' => 'submit',
            'label' => 'INVITER_Continue'
        ));
        $params['action'] = 'request';
        $params['new'] = 1;
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $url = $host_url . $this->view->url($params, 'default');
        if ($provider == 'hotmail') {
            $wll = Engine_Api::_()->loadClass('Inviter_Plugin_WindowsLiveLogin');
            $settings = Engine_Api::_()->getDbTable('settings', 'core');
            $params = array('appid' => $settings->getSetting('inviter.hotmail.consumer.key', false),
                'secret' => $settings->getSetting('inviter.hotmail.consumer.secret', false),
                'securityalgorithm' => 'wsignin1.0'
            );
            $wll = Inviter_Plugin_WindowsLiveLogin::initMe($params);
            $logout_url = $wll->getTrustedLogoutUrl();
            $form->addElement('Cancel', 'cancel', array(
                'label' => 'INVTTER_Use another account',
                'link' => true,
                'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
                'href' => "javascript:void(0);",
                'onclick' => "inviter.live_logout('" . $logout_url . "','" . $url . "');",
                'decorators' => array(
                    'ViewHelper'
                )
            ));
        } else {
            $form->addElement('Cancel', 'cancel', array(
                'label' => 'INVTTER_Use another account',
                'link' => true,
                'id' => 'test',
                'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
                'href' => $url,
                'decorators' => array(
                    'ViewHelper'
                )
            ));
        }
        $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        return $form;
    }

    public function _setCallbackUrl($url)
    {
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $this->config['callbackUrl'] = $host_url . $url;
    }

    /*********** Oauth controller ***********************/


    /*********** Invitations controller *********************/
    public function invitationsIndexAction()
    {
        $session = new Zend_Session_Namespace('inviter');

        if ($session->__isset('invites_del_msg')) {
            $this->view->has_msg = true;
            $this->view->msg = $session->__get('invites_del_msg');
            $this->view->msg_type = $session->__get('invites_del_msg_type');
            $session->__unset('invites_del_msg');
            $session->__unset('invites_del_msg_type');
        }

        $table = $this->_helper->api()->getDbtable('invites', 'inviter');

        $user = Engine_Api::_()->user()->getViewer();

        $search = $this->_getParam('search', false);
        $params = array(
            'user_id' => $user->getIdentity(),
            'page' => $this->_getParam('page'),
            'date' => 'ASC',
            'provider' => '',
            'ipp' => 10,
        );

        $params['recipient'] = ($search) ? $search : "";

        $form = $this->getSearchForm();
        $form->setMethod('get');
        $form->getElement('search')->setValue($this->_getParam('search'));

        // Make paginator
        $invites_paginator = $invites_paginator = Engine_Api::_()->getDbTable('invites', 'inviter')->getInvitesPaginator($params);
        $this->setPageTitle('My Invitations');
        $this->add($this->component()->navigation('main'));
        $this->add($this->component()->itemSearch($form));
        $this->add($this->component()->inviterInvitesList($invites_paginator));
        $this->add($this->component()->paginator($invites_paginator));
        $this->renderContent();
    }

//
//    public function invitationsSendnewAction()
//    {
//        $viewer = Engine_Api::_()->user()->getViewer();
//        if (!$viewer->getIdentity()) {
//            return $this->redirect($this->view->url(array(), 'default', true));
//        }
//
//        $invite_id = $this->_getParam('id', null);
//        $invite = Engine_Api::_()->inviter()->getInvitation($invite_id);
//        $form = new Inviter_Form_Sendnew(array('params' => $invite_id));
//        $form->setAttrib('action', 'inviter/index/invitationsend');
//        $this->add($this->component()->form($form));
//
//        $inv = $form->_inv->toArray();
//        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
//        $conf = $providerApi->getProviderConfig($form->_inv->provider);
//
//        $invitation = array (
//            'sender' => $form->_inv->sender,
//            'recipient' => $form->_inv->recipient,
//            'recipient_name' => $form->_inv->recipient_name,
//            'provider' => $form->_inv->provider
//        );
//
//        if ( $this->getRequest()->isPost() ) {
//        }
//
////        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
////            $this->_success = $form->sendNewInvite();
////        }
////
////        if ($this->getRequest()->isPost() && !$form->isErrors()) {
////            return $this->redirect($this->view->url(array('module' => 'inviter', 'controller' => 'invitations', 'action' => 'index'), 'default', true));
////        }
//        $this->renderContent();
//    }
//

    public function invitationsDeleteAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return $this->redirect($this->view->url(array(), 'default', true));
        }

        $id = $this->_getParam('id', null);
        $invitation = Engine_Api::_()->inviter()->getInvitation($id);
        $form = new Inviter_Form_Delete();
        $this->add($this->component()->form($form));

        if ($this->getRequest()->isPost()) {
            $invitation->delete();
            return $this->redirect($this->view->url(array('module' => 'inviter', 'controller' => 'invitations', 'action' => 'index'), 'default', true));
        }
        $this->renderContent();
    }

    /*********** Invitations controller *********************/


    /*********** Referrals controller ***********************/
    public function referralsIndexAction()
    {
        $this->setPageTitle('Referrals');
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return $this->_redirect($host_url . $this->view->url(array(), 'default'));
        }

        $auth_table = Engine_Api::_()->getDbTable('permissions', 'authorization');
        if (!$auth_table->isAllowed('inviter', $viewer, 'use')) {
            return $this->_redirect($host_url . $this->view->url(array(), 'default'));
        }
        $this->view->filter_form = $filter_form = new Inviter_Form_ReferralsFilter();

        $params = array(
            'user_id' => $viewer->getIdentity(),
            'ipp' => 10,
            'page' => $this->_getParam('page', 1)
        );

        if ($this->getRequest()->isPost() && $filter_form->isValid($this->_getAllParams())) {
            $params = array_merge($params, $this->_getAllParams());
        }

        $inviterTable = $this->_helper->api()->getDbtable('invites', 'inviter');
        $inviterSelect = $inviterTable->select('new_user_id')->where('user_id = ? && new_user_id != 0', $viewer->getIdentity());

        $table = Engine_Api::_()->getItemTable('page');
        $select = $table->select()->where("search = 1")->order(' DESC');

        if ($this->_getParam('search', false)) {
            $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
        }

        $users = array();
        $search = $this->_getParam('search', false);
        foreach ($inviterTable->fetchAll($inviterSelect) as $inviter) {
            $user = $this->view->item('user', $inviter->new_user_id);
            if ($search) {
                if (!strstr($user->getTitle(), $search)) continue;
            }
            $users[] = $user;
        }
        $referrals_paginator = Zend_Paginator::factory($users);
        $referrals_paginator->setItemCountPerPage(10);
        $referrals_paginator->setCurrentPageNumber($this->_getParam('page'));

        $form = $this->getSearchForm();
        $form->setMethod('get');
        $form->getElement('search')->setValue($this->_getParam('search'));

        $this->add($this->component()->navigation('main'));
        $this->add($this->component()->itemSearch($form));
        $this->add($this->component()->itemList($referrals_paginator, null, array('listPaginator' => true,)));
//        $this->add($this->component()->paginator($referrals_paginator))
        ;
        $this->renderContent();
    }


    public function referralsReferralAction()
    {
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $code = $this->_getParam('code');

        if (!$code) {
            return $this->_redirect($host_url . $this->view->url(array(), 'default'));
        }
        $invites_tbl = Engine_Api::_()->getDbTable('invites', 'inviter');
        $codes_tbl = Engine_Api::_()->getDbTable('codes', 'inviter');
        $sender_id = $codes_tbl->getUserId($code);
        if (!$sender_id) {
            return $this->_redirect($host_url . $this->view->url(array(), 'default'));
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $invitation_id = false;
        try {
            $invitation = array(
                'user_id' => $sender_id,
                'code' => trim($code),
                'provider' => 'link',
                'referred_date' => new Zend_Db_Expr('NOW()')
            );

            $invitation_id = $invites_tbl->insertReferralInvitation($invitation);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
        if (!$invitation_id) {
            return $this->_redirect($host_url . $this->view->url(array(), 'default'));
        }
        return $this->_redirect($host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'signup', 'code' => $code, 'sender' => $invitation_id), 'default', true));
    }

    /*********** Referrals controller ***********************/

    /*********** Signup controller ***********************/
    public function signupInit()
    {
    }

    public function signupIndexAction()
    {
        // If the user is logged in, they can't sign up now can they?
        if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
            return $this->redirect($this->view->url(array(), 'default', true));
        }

        //for user account fields
        $session = new Zend_Session_Namespace('invite');
        $session->invite_code = $this->_getParam('code');
        $session->invite_email = $this->_getParam('email');

        // Get invite params
        $session = new Zend_Session_Namespace('inviter');
        $session->invite_code = $this->_getParam('code');
        $session->invite_id = $this->_getParam('sender');
        $session->invite_email = $this->_getParam('email');
        $session->lock();

        /**
         * @var $providerApi Inviter_Api_Provider
         */
        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
        $inviteTable = Engine_Api::_()->getDbtable('invites', 'inviter');

        if (empty($session->invite_code)) {
            return $this->redirect($this->view->url(array(), 'default', true));
        }

        if ($session->invite_id) {
            $inviteSelect = $inviteTable->select()->where('invite_id = ?', $session->invite_id);
            $invite = $inviteTable->fetchRow($inviteSelect);
            if ($invite)
                return $this->redirect($this->view->url(array(), 'user_signup', true));
        } else {
            $inviteSelect = $inviteTable->select()->where('code = ?', $session->invite_code);
            $invite = $inviteTable->fetchRow($inviteSelect);
        }

        if ($invite && $providerApi->checkIntegratedProvider($invite->provider)) {
            return $this->redirect($this->view->url(array(), 'user_signup', true));
        }

        // Check code now if set
        $settings = Engine_Api::_()->getApi('settings', 'core');
        if ($settings->getSetting('user.signup.inviteonly') > 0) {
            // Check code

            // Check email
            if ($settings->getSetting('user.signup.checkemail')) {
                // Tsk tsk no email
                if (empty($session->sender)) {
                    if (empty($session->invite_email)) {
                        return $this->redirect($this->view->url(array(), 'default', true));
                    }
                    $inviteSelect
                        ->where('recipient = ?', $session->invite_email);
                }
            }
            $inviteRow = $inviteTable->fetchRow($inviteSelect);

            // No invite or already signed up
            if (!$inviteRow || $inviteRow->new_user_id) {
                return $this->redirect($this->view->url(array(), 'default', true));
            }
        }
        return $this->redirect($this->view->url(array(), 'user_signup', true));
    }
    /*********** Signup controller ***********************/
}
