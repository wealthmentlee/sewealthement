<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heloginpopup
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: IndexController.php 24.09.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Heloginpopup
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heloginpopup_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
//    $this->_helper->layout->setLayout('default-simple');

    $viewer = Engine_Api::_()->user()->getViewer();

    if( $viewer && $viewer->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }

    $this->view->form = $form = new Heloginpopup_Form_Login();

    $form->populate(array(
      'return_url' => $this->_getParam('return_url'),
    ));

    // Not a post
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Form not validfacebooka
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check login creds
    extract($form->getValues()); // $email, $password, $remember
    $user_table = Engine_Api::_()->getDbtable('users', 'user');
    $user_select = $user_table->select()
      ->where('email = ?', $email);          // If post exists
    $user = $user_table->fetchRow($user_select);

    // Get ip address
    $db = Engine_Db_Table::getDefaultAdapter();
    $ipObj = new Engine_IP();
    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

    // Check if user exists
    if( empty($user) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.');
      $form->addError(Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.'));

      // Register login
      Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
        'email' => $email,
        'ip' => $ipExpr,
        'timestamp' => new Zend_Db_Expr('NOW()'),
        'state' => 'no-member',
      ));

      return;
    }

    // Check if user is verified and enabled
    if( !$user->enabled ) {
      if( !$user->verified ) {
        $this->view->status = false;

        $resend_url = $this->_helper->url->url(array('action' => 'resend', 'email'=>$email), 'user_signup', true);
        $translate = Zend_Registry::get('Zend_Translate');
        $error = $translate->translate('This account still requires either email verification.');
        $error .= ' ';
        $error .= sprintf($translate->translate('Click <a href="%s">here</a> to resend the email.'), $resend_url);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);

        // Register login
        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
          'user_id' => $user->getIdentity(),
          'email' => $email,
          'ip' => $ipExpr,
          'timestamp' => new Zend_Db_Expr('NOW()'),
          'state' => 'disabled',
        ));

        return;
      } else if( !$user->approved ) {
        $this->view->status = false;

        $translate = Zend_Registry::get('Zend_Translate');
        $error = $translate->translate('This account still requires admin approval.');
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);

        // Register login
        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
          'user_id' => $user->getIdentity(),
          'email' => $email,
          'ip' => $ipExpr,
          'timestamp' => new Zend_Db_Expr('NOW()'),
          'state' => 'disabled',
        ));

        return;
      }
      // Should be handled by hooks or payment
      //return;
    }

    // Handle subscriptions
    if( Engine_Api::_()->hasModuleBootstrap('payment') ) {
      // Check for the user's plan
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
      if( !$subscriptionsTable->check($user) ) {
        // Register login
        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
          'user_id' => $user->getIdentity(),
          'email' => $email,
          'ip' => $ipExpr,
          'timestamp' => new Zend_Db_Expr('NOW()'),
          'state' => 'unpaid',
        ));
        // Redirect to subscription page
        $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
        $subscriptionSession->unsetAll();
        $subscriptionSession->user_id = $user->getIdentity();
        return $this->_helper->redirector->gotoRoute(array('module' => 'payment',
          'controller' => 'subscription', 'action' => 'index'), 'default', true);
      }
    }

    // Run pre login hook
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginBefore', $user);
    foreach( (array) $event->getResponses() as $response ) {
      if( is_array($response) ) {
        if( !empty($response['error']) && !empty($response['message']) ) {
          $form->addError($response['message']);
        } else if( !empty($response['redirect']) ) {
          $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
        } else {
          continue;
        }

        // Register login
        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
          'user_id' => $user->getIdentity(),
          'email' => $email,
          'ip' => $ipExpr,
          'timestamp' => new Zend_Db_Expr('NOW()'),
          'state' => 'third-party',
        ));

        // Return
        return;
      }
    }

    // Version 3 Import compatibility
    if( empty($user->password) ) {
      $compat = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.compatibility.password');
      $migration = null;
      try {
        $migration = Engine_Db_Table::getDefaultAdapter()->select()
          ->from('engine4_user_migration')
          ->where('user_id = ?', $user->getIdentity())
          ->limit(1)
          ->query()
          ->fetch();
      } catch( Exception $e ) {
        $migration = null;
        $compat = null;
      }
      if( !$migration ) {
        $compat = null;
      }

      if( $compat == 'import-version-3' ) {

        // Version 3 authentication
        $cryptedPassword = self::_version3PasswordCrypt($migration['user_password_method'], $migration['user_code'], $password);
        if( $cryptedPassword === $migration['user_password'] ) {
          // Regenerate the user password using the given password
          $user->salt = (string) rand(1000000, 9999999);
          $user->password = $password;
          $user->save();
          Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
          // @todo should we delete the old migration row?
        } else {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid credentials');
          $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid credentials supplied'));
          return;
        }
        // End Version 3 authentication

      } else {
        $form->addError('There appears to be a problem logging in. Please reset your password with the Forgot Password link.');

        // Register login
        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
          'user_id' => $user->getIdentity(),
          'email' => $email,
          'ip' => $ipExpr,
          'timestamp' => new Zend_Db_Expr('NOW()'),
          'state' => 'v3-migration',
        ));

        return;
      }
    }

    // Normal authentication
    else {
      $authResult = Engine_Api::_()->user()->authenticate($email, $password);
      $authCode = $authResult->getCode();
      Engine_Api::_()->user()->setViewer();

      if( $authCode != Zend_Auth_Result::SUCCESS ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid credentials');
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid credentials supplied'));

        // Register login
        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
          'user_id' => $user->getIdentity(),
          'email' => $email,
          'ip' => $ipExpr,
          'timestamp' => new Zend_Db_Expr('NOW()'),
          'state' => 'bad-password',
        ));

        return;
      }
    }

    // -- Success! --

    // Register login
    $loginTable = Engine_Api::_()->getDbtable('logins', 'user');
    $loginTable->insert(array(
      'user_id' => $user->getIdentity(),
      'email' => $email,
      'ip' => $ipExpr,
      'timestamp' => new Zend_Db_Expr('NOW()'),
      'state' => 'success',
      'active' => true,
    ));
    $_SESSION['login_id'] = $login_id = $loginTable->getAdapter()->lastInsertId();

    // Remember
    if( $remember ) {
      $lifetime = 1209600; // Two weeks
      Zend_Session::getSaveHandler()->setLifetime($lifetime, true);
      Zend_Session::rememberMe($lifetime);
    }

    // Increment sign-in count
    Engine_Api::_()->getDbtable('statistics', 'core')
      ->increment('user.logins');

    // Test activity @todo remove
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
      $viewer->lastlogin_date = date("Y-m-d H:i:s");
      if( 'cli' !== PHP_SAPI ) {
        $viewer->lastlogin_ip = $ipExpr;
      }
      $viewer->save();
      Engine_Api::_()->getDbtable('actions', 'activity')
        ->addActivity($viewer, $viewer, 'login');
    }

    // Assign sid to view for json context
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Login successful');
    $this->view->sid = Zend_Session::getId();
    $this->view->sname = Zend_Session::getOptions('name');

    // Run post login hook
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $viewer);

    // Do redirection only if normal context
    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      // Redirect by form
      $uri = $form->getValue('return_url');
      if( $uri ) {
        if( substr($uri, 0, 3) == '64-' ) {
          $uri = base64_decode(substr($uri, 3));
        }
        return $this->_redirect($uri, array('prependBase' => false));
      }

      // Redirect by session
      $session = new Zend_Session_Namespace('Redirect');
      if( isset($session->uri) ) {
        $uri  = $session->uri;
        $opts = $session->options;
        $session->unsetAll();
        return $this->_redirect($uri, $opts);
      } else if( isset($session->route) ) {
        $session->unsetAll();
        return $this->_helper->redirector->gotoRoute($session->params, $session->route, $session->reset);
      }

      // Redirect by hook
      foreach( (array) $event->getResponses() as $response ) {
        if( is_array($response) ) {
          if( !empty($response['error']) && !empty($response['message']) ) {
            return $form->addError($response['message']);
          } else if( !empty($response['redirect']) ) {
            return $this->_helper->redirector->gotoUrl($response['redirect'], array('prependBase' => false));
          }
        }
      }

      // Just redirect to home
      return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }
  }

  public function facebookAction()
  {
    // Clear
    if( null !== $this->_getParam('clear') ) {
      unset($_SESSION['facebook_lock']);
      unset($_SESSION['facebook_uid']);
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    $facebook = $facebookTable->getApi();
    $settings = Engine_Api::_()->getDbtable('settings', 'core');

    $db = Engine_Db_Table::getDefaultAdapter();
    $ipObj = new Engine_IP();
    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

    $return_url = $this->_getParam('return_url', false);

    // Enabled?
    if( !$facebook || 'none' == $settings->core_facebook_enable ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Already connected
    if( $facebook->getUser() ) {
      $code = $facebook->getPersistentData('code');

      // Attempt to login
      if( !$viewer->getIdentity() ) {
        $facebook_uid = $facebook->getUser();
        if( $facebook_uid ) {
          $user_id = $facebookTable->select()
            ->from($facebookTable, 'user_id')
            ->where('facebook_uid = ?', $facebook_uid)
            ->query()
            ->fetchColumn();
        }
        if( $user_id &&
          $viewer = Engine_Api::_()->getItem('user', $user_id) ) {
          Zend_Auth::getInstance()->getStorage()->write($user_id);

          // Register login
          $viewer->lastlogin_date = date("Y-m-d H:i:s");

          if( 'cli' !== PHP_SAPI ) {
            $viewer->lastlogin_ip = $ipExpr;

            Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
              'user_id' => $user_id,
              'ip' => $ipExpr,
              'timestamp' => new Zend_Db_Expr('NOW()'),
              'state' => 'success',
              'source' => 'facebook',
            ));
          }

          $viewer->save();
        } else if( $facebook_uid ) {
          // They do not have an account
          $_SESSION['facebook_signup'] = true;
          return $this->_helper->redirector->gotoRoute(array(
            //'action' => 'facebook',
          ), 'user_signup', true);
        }
      } else {
        // Attempt to connect account
        $info = $facebookTable->select()
          ->from($facebookTable)
          ->where('user_id = ?', $viewer->getIdentity())
          ->limit(1)
          ->query()
          ->fetch();
        if( empty($info) ) {
          $facebookTable->insert(array(
            'user_id' => $viewer->getIdentity(),
            'facebook_uid' => $facebook->getUser(),
            'access_token' => $facebook->getAccessToken(),
            'code' => $code,
            'expires' => 0, // @todo make sure this is correct
          ));
        } else {
          //if( !empty($info['facebook_uid']) && $info['facebook_uid'] != $facebook->getUser() ) {
          // Incorrect user
          // Should we reconnect?
          //} else {
          // Save info to db
          $facebookTable->update(array(
            'facebook_uid' => $facebook->getUser(),
            'access_token' => $facebook->getAccessToken(),
            'code' => $code,
            'expires' => 0, // @todo make sure this is correct
          ), array(
            'user_id = ?' => $viewer->getIdentity(),
          ));
          //}
        }
      }

      // Redirect to home
      if($return_url)
        return $this->_redirect($return_url, array('prependBase' => false));
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Not connected
    else {
      // Okay
      if( !empty($_GET['code']) ) {
        // This doesn't seem to be necessary anymore, it's probably
        // being handled in the api initialization
        if($return_url)
          return $this->_redirect($return_url, array('prependBase' => false));
        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
      }

      // Error
      else if( !empty($_GET['error']) ) {
        // @todo maybe display a message?
        if($return_url)
          return $this->_redirect($return_url, array('prependBase' => false));
        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
      }

      // Redirect to auth page
      else {
        $redirect_uri = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url();
        if($return_url)
          $redirect_uri = $redirect_uri . '?return_url=' . $return_url;
        $url = $facebook->getLoginUrl(array(
          'redirect_uri' => $redirect_uri,
          'scope' => join(',', array(
            'email',
            'user_birthday',
            'user_status',
            'publish_stream',
            'offline_access',
          )),
        ));
        return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
      }
    }
  }

  public function twitterAction()
  {
    // Clear
    if( null !== $this->_getParam('clear') ) {
      unset($_SESSION['twitter_lock']);
      unset($_SESSION['twitter_token']);
      unset($_SESSION['twitter_secret']);
      unset($_SESSION['twitter_token2']);
      unset($_SESSION['twitter_secret2']);
    }

    if( $this->_getParam('denied') ) {
      $this->view->error = 'Access Denied!';
      return;
    }

    $return_url = $this->_getParam('return_url', false);

    // Setup
    $viewer = Engine_Api::_()->user()->getViewer();
    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
    $twitter = $twitterTable->getApi();
    $twitterOauth = $twitterTable->getOauth();

    $db = Engine_Db_Table::getDefaultAdapter();
    $ipObj = new Engine_IP();
    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

    // Check
    if( !$twitter || !$twitterOauth ) {
      if($return_url)
        return $this->_redirect($return_url, array('prependBase' => false));
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Connect
    try {

      $accountInfo = null;
      if( isset($_SESSION['twitter_token2'], $_SESSION['twitter_secret2']) ) {
        // Try to login?
        if( !$viewer->getIdentity() ) {
          // Get account info
          try {
            $accountInfo = $twitter->account->verify_credentials();
          } catch( Exception $e ) {
            // This usually happens when the application is modified after connecting
            unset($_SESSION['twitter_token']);
            unset($_SESSION['twitter_secret']);
            unset($_SESSION['twitter_token2']);
            unset($_SESSION['twitter_secret2']);
            $twitterTable->clearApi();
            $twitter = $twitterTable->getApi();
            $twitterOauth = $twitterTable->getOauth();
          }
        }
      }

      if( isset($_SESSION['twitter_token2'], $_SESSION['twitter_secret2']) ) {
        // Try to login?
        if( !$viewer->getIdentity() ) {

          $info = $twitterTable->select()
            ->from($twitterTable)
            ->where('twitter_uid = ?', $accountInfo->id)
            ->query()
            ->fetch();

          if( empty($info) ) {
            // They do not have an account
            $_SESSION['twitter_signup'] = true;
            return $this->_helper->redirector->gotoRoute(array(
              //'action' => 'twitter',
            ), 'user_signup', true);
          } else {
            Zend_Auth::getInstance()->getStorage()->write($info['user_id']);
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
          }
        }
        // Success
        if($return_url)
          return $this->_redirect($return_url, array('prependBase' => false));
        return $this->_helper->redirector->gotoRoute(array(), 'default', true);

      } else if( isset($_SESSION['twitter_token'], $_SESSION['twitter_secret'],
      $_GET['oauth_verifier']) ) {
        $twitterOauth->getAccessToken('https://twitter.com/oauth/access_token', $_GET['oauth_verifier']);

        $_SESSION['twitter_token2'] = $twitter_token = $twitterOauth->getToken();
        $_SESSION['twitter_secret2'] = $twitter_secret = $twitterOauth->getTokenSecret();

        // Reload api?
        $twitterTable->clearApi();
        $twitter = $twitterTable->getApi();

        // Get account info
        $accountInfo = $twitter->account->verify_credentials();

        // Save to settings table (if logged in)
        if( $viewer->getIdentity() ) {
          $info = $twitterTable->select()
            ->from($twitterTable)
            ->where('user_id = ?', $viewer->getIdentity())
            ->query()
            ->fetch();

          if( !empty($info) ) {
            $twitterTable->update(array(
              'twitter_uid' => $accountInfo->id,
              'twitter_token' => $twitter_token,
              'twitter_secret' => $twitter_secret,
            ), array(
              'user_id = ?' => $viewer->getIdentity(),
            ));
          } else {
            $twitterTable->insert(array(
              'user_id' => $viewer->getIdentity(),
              'twitter_uid' => $accountInfo->id,
              'twitter_token' => $twitter_token,
              'twitter_secret' => $twitter_secret,
            ));
          }

          // Redirect
          if($return_url)
            return $this->_redirect($return_url, array('prependBase' => false));
          return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else { // Otherwise try to login?
          $info = $twitterTable->select()
            ->from($twitterTable)
            ->where('twitter_uid = ?', $accountInfo->id)
            ->query()
            ->fetch();

          if( empty($info) ) {
            // They do not have an account
            $_SESSION['twitter_signup'] = true;
            return $this->_helper->redirector->gotoRoute(array(
              //'action' => 'twitter',
            ), 'user_signup', true);
          } else {
            Zend_Auth::getInstance()->getStorage()->write($info['user_id']);

            // Register login
            $viewer = Engine_Api::_()->getItem('user', $info['user_id']);
            $viewer->lastlogin_date = date("Y-m-d H:i:s");

            if( 'cli' !== PHP_SAPI ) {
              $viewer->lastlogin_ip = $ipExpr;

              Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                'user_id' => $info['user_id'],
                'ip' => $ipExpr,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'state' => 'success',
                'source' => 'twitter',
              ));
            }

            $viewer->save();

            // Redirect
            if($return_url)
              return $this->_redirect($return_url, array('prependBase' => false));
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
          }

        }

      } else {

        unset($_SESSION['twitter_token']);
        unset($_SESSION['twitter_secret']);
        unset($_SESSION['twitter_token2']);
        unset($_SESSION['twitter_secret2']);

        // Reload api?
        $twitterTable->clearApi();
        $twitter = $twitterTable->getApi();
        $twitterOauth = $twitterTable->getOauth();

        // Connect account
        $redirect_uri = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url();
        if($return_url)
          $redirect_uri = $redirect_uri . '?return_url=' . $return_url;

        $twitterOauth->getRequestToken('https://twitter.com/oauth/request_token',
          $redirect_uri);

        $_SESSION['twitter_token']  = $twitterOauth->getToken();
        $_SESSION['twitter_secret'] = $twitterOauth->getTokenSecret();

        $url = $twitterOauth->getAuthorizeUrl('http://twitter.com/oauth/authenticate');

        return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
      }
    } catch( Services_Twitter_Exception $e ) {
      if( in_array($e->getCode(), array(500, 502, 503)) ) {
        $this->view->error = 'Twitter is currently experiencing technical issues, please try again later.';
        return;
      } else {
        throw $e;
      }
    } catch( Exception $e ) {
      throw $e;
    }
  }
}
