<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 07.06.12
 * Time: 18:05
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_UserController extends Apptouch_Controller_Action_Bridge
{

  protected $_user;


// IndexController {
  public function indexHomeAction()
  {

    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      return $this->redirect($this->view->url(array(), 'user_login', true));
    }


    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.portal', 1);
    if (!$require_check) {
      if (!$this->_helper->requireUser()->isValid()) return;
    }

    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      return $this->redirect($this->view->url(array(), 'default', true));
    }
    $a = Engine_Api::_()->getItem('activity_action', 101);
    $title = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('wall') ? '' : $this->view->translate('What\'s New');
    $this
      ->setPageTitle($title)
      ->add($this->component()->feed())
      ->renderContent();
  }

  public function indexBrowseAction()
  {
    $this->addPageInfo('contentTheme', 'd');

    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if (!$require_check) {
      if (!$this->_helper->requireUser()->isValid()) return;
    }
    if (!($paginator = $this->_executeSearch())) {
      // throw new Exception('error');
    }
    $members = $this->dom()->new_('div', array('class' => 'member_list ui-grid-b'));
    $blocks = array('ui-block-a', 'ui-block-b', 'ui-block-c');
    /**
     * @var $member User_Model_User
     * */
    $counter = 0;
    $paginator->setItemCountPerPage(9);
    foreach ($paginator as $member) {
      $memberEl = $this->dom()->new_('div', array('class' => $blocks[$counter % 3]), null, array(
        $this->dom()->new_('div', array('class' => 'member_item'), null, array(
          $this->dom()->new_('div', array(), null, array(
            $this->dom()->new_('a', array('class' => 'profile_img', 'href' => $member->getHref(), 'style' => $member->getPhotoUrl('thumb.profile') ? 'background-image: url(' . $member->getPhotoUrl('thumb.profile') . ')' : '')),
            $this->dom()->new_('span', array(), $member->getTitle()),
            $this->dom()->new_('div', array(), $this->userFriendshipBtn($member))
          ))
        ))
      ));
      $counter++;
      $members->append($memberEl);
    }

    $this->setFormat('manage')
      ->setPageTitle($this->view->translate('Browse Members'))
//      ->add($this->component()->itemList($paginator, 'browseUserData'))
      ->add($this->component()->html($members))
      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  protected function _executeSearch()
  {
    $page = (int)$this->_getParam('page', 1);
    $ajax = (bool)$this->_getParam('ajax', false);
    $options = $this->_getAllParams();
    $form = $this->getSearchForm();
    $form->populate($options);
    //$form->getValues();

    // Process options
    $tmp = array();
    $originalOptions = $options;
    foreach ($options as $k => $v) {
      if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
        continue;
      } else if (false !== strpos($k, '_field_')) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if (false !== strpos($k, '_alias_')) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $options = $tmp;

    // Get table info
    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
    $searchTableName = $searchTable->info('name');

    $profile_type = @$options['profile_type'];
    $displayname = @$options['search'];
    if (!empty($options['extra'])) {
      extract($options['extra']); // is_online, has_photo, submit
    }

    // Contruct query
    $select = $table->select()
      ->from($userTableName)
      ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
      ->where("{$userTableName}.search = ?", 1)
      ->where("{$userTableName}.enabled = ?", 1)
      ->order("{$userTableName}.displayname ASC");

    // Build the photo and is online part of query
    if (isset($has_photo) && !empty($has_photo)) {
      $select->where($userTableName . '.photo_id != ?', "0");
    }

    if (isset($is_online) && !empty($is_online)) {
      $select
        ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
        ->group("engine4_user_online.user_id")
        ->where($userTableName . '.user_id != ?', "0");
    }

    // Add displayname
    if (!empty($displayname)) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
    }

    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
    foreach ($searchParts as $k => $v) {
      $select->where("`{$searchTableName}`.{$k}", $v);
    }

    // Build paginator
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);
    $this->add($this->component()->itemSearch($form));

    return $paginator;
  }

  public function browseUserData(Core_Model_Item_Abstract $item)
  {
    $customize_fields = array(
      'descriptions' => null,
      'owner_id' => null,
      'owner' => null,
      'creation_date' => $this->view->translate('Joined: %s', $this->view->timestamp(strtotime($item->creation_date))),
      'photo' => $item->getPhotoUrl('thumb.normal'),
      'manage' => array(
        $this->userFriendship($item)
      )
    );
    if ($item->status != "")
      $customize_fields['descriptions'] = array(
        $item->status,
        $this->view->timestamp($item->status_date)
      );

    return $customize_fields;

  }

  public function userFriendship(Core_Model_Item_Abstract $user)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer || !$viewer->getIdentity() || $user->isSelf($viewer)) {
      return array();
    }

    $direction = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

    // Get data
    if (!$direction) {
      $row = $user->membership()->getRow($viewer);
    } else $row = $viewer->membership()->getRow($user);

    // Render

    // Check if friendship is allowed in the network
    $eligible = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    if ($eligible == 0) {
      return array();
    } // check admin level setting if you can befriend people in your network
    else if ($eligible == 1) {

      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $networkMembershipName = $networkMembershipTable->info('name');

      $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
      $select
        ->from($networkMembershipName, 'user_id')
        ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
        ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
        ->where("`{$networkMembershipName}_2`.user_id = ?", $user->getIdentity());

      $data = $select->query()->fetch();

      if (empty($data)) {
        return array();
      }
    }

    if (!$direction) {
      // one-way mode
      if (null === $row) {
        return $this->getOption($user, 0);
      } else if ($row->resource_approved == 0) {
        return $this->getOption($user, 1);
      } else {
        return $this->getOption($user, 2);
      }

    } else {
      // two-way mode
      if (null === $row) {
        return $this->getOption($user, 3);
      } else if ($row->user_approved == 0) {
        return $this->getOption($user, 4);
      } else if ($row->resource_approved == 0) {
        return $this->getOption($user, 5);
      } else if ($row->active) {
        return $this->getOption($user, 6);
      }
    }

    return array();
  }

// } IndexController

// AuthController {
  public function authLoginAction()
  {
    $email = '';
    $password = '';
    $remember = '';

    // Already logged in
    if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('You are already signed in.');

      $this->redirect($this->view->url(array('action' => 'home'), 'user_general'));
      return;
    }

    $form = new Apptouch_Form_Login();
    $form->setMethod(Zend_Form::METHOD_POST);
    // Make form
//    $this
//      ->setFormat('html')
//      ->add($this->component()->dashboard())
//      ->add($this->component()->footerMenu(Engine_Api::_()->getApi('menus', 'apptouch')->getNavigation('core_footer')));
    $form->populate(array(
      'return_url' => $this->_getParam('return_url'),
    ));

    // Facebook login
    //    if( User_Model_DbTable_Facebook::authenticate($form) ) {
    // Facebook login succeeded, redirect to home
    //      return $this->_helper->touchRedirector->gotoRoute(array(), 'default', true);
    //    }

    // Not a post
    if (!$this->getRequest()->isPost()) {
      $siteinfo = Engine_Api::_()->getApi('settings', 'core')->__get('core.general.site', array());
      $siteinfo = array_filter($siteinfo);
      $siteinfo = array_merge(array(
        'title' => 'Social Network',
        'description' => '',
        'keywords' => '',
      ), $siteinfo);
      //      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      $title = $siteinfo['title'];
      $title = $this->view->translate('' . (is_array($title) ? $title[Zend_Registry::get('Locale')->getLanguage()] : $title));
      $desc = $siteinfo['description'];

      $desc = is_array($desc) ? $desc[Zend_Registry::get('Locale')->getLanguage()] : $desc;
      $logoUrl = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('apptouch.sitelogo', false);
      $logo = '<h1 class="site-title">' . $title . '</h1>';
      if ($logoUrl) {
        $logoUrl = $this->view->siteLogo()->url();
        $logo = <<<LOGO
        <img class="site-logo" src="{$logoUrl}" />
LOGO;
      }
      $intro = <<<INTRO
    <center class="ui-bar-a">
    {$logo}
   <p class="site-description">{$desc}</p>
   </center>
INTRO;
      if ($intro && !Engine_Api::_()->apptouch()->isApp()) {
        $this
          ->add($this->component()->html($intro));
      }

      $this
        ->add($this->component()->form($form))
        ->renderContent();

      return 0;
    }


    // Form not valid
    if (!$form->isValid($this->getRequest()->getParams())) {
      $this->view->status = false;
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }
    // Check login creds
    extract($form->getValues()); // $email, $password, $remember
    $user_table = Engine_Api::_()->getDbtable('users', 'user');
    $user_select = $user_table->select()
      ->where('email = ?', $email); // If post exists
    $user = $user_table->fetchRow($user_select);

    if (class_exists('Engine_IP')) {
      $ipObj = new Engine_IP();
      $ipBinStr = $ipObj->toBinary();
    } else {
      $ipBinStr = ip2long($_SERVER['REMOTE_ADDR']);
    }

    // Check if user exists
    if (empty($user)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.');
      $form->addError(Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.'));

      // Register login
      Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
        'email' => $email,
        'ip' => $ipBinStr,
        'timestamp' => new Zend_Db_Expr('NOW()'),
        'state' => 'no-member',
      ));
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Handle subscriptions
    if (Engine_Api::_()->hasModuleBootstrap('payment')) {
      // Check for the user's plan
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
      if (!$subscriptionsTable->check($user)) {
        // Register login
        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
          'user_id' => $user->getIdentity(),
          'email' => $email,
          'ip' => $ipBinStr,
          'timestamp' => new Zend_Db_Expr('NOW()'),
          'state' => 'unpaid',
        ));
        // Redirect to subscription page
        $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
        $subscriptionSession->unsetAll();
        $subscriptionSession->user_id = $user->getIdentity();

        return $this->redirect($this->view->url(array('module' => 'payment',
          'controller' => 'subscription', 'action' => 'index'), 'default', true));
      }
    }

    // Check if user is verified and enabled
    if (!$user->enabled) {
      $this->view->status = false;

      $translate = Zend_Registry::get('Zend_Translate');
      $error = $translate->translate('This account still requires either email verification or admin approval.');

      if (!empty($user) && !$user->verified) {
        $resend_url = $this->_helper->url->url(array('action' => 'resend', 'email' => $email), 'user_signup', true);
        $error .= ' ';
        $error .= sprintf($translate->translate('Click <a href="%s">here</a> to resend the email.'), $resend_url);
      }

      $form->getDecorator('errors')->setOption('escape', 'false');
      $form->addError($error);

      // Register login
      Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
        'user_id' => $user->getIdentity(),
        'email' => $email,
        'ip' => $ipBinStr,
        'timestamp' => new Zend_Db_Expr('NOW()'),
        'state' => 'disabled',
      ));
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Run pre login hook
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginBefore', $user);
    foreach ((array)$event->getResponses() as $response) {
      if (is_array($response)) {
        if (!empty($response['error']) && !empty($response['message'])) {
          $form->addError($response['message']);
        } else if (!empty($response['redirect'])) {
          $this->redirect($this->view->url($response['redirect'], array('prependBase' => false)));
        } else {
          continue;
        }

        // Register login
        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
          'user_id' => $user->getIdentity(),
          'email' => $email,
          'ip' => $ipBinStr,
          'timestamp' => new Zend_Db_Expr('NOW()'),
          'state' => 'third-party',
        ));

        // Return
        $this
          ->add($this->component()->form($form))
          ->renderContent();
        return;
      }
    }

    // Version 3 Import compatibility
    if (empty($user->password)) {
      $compat = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.compatibility.password');
      $migration = null;
      try {
        $migration = Engine_Db_Table::getDefaultAdapter()->select()
          ->from('engine4_user_migration')
          ->where('user_id = ?', $user->getIdentity())
          ->limit(1)
          ->query()
          ->fetch();
      } catch (Exception $e) {
        $migration = null;
        $compat = null;
      }
      if (!$migration) {
        $compat = null;
      }

      if ($compat == 'import-version-3') {

        // Version 3 authentication
        $cryptedPassword = self::_version3PasswordCrypt($migration['user_password_method'], $migration['user_code'], $password);
        if ($cryptedPassword === $migration['user_password']) {
          // Regenerate the user password using the given password
          $user->salt = (string)rand(1000000, 9999999);
          $user->password = $password;
          $user->save();
          Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
          // @todo should we delete the old migration row?
        } else {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid credentials');
          $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid credentials supplied'));
          $this
            ->add($this->component()->form($form))
            ->renderContent();
          return;
        }
        // End Version 3 authentication

      } else {
        $form->addError('There appears to be a problem logging in. Please reset your password with the Forgot Password link.');

        // Register login
        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
          'user_id' => $user->getIdentity(),
          'email' => $email,
          'ip' => $ipBinStr,
          'timestamp' => new Zend_Db_Expr('NOW()'),
          'state' => 'v3-migration',
        ));

        $this
          ->add($this->component()->form($form))
          ->renderContent();
        return;
      }
    } // Normal authentication
    else {
      $authResult = Engine_Api::_()->user()->authenticate($email, $password);
      $authCode = $authResult->getCode();
      Engine_Api::_()->user()->setViewer();

      if ($authCode != Zend_Auth_Result::SUCCESS) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid credentials');
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid credentials supplied'));

        // Register login
        Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
          'user_id' => $user->getIdentity(),
          'email' => $email,
          'ip' => $ipBinStr,
          'timestamp' => new Zend_Db_Expr('NOW()'),
          'state' => 'bad-password',
        ));

        $this
          ->add($this->component()->form($form))
          ->renderContent();
        return;
      }
    }

    // -- Success! --

    // Register login
    $loginTable = Engine_Api::_()->getDbtable('logins', 'user');
    $loginTable->insert(array(
      'user_id' => $user->getIdentity(),
      'email' => $email,
      'ip' => $ipBinStr,
      'timestamp' => new Zend_Db_Expr('NOW()'),
      'state' => 'success',
      'active' => true,
    ));
    $_SESSION['login_id'] = $login_id = $loginTable->getAdapter()->lastInsertId();

    // Remember
    if ($remember) {
      $lifetime = 1209600; // Two weeks
      Zend_Session::getSaveHandler()->setLifetime($lifetime, true);
      Zend_Session::rememberMe($lifetime);
    }

    // Increment sign-in count
    Engine_Api::_()->getDbtable('statistics', 'core')
      ->increment('user.logins');
    Engine_Api::_()->getDbtable('statistics', 'core')
      ->increment('apptouch.user.logins');

    // Test activity @todo remove
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity()) {
      $viewer->lastlogin_date = date("Y-m-d H:i:s");
      if ('cli' !== PHP_SAPI) {
        if (class_exists('Engine_IP')) {
          $ipObj = new Engine_IP();
          $viewer->lastlogin_ip = $ipObj->toBinary();
        } else {
          $viewer->lastlogin_ip = ip2long($_SERVER['REMOTE_ADDR']);
        }
      }
      $viewer->save();
      Engine_Api::_()->getDbtable('actions', 'activity')
        ->addActivity($viewer, $viewer, 'login', null, array('is_mobile' => true));
    }

    // Assign sid to view for json context
    $this->view->status = true;
//    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Login successful');
    $this->userSessionStart();

    //    $this->redirect($this->view->url(array('action' => 'home'), 'user_general', true);
    //    $this->view->sname = Zend_Session::getOptions('name');

    // Run post login hook
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $viewer);

    // Do redirection only if normal context
    if (true /* todo temp solution */ || null === $this->_helper->contextSwitch->getCurrentContext()) {
      // Redirect by form
      $uri = $form->getValue('return_url');
      if ($uri) {
        if (substr($uri, 0, 3) == '64-') {
          $uri = base64_decode(substr($uri, 3));
        }
        return $this->redirect($uri);
      }

      // Redirect by session
      $session = new Zend_Session_Namespace('Redirect');
      if (isset($session->uri)) {
        $uri = $session->uri;
        $opts = $session->options;
        $session->unsetAll();
        return $this->_redirect($uri, $opts);
      } else if (isset($session->route)) {
        $session->unsetAll();

        return $this->redirect($this->view->url($session->params, $session->route, $session->reset));
      }

      // Redirect by hook
      foreach ((array)$event->getResponses() as $response) {
        if (is_array($response)) {
          if (!empty($response['error']) && !empty($response['message'])) {
            return $form->addError($response['message']);
          } else if (!empty($response['redirect'])) {
            return $this->redirect($this->view->url($response['redirect'], array('prependBase' => false)));
          }
        }
      }
      // Just redirect to home
      return $this->redirect($this->view->url(array('action' => 'home'), 'user_general', true));
    }

    $this
      ->add($this->component()->form($form))
      ->renderContent();

  }

  public function authForgotAction()
  {
    // no logged in users
    if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
      return $this->redirect($this->view->url(array('action' => 'home'), 'user_general', true));
    }

    // Make form
    $form = new User_Form_Auth_Forgot();

    // Check request
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Check data
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Check for existing user
    $user = Engine_Api::_()->getDbtable('users', 'user')
      ->fetchRow(array('email = ?' => $form->getValue('email')));
    if (!$user || !$user->getIdentity()) {
      $form->addError('A user account with that email was not found.');
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Check to make sure they're enabled
    if (!$user->enabled) {
      $form->addError('That user account has not yet been verified or disabled by an admin.');
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Ok now we can do the fun stuff
    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'user');
    $db = $forgotTable->getAdapter();
    $db->beginTransaction();

    try {
      // Delete any existing reset password codes
      $forgotTable->delete(array(
        'user_id = ?' => $user->getIdentity(),
      ));

      // Create a new reset password code
      $code = base_convert(md5($user->salt . $user->email . $user->user_id . uniqid(time(), true)), 16, 36);
      $forgotTable->insert(array(
        'user_id' => $user->getIdentity(),
        'code' => $code,
        'creation_date' => date('Y-m-d H:i:s'),
      ));

      // Send user an email
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'core_lostpassword', array(
        'host' => $_SERVER['HTTP_HOST'],
        'email' => $user->email,
        'date' => time(),
        'recipient_title' => $user->getTitle(),
        'recipient_link' => $user->getHref(),
        'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
        'object_link' => $this->_helper->url->url(array('action' => 'reset', 'code' => $code, 'uid' => $user->getIdentity())),
        'queue' => false,
      ));

      // Show success
//      $this->view->sent = true;
      $this->add($this->component()->tip($this->view->translate("USER_VIEWS_SCRIPTS_AUTH_FORGOT_DESCRIPTION")))
        ->renderContent();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->add($this->component()->tip($e->getTrace(), $e->getMessage()))
        ->renderContent();

      throw $e;
    }
  }

  public function authResetAction()
  {
    // no logged in users
    if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
      return $this->redirect($this->view->url(array('action' => 'home'), 'user_general', true));
    }

    // Check for empty params
    $user_id = $this->_getParam('uid');
    $code = $this->_getParam('code');

    if (empty($user_id) || empty($code)) {
      return $this->redirect($this->view->url(array(), 'default', true));
    }

    // Check user
    $user = Engine_Api::_()->getItem('user', $user_id);
    if (!$user || !$user->getIdentity()) {
      return $this->redirect($this->view->url(array(), 'default', true));
    }

    // Check code
    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'user');
    $forgotSelect = $forgotTable->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('code = ?', $code);

    $forgotRow = $forgotTable->fetchRow($forgotSelect);
    if (!$forgotRow || (int)$forgotRow->user_id !== (int)$user->getIdentity()) {
      return $this->redirect($this->view->url(array(), 'default', true));
    }

    // Code expired
    // Note: Let's set the current timeout for 6 hours for now
    $min_creation_date = time() - (3600 * 24);
    if (strtotime($forgotRow->creation_date) < $min_creation_date) { // @todo The strtotime might not work exactly right
      return $this->redirect($this->view->url(array(), 'default', true));
    }

    // Make form
    $form = new User_Form_Auth_Reset();
    $form->setAction($this->_helper->url->url(array()));
    // Check request
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Check data
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $values = $form->getValues();

    // Check same password
    if ($values['password'] !== $values['password_confirm']) {
      $form->addError('The passwords you entered did not match.');
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Db
    $db = $user->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      // Delete the lost password code now
      $forgotTable->delete(array(
        'user_id = ?' => $user->getIdentity(),
      ));

      // This gets handled by the post-update hook
      $user->password = $values['password'];
      $user->save();
      $db->commit();
      $this->add($this->component()->tip($this->view->translate("Your password has been reset. Click %s to sign-in.", $this->view->htmlLink(array('route' => 'user_login'), $this->view->translate('here')))))
        ->renderContent();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function authLogoutAction()
  {
    // Check if already logged out
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('You are already logged out.');
      if (null === $this->_helper->contextSwitch->getCurrentContext()) {
        $this->redirect($this->view->url(array(), 'user_login'));
      }
      return;
    }

    // Test activity @todo remove
    Engine_Api::_()->getDbtable('actions', 'activity')
      ->addActivity($viewer, $viewer, 'logout', null, array('is_mobile' => true));

    $table = $this->_helper->api()->getItemTable('user');
    $onlineTable = $this->_helper->api()->getDbtable('online', 'user')
      ->delete(array(
        'user_id = ?' => $viewer->getIdentity(),
      ));

    // Logout
    Engine_Api::_()->user()->getAuth()->clearIdentity();

    if (!empty($_SESSION['login_id'])) {
      Engine_Api::_()->getDbtable('logins', 'user')->update(array(
        'active' => false,
      ), array(
        'login_id = ?' => $_SESSION['login_id'],
      ));
      unset($_SESSION['login_id']);
    }

    // Run logout hook
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLogoutAfter', $viewer);

    $this->userSessionClose();

//    $session = new Zend_Session_Namespace('apptouch-site-mode');
//    if ($session->__isset('mode'))
//      $session->__unset('mode');

    $this->redirect($this->view->url(array(), 'user_login'), Zend_Registry::get('Zend_Translate')->_('You are now logged out.'), true);
  }

  public function authFacebookAction()
  {
    // Clear todo may be delete this code part
    if (null !== $this->_getParam('clear')) {
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

    // Enabled?
    if (!$facebook || 'none' == $settings->core_facebook_enable) {
      return $this->redirect($this->view->url(array(), 'default', true));
    }

    if (Engine_Api::_()->apptouch()->isApp()) {
      if ($this->getRequest()->isPost()) {

        $params = $this->_getAllParams();

        if (!$params || empty($params)) {
          return $this->redirect($this->view->url(array(), 'default', true));
        }

        if (!empty($params['id'])) {

          if (!$viewer->getIdentity()) {

            $facebook_uid = $params['id'];

            if ($facebook_uid) {
              $user_id = $facebookTable->select()
                ->from($facebookTable, 'user_id')
                ->where('facebook_uid = ?', $facebook_uid)
                ->query()
                ->fetchColumn();
            }
            if ($user_id && $viewer = Engine_Api::_()->getItem('user', $user_id)) {
              Zend_Auth::getInstance()->getStorage()->write($user_id);

              // Register login
              $viewer->lastlogin_date = date("Y-m-d H:i:s");

              if ('cli' !== PHP_SAPI) {
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

              $this->userSessionStart();
              $this->view->userSession = 'start';

              // Run post login hook
              $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $viewer);
              return $this->redirect($this->view->url(array('action' => 'home'), 'user_general', true));

            } else if ($facebook_uid) {
              // They do not have an account
              $_SESSION['facebook_signup'] = true;
              $_SESSION['facebook_api_info'] = Zend_Json::encode($params);
              $this->clearClientCache();
              return $this->redirect($this->view->url(array(), 'user_signup', true)); // todo
            }
          } else {
            // Attempt to connect account
            $info = $facebookTable->select()
              ->from($facebookTable)
              ->where('user_id = ?', $viewer->getIdentity())
              ->limit(1)
              ->query()
              ->fetch();
            if (empty($info)) {
              $facebookTable->insert(array(
                'user_id' => $viewer->getIdentity(),
                'facebook_uid' => $params['id'],
                'access_token' => $params['access_token'],
                'code' => '', // todo whey we need code
                'expires' => 0, // @todo make sure this is correct
              ));
            } else {
              //if( !empty($info['facebook_uid']) && $info['facebook_uid'] != $facebook->getUser() ) {
              // Incorrect user
              // Should we reconnect?
              //} else {
              // Save info to db
              $facebookTable->update(array(
                'facebook_uid' => $params['id'],
                'access_token' => $params['access_token'],
                'code' => '', // todo whey we need code
                'expires' => 0, // @todo make sure this is correct
              ), array(
                'user_id = ?' => $viewer->getIdentity(),
              ));
              //}
            }
          }
          return $this->redirect($this->view->url(array(), 'default', true));
        } // Not connected
        else {
          // connect to facebook todo
        }

      }
      $this->userSessionStart();
      // Run post login hook
      $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $viewer);
      return $this->redirect($this->view->url(array('action' => 'home'), 'user_general', true));
    }
    // Already connected
    if ($facebook->getUser()) {
      $code = $facebook->getPersistentData('code');

      // Attempt to login
      if (!$viewer->getIdentity()) {
        $facebook_uid = $facebook->getUser();
        if ($facebook_uid) {
          $user_id = $facebookTable->select()
            ->from($facebookTable, 'user_id')
            ->where('facebook_uid = ?', $facebook_uid)
            ->query()
            ->fetchColumn();
        }
        if ($user_id &&
          $viewer = Engine_Api::_()->getItem('user', $user_id)
        ) {
          Zend_Auth::getInstance()->getStorage()->write($user_id);

          // Register login
          $viewer->lastlogin_date = date("Y-m-d H:i:s");

          if ('cli' !== PHP_SAPI) {
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

          $this->userSessionStart();

          // Run post login hook
          $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $viewer);
        } else if ($facebook_uid) {
          // They do not have an account
          $_SESSION['facebook_signup'] = true;

          return $this->_helper->redirector->gotoUrl($this->view->url(array(), 'user_signup', true), array('prependBase' => false));
        }
      } else {
        // Attempt to connect account
        $info = $facebookTable->select()
          ->from($facebookTable)
          ->where('user_id = ?', $viewer->getIdentity())
          ->limit(1)
          ->query()
          ->fetch();
        if (empty($info)) {
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
      return $this->_helper->redirector->gotoUrl($this->view->url(array(), 'default', true), array('prependBase' => false));
    } // Not connected
    else {
      // Okay
      if (!empty($_GET['code'])) {
        // This doesn't seem to be necessary anymore, it's probably
        // being handled in the api initialization
        return $this->redirect($this->view->url(array(), 'default', true));
      } // Error
      else if (!empty($_GET['error'])) {
        // @todo maybe display a message?
        return $this->redirect($this->view->url(array(), 'default', true));
      } // Redirect to auth page
      else {
        $url = $facebook->getLoginUrl(array(
          'redirect_uri' => (_ENGINE_SSL ? 'https://' : 'http://')
            . $_SERVER['HTTP_HOST'] . $this->view->url(),
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

  public function authTwitterAction()
  {
    // Clear
    if (null !== $this->_getParam('clear')) {
      unset($_SESSION['twitter_lock']);
      unset($_SESSION['twitter_token']);
      unset($_SESSION['twitter_secret']);
      unset($_SESSION['twitter_token2']);
      unset($_SESSION['twitter_secret2']);
    }

    if ($this->_getParam('denied')) {
      $this->view->error = 'Access Denied!';
      return;
    }

    // Setup
    $viewer = Engine_Api::_()->user()->getViewer();
    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
    $twitter = $twitterTable->getApi();
    $twitterOauth = $twitterTable->getOauth();

    $db = Engine_Db_Table::getDefaultAdapter();
    $ipObj = new Engine_IP();
    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

    // Check
    if (!$twitter || !$twitterOauth) {
      return $this->redirect($this->view->url(array(), 'default', true));
    }

    if (Engine_Api::_()->apptouch()->isApp()) {
      if ($this->getRequest()->isPost()) {

        $params = $this->_getAllParams();

        if (!$params || empty($params)) {
          return $this->redirect($this->view->url(array(), 'default', true));
        }

        if (!empty($params['user_id'])) {

          if (!$viewer->getIdentity()) {

            $twitter_uid = $params['user_id'];

            if ($twitter_uid) {
              $user_id = $twitterTable->select()
                ->from($twitterTable, 'user_id')
                ->where('twitter_uid = ?', $twitter_uid)
                ->query()
                ->fetchColumn();
            }
            if ($user_id && $viewer = Engine_Api::_()->getItem('user', $user_id)) {
              Zend_Auth::getInstance()->getStorage()->write($user_id);

              // Register login
              $viewer->lastlogin_date = date("Y-m-d H:i:s");

              if ('cli' !== PHP_SAPI) {
                $viewer->lastlogin_ip = $ipExpr;

                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                  'user_id' => $user_id,
                  'ip' => $ipExpr,
                  'timestamp' => new Zend_Db_Expr('NOW()'),
                  'state' => 'success',
                  'source' => 'twitter',
                ));
              }

              $viewer->save();

              $this->userSessionStart();
              $this->view->userSession = 'start';

              return $this->redirect($this->view->url(array('action' => 'home'), 'user_general', true));

            } else if ($twitter_uid) {
              // They do not have an account
              $_SESSION['twitter_signup'] = true;
              $_SESSION['twitter_api_info'] = Zend_Json::encode($params);
              $this->clearClientCache();
              return $this->redirect($this->view->url(array(), 'user_signup', true)); // todo
            }
          } else {
            // Attempt to connect account
            $info = $twitterTable->select()
              ->from($twitterTable)
              ->where('user_id = ?', $viewer->getIdentity())
              ->limit(1)
              ->query()
              ->fetch();
            if (empty($info)) {
              $twitterTable->insert(array(
                'user_id' => $viewer->getIdentity(),
                'twitter_uid' => $params['user_id'],
                'twitter_token' => $params['oauth_token'],
                'twitter_secret ' => $params['oauth_token_secret']
              ));
            } else {
              $twitterTable->update(array(
                'twitter_uid' => $params['user_id'],
                'twitter_token' => $params['oauth_token'],
                'twitter_secret' => $params['oauth_token_secret']
              ), array(
                'user_id = ?' => $viewer->getIdentity(),
              ));
              //}
            }
          }
          return $this->redirect($this->view->url(array(), 'default', true));
        } // Not connected
        else {
          // connect to facebook todo
        }

      }
      $this->clearClientCache();
      return $this->redirect($this->view->url(array('action' => 'home'), 'user_general', true));
    }

    // Connect
    try {

      $accountInfo = null;
      if (isset($_SESSION['twitter_token2'], $_SESSION['twitter_secret2'])) {
        // Try to login?
        if (!$viewer->getIdentity()) {
          // Get account info
          try {
            $accountInfo = $twitter->account->verify_credentials();
          } catch (Exception $e) {
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

      if (isset($_SESSION['twitter_token2'], $_SESSION['twitter_secret2'])) {
        // Try to login?
        if (!$viewer->getIdentity()) {

          $info = $twitterTable->select()
            ->from($twitterTable)
            ->where('twitter_uid = ?', $accountInfo->id)
            ->query()
            ->fetch();

          if (empty($info)) {
            // They do not have an account
            $_SESSION['twitter_signup'] = true;
            return $this->redirect($this->view->url(array(), 'user_signup', true));
          } else {
            Zend_Auth::getInstance()->getStorage()->write($info['user_id']);
            return $this->redirect($this->view->url(array(), 'default', true));
          }
        }
        // Success
        return $this->redirect($this->view->url(array(), 'default', true));

      } else if (isset($_SESSION['twitter_token'], $_SESSION['twitter_secret'],
      $_GET['oauth_verifier'])
      ) {
        $twitterOauth->getAccessToken('https://twitter.com/oauth/access_token', $_GET['oauth_verifier']);

        $_SESSION['twitter_token2'] = $twitter_token = $twitterOauth->getToken();
        $_SESSION['twitter_secret2'] = $twitter_secret = $twitterOauth->getTokenSecret();

        // Reload api?
        $twitterTable->clearApi();
        $twitter = $twitterTable->getApi();

        // Get account info
        $accountInfo = $twitter->account->verify_credentials();

        // Save to settings table (if logged in)
        if ($viewer->getIdentity()) {
          $info = $twitterTable->select()
            ->from($twitterTable)
            ->where('user_id = ?', $viewer->getIdentity())
            ->query()
            ->fetch();

          if (!empty($info)) {
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
          return $this->redirect($this->view->url(array(), 'default', true));
        } else { // Otherwise try to login?
          $info = $twitterTable->select()
            ->from($twitterTable)
            ->where('twitter_uid = ?', $accountInfo->id)
            ->query()
            ->fetch();

          if (empty($info)) {
            // They do not have an account
            $_SESSION['twitter_signup'] = true;
            return $this->_helper->redirector->gotoRoute(array( //'action' => 'twitter',
            ), 'user_signup', true);
          } else {
            Zend_Auth::getInstance()->getStorage()->write($info['user_id']);

            // Register login
            $viewer = Engine_Api::_()->getItem('user', $info['user_id']);
            $viewer->lastlogin_date = date("Y-m-d H:i:s");

            if ('cli' !== PHP_SAPI) {
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
            return $this->redirect($this->view->url(array(), 'default', true));
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
        $twitterOauth->getRequestToken('https://twitter.com/oauth/request_token',
          'http://' . $_SERVER['HTTP_HOST'] . $this->view->url());

        $_SESSION['twitter_token'] = $twitterOauth->getToken();
        $_SESSION['twitter_secret'] = $twitterOauth->getTokenSecret();

        $url = $twitterOauth->getAuthorizeUrl('http://twitter.com/oauth/authenticate');

        return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
      }
    } catch (Services_Twitter_Exception $e) {
      if (in_array($e->getCode(), array(500, 502, 503))) {
        $this->view->error = 'Twitter is currently experiencing technical issues, please try again later.';
        return;
      } else {
        throw $e;
      }
    } catch (Exception $e) {
      throw $e;
    }
  }
// } AuthController
// Edit Controller {
  public function editInit()
  {
    if (!Engine_Api::_()->core()->hasSubject()) {
      // Can specifiy custom id
      $id = $this->_getParam('id', null);
      $subject = null;
      if (null === $id) {
        $subject = Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->core()->setSubject($subject);
      } else {
        $subject = Engine_Api::_()->getItem('user', $id);
        Engine_Api::_()->core()->setSubject($subject);
      }
    }

    if (!empty($id)) {
      $params = array('params' => array('id' => $id));
    } else {
      $params = array();
    }
    // Set up navigation
    $this->navigation = Engine_Api::_()
      ->getApi('menus', 'apptouch')
      ->getNavigation('user_edit', array('params' => array('id' => $id)));

    // Set up require's
    $this->_helper->requireUser();
    $this->_helper->requireSubject('user');
    $this->_helper->requireAuth()->setAuthParams(
      null,
      null,
      'edit'
    );

  }

  public function editProfileAction()
  {
    $user = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();


    // General form w/o profile type
    $aliasedFields = $user->fields()->getFieldsObjectsByAlias();
    $topLevelId = 0;
    $topLevelValue = null;
    if (isset($aliasedFields['profile_type'])) {
      $aliasedFieldValue = $aliasedFields['profile_type']->getValue($user);
      $topLevelId = $aliasedFields['profile_type']->field_id;
      $topLevelValue = (is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null);
      if (!$topLevelId || !$topLevelValue) {
        $topLevelId = null;
        $topLevelValue = null;
      }
    }

    // Get form
    $form = new Fields_Form_Standard(array(
      'item' => Engine_Api::_()->core()->getSubject(),
      'topLevelId' => $topLevelId,
      'topLevelValue' => $topLevelValue,
    ));
    //$form->generate();

    // Not posting
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $form->saveValues();

      // Update display name
      $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
      $user->setDisplayName($aliasValues);
      //$user->modified_date = date('Y-m-d H:i:s');
      $user->save();

      // update networks
      Engine_Api::_()->network()->recalculate($user);

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }
    $this
      ->add($this->component()->crumb($this->component()->getNavigation(Engine_Api::_()
        ->getApi('menus', 'apptouch')
        ->getNavigation('user_edit', array('params' => array('id' => $this->_getParam('id', null)))))))
      ->add($this->component()->form($form))
      ->renderContent();
  }

  public function editPhotoAction()
  {
    $user = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    // Get form
    $form = new User_Form_Edit_Photo();

    if (empty($user->photo_id)) {
      $form->removeElement('remove');
    }
    $this
      ->add($this->component()->crumb($this->component()->getNavigation(Engine_Api::_()
        ->getApi('menus', 'apptouch')
        ->getNavigation('user_edit', array('params' => array('id' => $this->_getParam('id', null)))))));

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }
    $picupFiles = $this->getPicupFiles('Filedata');

    $form->getValues();
    $photo = null;
    if (empty($picupFiles))
      $photo = $form->Filedata->getFileName();
    else
      $photo = $picupFiles[0];
    // Uploading a new photo
    if ($photo) {
      $db = $user->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        $user->setPhoto($photo);

        $iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);

        // Insert activity
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update',
          '{item:$subject} added a new profile photo.', array('is_mobile' => true));

        // Hooks to enable albums to work
        if ($action) {
          $event = Engine_Hooks_Dispatcher::_()
            ->callEvent('onUserProfilePhotoUpload', array(
              'user' => $user,
              'file' => $iMain,
            ));

          $attachment = $event->getResponse();
          if (!$attachment) $attachment = $iMain;

          // We have to attach the user himself w/o album plugin
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
        }

        $db->commit();
      } // If an exception occurred within the image adapter, it's probably an invalid image
      catch (Engine_Image_Adapter_Exception $e) {
        $db->rollBack();
        $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
      } // Otherwise it's probably a problem with the database or the storage system (just throw it)
      catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    } // Resizing a photo
    else if ($form->getValue('coordinates') !== '') {
      $storage = Engine_Api::_()->storage();

      $iProfile = $storage->get($user->photo_id, 'thumb.profile');
      $iSquare = $storage->get($user->photo_id, 'thumb.icon');

      // Read into tmp file
      $pName = $iProfile->getStorageService()->temporary($iProfile);
      $iName = dirname($pName) . '/nis_' . basename($pName);

      list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));

      $image = Engine_Image::factory();
      $image->open($pName)
        ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
        ->write($iName)
        ->destroy();

      $iSquare->store($iName);

      // Remove temp files
      @unlink($iName);
    }
    $this->add($this->component()->form($form))
      ->renderContent();

  }

  public function editRemovePhotoAction()
  {
    // Get form
    $form = new User_Form_Edit_RemovePhoto();

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }


    $user = Engine_Api::_()->core()->getSubject();
    $user->photo_id = 0;
    $user->save();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your photo has been removed.');

    $this->redirect('refresh');
  }

  public function editExternalPhotoAction()
  {
    if (!$this->_helper->requireSubject()->isValid()) return;
    $user = Engine_Api::_()->core()->getSubject();

    // Get photo
    $photo = Engine_Api::_()->getItemByGuid($this->_getParam('photo'));
    if (!$photo || !($photo instanceof Core_Model_Item_Abstract) || empty($photo->photo_id)) {
      $this->_forward('requiresubject', 'error', 'core');
      return;
    }

    if (!$photo->authorization()->isAllowed(null, 'view')) {
      $this->_forward('requireauth', 'error', 'core');
      return;
    }


    // Make form
    $form = new User_Form_Edit_ExternalPhoto();
    $this->view->photo = $photo;

    if (!$this->getRequest()->isPost()) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = $user->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      // Get the owner of the photo
      $photoOwnerId = null;
      if (isset($photo->user_id)) {
        $photoOwnerId = $photo->user_id;
      } else if (isset($photo->owner_id) && (!isset($photo->owner_type) || $photo->owner_type == 'user')) {
        $photoOwnerId = $photo->owner_id;
      }

      // if it is from your own profile album do not make copies of the image
      if ($photo instanceof Album_Model_Photo &&
        ($photoParent = $photo->getParent()) instanceof Album_Model_Album &&
        $photoParent->owner_id == $photoOwnerId &&
        $photoParent->type == 'profile'
      ) {

        // ensure thumb.icon and thumb.profile exist
        $newStorageFile = Engine_Api::_()->getItem('storage_file', $photo->file_id);
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        if ($photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.profile')) {
          try {
            $tmpFile = $newStorageFile->temporary();
            $image = Engine_Image::factory();
            $image->open($tmpFile)
              ->resize(300, 600)
              ->write($tmpFile)
              ->destroy();
            $iProfile = $filesTable->createFile($tmpFile, array(
              'parent_type' => $user->getType(),
              'parent_id' => $user->getIdentity(),
              'user_id' => $user->getIdentity(),
              'name' => basename($tmpFile),
            ));
            $newStorageFile->bridge($iProfile, 'thumb.profile');
            @unlink($tmpFile);
          } catch (Exception $e) {
            echo $e;
            die();
          }
        }
        if ($photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.icon')) {
          try {
            $tmpFile = $newStorageFile->temporary();
            $image = Engine_Image::factory();
            $image->open($tmpFile);
            $size = min($image->height, $image->width);
            $x = ($image->width - $size) / 2;
            $y = ($image->height - $size) / 2;
            $image->resample($x, $y, $size, $size, 48, 48)
              ->write($tmpFile)
              ->destroy();
            $iSquare = $filesTable->createFile($tmpFile, array(
              'parent_type' => $user->getType(),
              'parent_id' => $user->getIdentity(),
              'user_id' => $user->getIdentity(),
              'name' => basename($tmpFile),
            ));
            $newStorageFile->bridge($iSquare, 'thumb.icon');
            @unlink($tmpFile);
          } catch (Exception $e) {
            echo $e;
            die();
          }
        }

        // Set it
        $user->photo_id = $photo->file_id;
        $user->save();

        // Insert activity
        // @todo maybe it should read "changed their profile photo" ?
        $action = Engine_Api::_()->getDbtable('actions', 'activity')
          ->addActivity($user, $user, 'profile_photo_update',
            '{item:$subject} changed their profile photo.', array('is_mobile' => true));
        if ($action) {
          // We have to attach the user himself w/o album plugin
          Engine_Api::_()->getDbtable('actions', 'activity')
            ->attachActivity($action, $photo);
        }
      } // Otherwise copy to the profile album
      else {
        $user->setPhoto($photo);

        // Insert activity
        $action = Engine_Api::_()->getDbtable('actions', 'activity')
          ->addActivity($user, $user, 'profile_photo_update',
            '{item:$subject} added a new profile photo.', array('is_mobile' => true));

        // Hooks to enable albums to work
        $newStorageFile = Engine_Api::_()->getItem('storage_file', $user->photo_id);
        $event = Engine_Hooks_Dispatcher::_()
          ->callEvent('onUserProfilePhotoUpload', array(
            'user' => $user,
            'file' => $newStorageFile,
          ));

        $attachment = $event->getResponse();
        if (!$attachment) {
          $attachment = $newStorageFile;
        }

        if ($action) {
          // We have to attach the user himself w/o album plugin
          Engine_Api::_()->getDbtable('actions', 'activity')
            ->attachActivity($action, $attachment);
        }
      }

      $db->commit();
    } // Otherwise it's probably a problem with the database or the storage system (just throw it)
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Set as profile photo'));
  }

  public function editClearStatusAction()
  {
    $this->view->status = false;

    if ($this->getRequest()->isPost()) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $viewer->status = '';
      $viewer->status_date = '00-00-0000';
      // twitter-style handling
      // $lastStatus = $viewer->status()->getLastStatus();
      // if( $lastStatus ) {
      //   $viewer->status = $lastStatus->body;
      //   $viewer->status_date = $lastStatus->creation_date;
      // }
      $viewer->save();

      $this->view->status = true;
    }
  }

// } Edit Controller


//  ProfileController {
  public function profileInit()
  {
    $subject = null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      $id = $this->_getParam('id');

      // use viewer ID if not specified
      //if( is_null($id) )
      //  $id = Engine_Api::_()->user()->getViewer()->getIdentity();

      if (null !== $id) {
        $subject = Engine_Api::_()->user()->getUser($id);
        if ($subject->getIdentity()) {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('user');
    $this->_helper->requireAuth()->setNoForward()->setAuthParams(
      $subject,
      Engine_Api::_()->user()->getViewer(),
      'view'
    );
  }

  public function profileIndexAction()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('badges')) {
      $table = Engine_Api::_()->getDbTable('badges', 'hebadge');
      $paginator = $table->getMemberPaginator($subject);
      $paginator->setItemCountPerPage(21);
      $paginator->setCurrentPageNumber($this->_getParam('page'));
      $this->add($this->component()->profileBadgesList($paginator));
    }

    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_profile;
    if (!$require_check && !$this->_helper->requireUser()->isValid()) {
      return;
    }
    if ($this->_hasParam('action_id') && $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'))) {
      return $this->redirect($this->view->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'view', 'action_id' => $action->getIdentity(), 'comments' => 'write'), 'default', true));
    }

    // Check enabled
    if (!$subject->enabled && !$viewer->isAdmin()) {
      $this->_forward('requireauth', 'error', 'core');
    }

    // Check block
    if ($viewer->isBlockedBy($subject) && !$viewer->isAdmin()) {
      $this->_forward('requireauth', 'error', 'core');
    }

    // Increment view count
    if (!$subject->isSelf($viewer)) {
      $subject->view_count++;
      $subject->save();
    }

    $this->setFormat('profile');
    if (Engine_Api::_()->getApi('core', 'apptouch')->isTabletMode()) {
      $this->addPageInfo('fields', $this->getHelper('fields')->toArray($subject));
    }

    $this->renderContent();
  }


// Tabs {
  public function tabFriends($active = false)
  {
    // Don't render this if friendships are disabled
    if (!Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible) {
      return;
    }

    // Get subject and check auth
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject('user');
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return;
    }

    // Multiple friend mode
    $select = $subject->membership()->getMembersOfSelect();
    //$this->view->friends =
    $friends = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Set item count per page and current page number
    //    $paginator->setItemCountPerPage($paginator->getTotalItemCount());

    // Get stuff
    $ids = array();
    foreach ($friends as $friend) {
      $ids[] = $friend->resource_id;
    }
    //    $this->view->friendIds =
    $ids;

    // Get the items
    $friendUsers = array();
    foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser) {
      $friendUsers[$friendUser->getIdentity()] = $friendUser;
    }

    // Get lists if viewing own profile
    if ($viewer->isSelf($subject)) {
      // Get lists
      $listTable = Engine_Api::_()->getItemTable('user_list');
      //      $this->view->lists =
      $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));

      $listIds = array();
      foreach ($lists as $list) {
        $listIds[] = $list->list_id;
      }

      // Build lists by user
      $listItems = array();
      $listsByUser = array();
      if (!empty($listIds)) {
        $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
        $listItemSelect = $listItemTable->select()
          ->where('list_id IN(?)', $listIds)
          ->where('child_id IN(?)', $ids);
        $listItems = $listItemTable->fetchAll($listItemSelect);
        foreach ($listItems as $listItem) {
          //$list = $lists->getRowMatching('list_id', $listItem->list_id);
          //$listsByUser[$listItem->child_id][] = $list;
          $listsByUser[$listItem->child_id][] = $listItem->list_id;
        }
      }
      //      $this->view->listItems = $listItems;
      //      $this->view->listsByUser = $listsByUser;
    }

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return;
    }
    $paginatorPages = $paginator->getPages();
    $items = array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',
    );
    foreach ($paginator as $item) {

      if (!isset($friendUsers[$item->resource_id])) continue;
      $item = $friendUsers[$item->resource_id];

      $std = array(
        'title' => $item->getTitle(),
        'descriptions' => array(
          $item->status()
        ),
        'href' => $item->getHref(),
        'photo' => $item->getPhotoUrl('thumb.normal'),
      );

      $items['items'][] = $std;
    }
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    if ($active)
      $this->add($this->component()->customComponent('itemList', $items), 7)//        ->add($this->component()->paginator($paginator), 8)
      ;
    //    } prepare
    return array(
      'showContent' => false,
      'response' => $paginator //Zend_Paginator::factory($friendUsers)
    );
  }

  public function tabGroups($active = false)
  {
    // Get paginator
    $subject = Engine_Api::_()->core()->getSubject('user');
    $membership = Engine_Api::_()->getDbtable('membership', 'group');
    //    $this->view->paginator =
    $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($subject));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return;
    }
    $paginator->setItemCountPerPage(5); // todo tmp

    return $paginator;
  }

  public function tabMusic($active = false)
  {
    // Get paginator
    $paginator = Engine_Api::_()->music()->getPlaylistPaginator(array(
      'user' => Engine_Api::_()->core()->getSubject('user')->getIdentity(),
      'sort' => 'creation_date',
      'searchBit' => 1,
      //'limit' => 10, // items per page
    ));

    // Set item count per page and current page number
    //    $paginator->setItemCountPerPage(2);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return;
    }

    return $paginator;
  }

  public function tabPolls($active = false)
  {
    // Get paginator
    $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator(array(
      'user_id' => Engine_Api::_()->core()->getSubject('user')->getIdentity(),
      'sort' => "creation_date",
    ));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return;
    }

    return $paginator;
  }

  public function tabVideos($active = false)
  {
    $table = Engine_Api::_()->getItemTable('video');
    $params = array(
      'user_id' => Engine_Api::_()->core()->getSubject('user')->getIdentity(),
      'status' => 1,
      'search' => 1
    );

    $select = Engine_Api::_()->video()->getVideosSelect($params);

    if ($this->_getParam('search', false)) {
      $select->where($table->info('name') . '.title LIKE ? OR ' . $table->info('name') . '.description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    // Get paginator
    $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0 && !$this->_getParam('search', false)) {
      return;
    } else {
      $this->_childCount = $paginator->getTotalItemCount(); // todo how to show count?
    }

    if ($active) {
      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));


      $this->add($this->component()->itemSearch($form), 10)
        ->add($this->component()->itemList($paginator, 'browseVideoList', array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))), 11)//        ->add($this->component()->paginator($paginator), 12)
      ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabForumPosts($active = false)
  {

    // Get paginator
    //    $this->view->subject =
    $subject = Engine_Api::_()->core()->getSubject('user');
    $postsTable = Engine_Api::_()->getDbtable('posts', 'forum');
    $postsSelect = $postsTable->select()
      ->where('user_id = ?', $subject->getIdentity())
      ->order('creation_date DESC');

    //    $this->view->paginator =
    $paginator = Zend_Paginator::factory($postsSelect);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return;
    }

    //    prepare {

    $paginatorPages = $paginator->getPages();
    $items = array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',
    );
    foreach ($paginator as $item) {
      $topic = $item->getParent();
      $forum = $topic->getParent();

      $std = array(
        'title' => ucfirst($this->view->translate('in the topic %1$s', $topic->__toString()) . '<br/>' .
          $this->view->translate('in the forum %1$s', $forum->__toString())),
        'descriptions' => array(
          $item->getDescription()
        ),
        'href' => $item->getHref(),
        'creation_date' => $this->view->locale()->toDateTime(strtotime($item->creation_date)),
      );
      $items[] = $std;
    }
    if ($active)
      $this->add($this->component()->customComponent('itemList', array('items' => $items)), 7)//        ->add($this->component()->paginator($paginator), 8)
      ;
    //    } prepare
    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabEvents($active = false)
  {
    // Get paginator
    $membership = Engine_Api::_()->getDbtable('membership', 'event');
    $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect(Engine_Api::_()->core()->getSubject('user'))->order('starttime DESC'));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return;
    }

    return $paginator;
  }

  public function tabBlogs($active = false)
  {
    // Get paginator
    $paginator = Engine_Api::_()->getItemTable('blog')->getBlogsPaginator(array(
      'orderby' => 'creation_date',
      'draft' => '0',
      'user_id' => Engine_Api::_()->core()->getSubject()->getIdentity(),
    ));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2)); // todo count per page
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return;
    }

    return $paginator;
  }

  public function tabClassifieds($active = false)
  {
    // Get paginator
    $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator(array(
      'orderby' => 'creation_date',
      'user_id' => Engine_Api::_()->core('user')->getSubject()->getIdentity(),
    ));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2)); //todo count per page
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return;
    }
    return $paginator;
  }

  public function tabAlbums($active = false)
  {
    $select = Engine_Api::_()->getItemTable('album')->getAlbumSelect(array(
      'owner' => Engine_Api::_()->core('user')->getSubject(),
      'search' => 1
    ));

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    // Get paginator
    $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0 && !$this->_getParam('search', false)) {
      return;
    }

    if ($active) {
      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));


      $this->add($this->component()->itemSearch($form), 10)
        ->add($this->component()->itemList($paginator, 'browseAlbumList', array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))), 11)//        ->add($this->component()->paginator($paginator), 12)
      ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabAdvalbum($active = false)
  {
    $album_table = Engine_Api::_()->getItemTable('advalbum_album');
    $select = $album_table->select();
    $options = array(
      'owner' => Engine_Api::_()->core('user')->getSubject(),
      'search' => 1
    );
    if (!empty($options['owner']) &&
      $options['owner'] instanceof Core_Model_Item_Abstract
    ) {
      $select
        ->where('owner_type = ?', $options['owner']->getType())
        ->where('owner_id = ?', $options['owner']->getIdentity())
        ->order('modified_date DESC');
    }

    if (!empty($options['search']) && is_numeric($options['search'])) {
      $select->where('search = ?', $options['search']);
    }


    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    // Get paginator
    $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0 && !$this->_getParam('search', false)) {
      return;
    }

    if ($active) {
      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));


      $this->add($this->component()->itemSearch($form), 10)
        ->add($this->component()->itemList($paginator, null, array('listPaginator' => true,)), 11)//              ->add($this->component()->paginator($paginator), 12)
      ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabPages($active = false)
  {
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject('user');
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($subject->getType() != 'user') {
      return false;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('membership', 'page');
    $itemTable = Engine_Api::_()->getDbTable('pages', 'page');

    $itName = $itemTable->info('name');
    $mtName = $table->info('name');
    $col = current($itemTable->info('primary'));

    $select = $itemTable->select()
      ->setIntegrityCheck(false)
      ->from($itName)
      ->joinLeft($mtName, "`{$mtName}`.`resource_id` = `{$itName}`.`{$col}`", array('admin_title' => "{$mtName}.title"))
      ->where("`{$mtName}`.`user_id` = ?", $subject->getIdentity())
      ->where("`{$mtName}`.`active` = 1")
      ->where("`{$itName}`.`approved` = 1");

    if ($active && $this->_getParam('search', false)) {
      $select->where($itName . '.title LIKE ? OR ' . $itName . '.description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return false;
    }

    $ids = array();
    foreach ($paginator as $page) {
      $ids[] = $page->getIdentity();
    }

    return $paginator;
  }


  public function tabGifts($active = false)
  {
    /**
     * @var $table Hegift_Model_DbTable_Recipients
     * @var $subject User_Model_User
     */

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      if (!($user = Engine_Api::_()->getItem('user', $this->_getParam('user_id', 0)))) {
        return false;
      }
      Engine_Api::_()->core()->setSubject($user);
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return false;
    }

    // Member type
    $subject = Engine_Api::_()->core()->getSubject();
    $table = Engine_Api::_()->getDbTable('recipients', 'hegift');

    $page = $this->_getParam('page', 1);
    $paginator = $table->getPaginator(array('user_id' => $subject->getIdentity(), 'action_name' => 'received', 'page' => $page, 'ipp' => 20));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return false;
    }

    if ($active) {
      $paginatorPages = $paginator->getPages();
      $data = array(
        'listPaginator' => true,
        'pageCount' => $paginatorPages->pageCount,
        'next' => @$paginatorPages->next,
        'paginationParam' => 'page',
      );
      $data['items'] = array();
      $description = $this->view->translate('HEGIFT_sent you this gift ') . '<b>';

      foreach ($paginator as $rs) {
        $user = $rs->getUser('received');
        $gift = $rs->getGift();

        $des = $description . $rs->getPrivacy() . '</b> <br>' .
          $this->view->translate('HEGIFT_Sent %s ', $this->view->timestamp($rs->send_date));

        if ($rs->getMessage()) {
          $des = $des . '<br><i>' . $rs->getMessage() . '</i>';
        }

        $url = $this->view->url(array('action' => $gift->getTypeName(), 'gift_id' => $gift->getIdentity(), 'sender_id' => $rs->subject_id), 'hegift_own', true);

        $data['items'][] = array(
          'title' => $user->getTitle(),
          'descriptions' => array($des),
          'href' => $url,
          'photo' => $gift->getPhotoUrl('thumb.normal'),
          'attrsA' => array('data-rel' => 'dialog'),
        );
      }

      $this->add($this->component()->customComponent('itemList', $data), 15)//        ->add($this->component()->paginator($paginator), 16)
      ;
    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

  public function tabCheckin($active = false)
  {
    return $this->component()->checkin();
  }

  public function tabArticles($active = false)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $user_type = $this->_getParam('user_type', 'owner');

    if ($user_type == 'viewer') {
      if (!$viewer->getIdentity()) {
        return false;
      } else {
        $user = $viewer;
      }
    } else {
      // owner mode
      if (!Engine_Api::_()->core()->hasSubject()) {
        return false;
      }

      // Get subject and check auth
      $subject = Engine_Api::_()->core()->getSubject();

      if (!($subject instanceof Core_Model_Item_Abstract)) {
        return false;
      }

      if (!$subject->authorization()->isAllowed($viewer, 'view')) {
        return false;
      }

      if (!($subject instanceof User_Model_User)) {
        $user = $subject->getOwner('user');
      } else {
        $user = $subject;
      }
    }

    if (!($user instanceof User_Model_User) || !$user->getIdentity()) {
      return false;
    }

    $params = array(
      'published' => 1,
      'search' => 1,
      'limit' => $this->_getParam('max', 5),
      'order' => $this->_getParam('order', 'recent'),
      'period' => $this->_getParam('period'),
      'keyword' => $this->_getParam('search'),
      'category' => $this->_getParam('category'),
    );

    if ($this->_getParam('featured', 0)) {
      $params['featured'] = 1;
    }

    if ($this->_getParam('sponsored', 0)) {
      $params['sponsored'] = 1;
    }

    $params['user'] = $user;

    $paginator = Engine_Api::_()->article()->getArticlesPaginator($params);

    $showphoto = $this->_getParam('showphoto', $this->view->display_style == 'narrow' ? 0 : 1);
    $showmeta = $this->_getParam('showmeta', $this->view->display_style == 'narrow' ? 0 : 1);
    $showdescription = $this->_getParam('showdescription', $this->view->display_style == 'narrow' ? 0 : 1);

    $showmemberarticleslink = $this->_getParam('showmemberarticleslink', $this->view->display_style == 'narrow' ? 0 : 1);

    $order = $params['order'];

    // Add count to title if configured

    if ($paginator->getTotalItemCount() <= 0) {
      return false;
    }

    return $paginator;
  }

  public function tabLikes($active = false)
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
    } else {
      $subject = $viewer;
    }

    if ($subject->getType() != 'user') {
      return false;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'interest')) {
      return false;
    }


    $settings = Engine_Api::_()->getApi('settings', 'core');
    $period = $settings->getSetting('like.profile_period', 1);
    $type = $this->_getParam('type', 'all');

    $item_count = Engine_Api::_()->like()->getLikedCount($subject);
    if (!$item_count) {
      return false;
    }

    if ($active) {
      $items = Engine_Api::_()->like()->getLikedItems($subject);
      shuffle($items);

      if ($period) { //for week and month
        $item_count = Engine_Api::_()->like()->getLikedCount($subject, $type);
        $items = Engine_Api::_()->like()->getLikedItems($subject, false, $type);
        shuffle($items);

        $all_btn = $this->dom()->new_('a', array('data-role' => 'button', 'href' => $subject->getHref() . '/tab/likes/type/all'), $this->view->translate('LIKE_Overall'));
        $month_btn = $this->dom()->new_('a', array('data-role' => 'button', 'href' => $subject->getHref() . '/tab/likes/type/month'), $this->view->translate('LIKE_This Month'));
        $week_btn = $this->dom()->new_('a', array('data-role' => 'button', 'href' => $subject->getHref() . '/tab/likes/type/week'), $this->view->translate('LIKE_This Week'));
        $group = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-type' => 'horizontal'));

        $group->append($all_btn)->append($month_btn)->append($week_btn);

        $this->add($this->component()->html($group), 10);
      }

      $data = array();
      $data['items'] = array();

      foreach ($items as $item) {
        $data['items'][] = array(
          'title' => $item->getTitle(),
          'descriptions' => array($item->getDescription()),
          'href' => $item->getHref(),
          'photo' => $item->getPhotoUrl('thumb.normal'),
        );
      }

      $count_txt = $this->view->translate(array("like_%s item", "like_%s items", $item_count), ($item_count));
      $href = $this->view->url(array('action' => 'see-liked', 'user_id' => $subject->getIdentity(), 'period_type' => $type), 'like_default');
      $html_el = $this->dom()->new_('a', array('href' => $href, 'data-rel' => 'dialog'), $count_txt);
      $this->add($this->component()->html($html_el), 11)
        ->add($this->component()->customComponent('itemList', $data), 12);
    }

    $href = $this->view->url(array('action' => 'see-liked', 'user_id' => $subject->getIdentity(), 'period_type' => $type), 'like_default');

    $dialog_title = $this->view->translate("like_%s's likes", $subject->getTitle());
    return true;
  }

// } Tabs

//  } ProfileController

// FriendsController {
  public function friendsInit()
  {
    // Try to set subject
    $user_id = $this->_getParam('user_id', null);
    if ($user_id && !Engine_Api::_()->core()->hasSubject()) {
      $user = Engine_Api::_()->getItem('user', $user_id);
      if ($user) {
        Engine_Api::_()->core()->setSubject($user);
      }
    }

    // Check if friendships are enabled
    if ($this->getRequest()->getActionName() !== 'suggest' &&
      !Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible
    ) {
      $this->_helper->requireAuth()->forward();
    }
  }

  public function friendsListAddAction()
  {
    $list_id = (int)$this->_getParam('list_id');
    $friend_id = (int)$this->_getParam('friend_id');

    $user = Engine_Api::_()->user()->getViewer();
    $friend = Engine_Api::_()->getItem('user', $friend_id);

    // Check params
    if (!$user->getIdentity() || !$friend || !$list_id) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check list
    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->find($list_id)->current();
    if (!$list || $list->owner_id != $user->getIdentity()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Missing list/not authorized');
    }

    // Check if already target status
    if ($list->has($friend)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already in list');
      return;
    }

    $list->add($friend);

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Member added to list.');
    Engine_Api::_()->core()->setSubject($user);
  }

  public function friendsListRemoveAction()
  {
    $list_id = (int)$this->_getParam('list_id');
    $friend_id = (int)$this->_getParam('friend_id');

    $user = Engine_Api::_()->user()->getViewer();
    $friend = Engine_Api::_()->getItem('user', $friend_id);

    // Check params
    if (!$user->getIdentity() || !$friend || !$list_id) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check list
    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->find($list_id)->current();
    if (!$list || $list->owner_id != $user->getIdentity()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Missing list/not authorized');
    }

    // Check if already target status
    if (!$list->has($friend)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already not in list');
      return;
    }

    $list->remove($friend);

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Member removed from list.');
    Engine_Api::_()->core()->setSubject($user);
  }

  public function friendsListCreateAction()
  {
    $title = (string)$this->_getParam('title');
    $friend_id = (int)$this->_getParam('friend_id');
    $user = Engine_Api::_()->user()->getViewer();
    $friend = Engine_Api::_()->getItem('user', $friend_id);

    if (!$user->getIdentity() || !$title) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->createRow();
    $list->owner_id = $user->getIdentity();
    $list->title = $title;
    $list->save();

    if ($friend && $friend->getIdentity()) {
      $list->add($friend);
    }

    $this->view->status = true;
    $this->view->message = 'List created.';
    $this->view->list_id = $list->list_id;
    Engine_Api::_()->core()->setSubject($user);
  }

  public function friendsListDeleteAction()
  {
    $list_id = (int)$this->_getParam('list_id');
    $user = Engine_Api::_()->user()->getViewer();

    // Check params
    if (!$user->getIdentity() || !$list_id) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check list
    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->find($list_id)->current();
    if (!$list || $list->owner_id != $user->getIdentity()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Missing list/not authorized');
    }

    $list->delete();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('List deleted');
  }

  public function friendsAddAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if (null == ($user_id = $this->_getParam('user_id')) ||
      null == ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // check that user is not trying to befriend 'self'
    if ($viewer->isSelf($user)) {
      return $this->redirect('parentRefresh', Zend_Registry::get('Zend_Translate')->_('You cannot befriend yourself.'));
    }

    // check that user is already friends with the member
    if ($user->membership()->isMember($viewer)) {
      return $this->redirect('parentRefresh', Zend_Registry::get('Zend_Translate')->_('You are already friends with this member.'));
    }

    // check that user has not blocked the member
    if ($viewer->isBlocked($user)) {
      return $this->redirect('parentRefresh', Zend_Registry::get('Zend_Translate')->_('Friendship request was not sent because you blocked this member.'));
    }

    // Make form
    $form = new User_Form_Friends_Add();

    if (!$this->getRequest()->isPost()) {
      //      $this->view->status = false;
      //      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return $this->attrPage('data-role', 'dialog')->add($this->component()->form($form))
        ->renderContent();
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {

      // send request
      $user->membership()
        ->addMember($viewer)
        ->setUserApproved($viewer);

      if (!$viewer->membership()->isUserApprovalRequired() && !$viewer->membership()->isReciprocal()) {
        // if one way friendship and verification not required

        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')
          ->addActivity($viewer, $user, 'friends_follow', '{item:$subject} is now following {item:$object}.', array('is_mobile' => true));

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($user, $viewer, $viewer, 'friend_follow');

        $message = Zend_Registry::get('Zend_Translate')->_("You are now following this member.");

      } else if (!$viewer->membership()->isUserApprovalRequired() && $viewer->membership()->isReciprocal()) {
        // if two way friendship and verification not required

        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')
          ->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));
        Engine_Api::_()->getDbtable('actions', 'activity')
          ->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($user, $viewer, $user, 'friend_accepted');

        $message = Zend_Registry::get('Zend_Translate')->_("You are now friends with this member.");

      } else if (!$user->membership()->isReciprocal()) {
        // if one way friendship and verification required

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($user, $viewer, $user, 'friend_follow_request');

        $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");

      } else if ($user->membership()->isReciprocal()) {
        // if two way friendship and verification required

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($user, $viewer, $user, 'friend_request');

        $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");
      }

      $db->commit();

      return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Your friend request has been sent.'), true);

    } catch (Exception $e) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
      return;
    }
  }

  public function friendsCancelAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if (null == ($user_id = $this->_getParam('user_id')) ||
      null == ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $form = new User_Form_Friends_Cancel();

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
            return $this->attrPage('data-role', 'dialog')->add($this->component()->form($form))
        ->renderContent();
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      $user->membership()->removeMember($viewer);

      // Set the requests as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
        ->getNotificationBySubjectAndType($user, $viewer, 'friend_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
        ->getNotificationBySubjectAndType($user, $viewer, 'friend_follow_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();

      return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Your friend request has been cancelled.'), true);

    } catch (Exception $e) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function friendsConfirmAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if (null == ($user_id = $this->_getParam('user_id')) ||
      null == ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $form = new User_Form_Friends_Confirm();

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      //      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return $this->add($this->component()->form($form))
        ->renderContent();
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return $this->add($this->component()->form($form))
        ->renderContent();

      return;
    }

    $friendship = $viewer->membership()->getRow($user);
    if ($friendship->active) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already friends');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer->membership()->setResourceApproved($user);

      // Add activity
      if (!$user->membership()->isReciprocal()) {
        Engine_Api::_()->getDbtable('actions', 'activity')
          ->addActivity($user, $viewer, 'friends_follow', '{item:$subject} is now following {item:$object}.', array('is_mobile' => true));
      } else {
        Engine_Api::_()->getDbtable('actions', 'activity')
          ->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));
        Engine_Api::_()->getDbtable('actions', 'activity')
          ->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.', array('is_mobile' => true));
      }

      // Add notification
      if (!$user->membership()->isReciprocal()) {
        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($user, $viewer, $user, 'friend_follow_accepted');
      } else {
        Engine_Api::_()->getDbtable('notifications', 'activity')
          ->addNotification($user, $viewer, $user, 'friend_accepted');
      }

      // Set the requests as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
        ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
        ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      // Increment friends counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.friendships');
      if (Engine_Api::_()->apptouch()->isApp()) {
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('ios.user.friendships');
      } else {
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('apptouch.user.friendships');
      }


      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('You are now friends with %s');
      $message = sprintf($message, $user->__toString());

      return $this->redirect('refresh', $message, true);
    } catch (Exception $e) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function friendsRejectAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if (null == ($user_id = $this->_getParam('user_id')) ||
      null == ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $form = new User_Form_Friends_Reject();

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      if ($viewer->membership()->isMember($user)) {
        $viewer->membership()->removeMember($user);
      }

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
        ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
        ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('You ignored a friend request from %s');
      $message = sprintf($message, $user->__toString());

      return $this->redirect('refresh', $message, true);
    } catch (Exception $e) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function friendsIgnoreAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if (null == ($user_id = $this->_getParam('user_id')) ||
      null == ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $form = new User_Form_Friends_Reject();

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      return $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer->membership()->removeMember($user);

      // Set the requests as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
        ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
        ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('You ignored %s\'s request to follow you');
      $message = sprintf($message, $user->__toString());

      $this->redirect('refresh', $message, true);
    } catch (Exception $e) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function friendsRemoveAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if (null == ($user_id = $this->_getParam('user_id')) ||
      null == ($user = Engine_Api::_()->getItem('user', $user_id))
    ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $form = new User_Form_Friends_Remove();

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      if ($this->_getParam('rev')) {
        $viewer->membership()->removeMember($user);
      } else {
        $user->membership()->removeMember($viewer);
      }

      // Remove from lists?
      // @todo make sure this works with one-way friendships
      $user->lists()->removeFriendFromLists($viewer);
      $viewer->lists()->removeFriendFromLists($user);

      // Set the requests as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
        ->getNotificationBySubjectAndType($user, $viewer, 'friend_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
        ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
      if ($notification) {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('This person has been removed from your friends.');

      return $this->redirect('refresh', $message, true);
    } catch (Exception $e) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function friendsSuggestAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      $data = null;
    } else {
      $data = array();
      $table = Engine_Api::_()->getItemTable('user');
      $select = Engine_Api::_()->user()->getViewer()->membership()->getMembersObjectSelect();

      if ($this->_getParam('includeSelf', false)) {
        $data[] = array(
          'type' => 'user',
          'id' => $viewer->getIdentity(),
          'guid' => $viewer->getGuid(),
          'label' => $viewer->getTitle() . ' (you)',
          'photo' => $this->view->itemPhoto($viewer, 'thumb.icon'),
          'url' => $viewer->getHref(),
        );
      }

      if (0 < ($limit = (int)$this->_getParam('limit', 10))) {
        $select->limit($limit);
      }

      if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
        $select->where('`' . $table->info('name') . '`.`displayname` LIKE ?', '%' . $text . '%');
      }

      $ids = array();
      foreach ($select->getTable()->fetchAll($select) as $friend) {
        $data[] = array(
          'type' => 'user',
          'id' => $friend->getIdentity(),
          'guid' => $friend->getGuid(),
          'label' => $friend->getTitle(),
          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
          'url' => $friend->getHref(),
        );
        $ids[] = $friend->getIdentity();
        $friend_data[$friend->getIdentity()] = $friend->getTitle();
      }

      // first get friend lists created by the user
      $listTable = Engine_Api::_()->getItemTable('user_list');
      $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
      $listIds = array();
      foreach ($lists as $list) {
        $listIds[] = $list->list_id;
        $listArray[$list->list_id] = $list->title;
      }

      // check if user has friend lists
      if ($listIds) {
        // get list of friend list + friends in the list
        $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
        $uName = Engine_Api::_()->getDbtable('users', 'user')->info('name');
        $iName = $listItemTable->info('name');

        $listItemSelect = $listItemTable->select()
          ->setIntegrityCheck(false)
          ->from($iName, array($iName . '.listitem_id', $iName . '.list_id', $iName . '.child_id', $uName . '.displayname'))
          ->joinLeft($uName, "$iName.child_id = $uName.user_id")
          //->group("$iName.child_id")
          ->where('list_id IN(?)', $listIds);

        $listItems = $listItemTable->fetchAll($listItemSelect);

        $listsByUser = array();
        foreach ($listItems as $listItem) {
          $listsByUser[$listItem->list_id][$listItem->user_id] = $listItem->displayname;
        }

        foreach ($listArray as $key => $value) {
          if (!empty($listsByUser[$key])) {
            $data[] = array(
              'type' => 'list',
              'friends' => $listsByUser[$key],
              'label' => $value,
            );
          }
        }
      }
    }

    if ($this->_getParam('sendNow', true)) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  public function friendsRequestFriendAction()
  {
    $this->view->notification = $notification = $this->_getParam('notification');
  }

  public function friendsRequestFollowAction()
  {
    $this->view->notification = $notification = $this->_getParam('notification');
  }


// } FriendsController

// SignupController {
  public function signupInit()
  {

  }

  public function signupIndexAction()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (!$this->getRequest()->isPost()) { // Forget Old
      unset($_SESSION['TemporaryProfileImg']);
      unset($_SESSION['TemporaryProfileImgProfile']);
      unset($_SESSION['TemporaryProfileImgSquare']);
    }
    // Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // If the user is logged in, they can't sign up now can they?
    if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
      return $this->redirect($this->view->url(array('action' => 'home', 'format' => 'json'), 'user_general', true));
    }

    $formSequenceHelper = $this->_helper->formSequence;
    $stdSignup = Engine_Api::_()->getDbtable('signup', 'user')->fetchAll();
    foreach (Engine_Api::_()->getDbtable('signup', 'apptouch')->fetchAll() as $row) {
      $stdRow = $stdSignup->current();
      $stdSignup->next();
      if ($stdRow->enable && $row->enable == 1) {
        $class = $row->class;
        $instance = new $class;
        // ReCaptcha
        if ($class == 'Apptouch_Plugin_Signup_Account') {
          $instance->setForm(new Apptouch_Form_Signup_Account());
        }
        $formSequenceHelper->setPlugin($instance, $stdRow->order);
      }
    }
    // This will handle everything until done, where it will return true
    if (!$this->_helper->formSequence()) {
      $this
        ->add($this->component()->form($this->view->form))
        ->renderContent();
      return;

    }

    // Get viewer
    $viewer = Engine_Api::_()->user()->getViewer();

    // Run post signup hook
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserSignupAfter', $viewer);
    $responses = $event->getResponses();
    if ($responses) {
      foreach ($event->getResponses() as $response) {
        if (is_array($response)) {
          // Clear login status
          if (!empty($response['error'])) {
            Engine_Api::_()->user()->setViewer(null);
            Engine_Api::_()->user()->getAuth()->getStorage()->clear();
          }
          // Redirect
          if (!empty($response['redirect'])) {
            return $this->redirect($response['redirect']);
          }
        }
      }
    }

    // Handle subscriptions
    if (Engine_Api::_()->hasModuleBootstrap('payment')) {
      // Check for the user's plan
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
      if (!$subscriptionsTable->check($viewer)) {

        // Handle default payment plan
        $defaultSubscription = null;
        try {
          $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
          if ($subscriptionsTable) {
            $defaultSubscription = $subscriptionsTable->activateDefaultPlan($viewer);
            if ($defaultSubscription) {
              // Re-process enabled?
              $viewer->enabled = true;
              $viewer->save();
            }
          }
        } catch (Exception $e) {
          // Silence
        }

        if (!$defaultSubscription) {
          // Redirect to subscription page, log the user out, and set the user id
          // in the payment session
          $subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
          $subscriptionSession->user_id = $viewer->getIdentity();

          Engine_Api::_()->user()->setViewer(null);
          Engine_Api::_()->user()->getAuth()->getStorage()->clear();

          if (!empty($subscriptionSession->subscription_id)) {
            return $this->redirect($this->view->url(array('module' => 'payment',
              'controller' => 'subscription', 'action' => 'gateway'), 'default', true));
          } else {
            return $this->redirect($this->view->url(array('module' => 'payment',
              'controller' => 'subscription', 'action' => 'index'), 'default', true));
          }
        }
      }
    }

    // Handle email verification or pending approval
    if (!$viewer->enabled) {
      Engine_Api::_()->user()->setViewer(null);
      Engine_Api::_()->user()->getAuth()->getStorage()->clear();

      $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
      $confirmSession->approved = $viewer->approved;
      $confirmSession->verified = $viewer->verified;
      $confirmSession->enabled = $viewer->enabled;
      return $this->redirect($this->view->url(array('action' => 'confirm'), 'user_signup', true));
    } // Handle normal signup
    else {
      Engine_Api::_()->user()->getAuth()->getStorage()->write($viewer->getIdentity());
      Engine_Hooks_Dispatcher::getInstance()
        ->callEvent('onUserEnable', $viewer);
    }

    // Set lastlogin_date here to prevent issues with payment
    if ($viewer->getIdentity()) {
      $viewer->lastlogin_date = date("Y-m-d H:i:s");
      if ('cli' !== PHP_SAPI) {
        $ipObj = new Engine_IP();
        $viewer->lastlogin_ip = $ipObj->toBinary();
      }
      $viewer->save();
    }
    $this->view->userSession = 'start';
    return $this->redirect($this->view->url(array('action' => 'home'), 'user_general', true));
  }

  public function signupVerifyAction()
  {
    $verify = $this->_getParam('verify');
    $email = $this->_getParam('email');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // No code or email
    if (!$verify || !$email) {
      $this->view->error = $this->view->translate('The email or verification code was not valid.');
      $this->redirect($this->view->url(array(), 'user_login', true));
      return;
    }

    // Get verify user
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $user = $userTable->fetchRow($userTable->select()->where('email = ?', $email));

    if (!$user || !$user->getIdentity()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('The email does not match an existing user.');
      $this->redirect($this->view->url(array(), 'user_login', true));
      return;
    }

    // If the user is already verified, just redirect
    if ($user->verified) {
      $this->view->status = true;
      $this->redirect($this->view->url(array(), 'user_login', true));
      return;
    }

    // Get verify row
    $verifyTable = Engine_Api::_()->getDbtable('verify', 'user');
    $verifyRow = $verifyTable->fetchRow($verifyTable->select()->where('user_id = ?', $user->getIdentity()));

    if (!$verifyRow || $verifyRow->code != $verify) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('There is no verification info for that user.');
      $this->redirect($this->view->url(array(), 'user_login', true));
      return;
    }

    // Process
    $db = $verifyTable->getAdapter();
    $db->beginTransaction();

    try {

      $verifyRow->delete();
      $user->verified = 1;
      $user->save();

      if ($user->enabled) {
        Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserEnable', $user);
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->redirect($this->view->url(array(), 'user_login', true));
  }

  public function signupTakenAction()
  {
    $username = $this->_getParam('username');
    $email = $this->_getParam('email');

    // Sent both or neither username/email
    if ((bool)$username == (bool)$email) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid param count');
      return;
    }

    // Username must be alnum
    if ($username) {
      $validator = new Zend_Validate_Alnum();
      if (!$validator->isValid($username)) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid param value');
        //$this->view->errors = $validator->getErrors();
        return;
      }

      $table = Engine_Api::_()->getItemTable('user');
      $row = $table->fetchRow($table->select()->where('username = ?', $username)->limit(1));

      $this->view->status = true;
      $this->view->taken = ($row !== null);
      return;
    }

    if ($email) {
      $validator = new Zend_Validate_EmailAddress();
      if (!$validator->isValid($email)) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid param value');
        //$this->view->errors = $validator->getErrors();
        return;
      }

      $table = Engine_Api::_()->getItemTable('user');
      $row = $table->fetchRow($table->select()->where('email = ?', $email)->limit(1));

      $this->view->status = true;
      $this->view->taken = ($row !== null);
      return;
    }
  }

  public function signupConfirmAction()
  {
    $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
    $approved = $this->_getParam('approved', $confirmSession->approved);
    $verified = $this->_getParam('verified', $confirmSession->verified);
//    $enabled = $this->_getParam('verified', $confirmSession->enabled);
    if (!($verified || $approved)) {
      $this->add($this->component()->html($this->view->translate("Welcome! A verification message has been sent to your email address with instructions on how to activate your account. Once you have clicked the link provided in the email and we have approved your account, you will be able to sign in.")));
    } else if (!$verified) {
      $this->add($this->component()->html($this->view->translate("Welcome! A verification message has been sent to your email address with instructions for activating your account. Once you have activated your account, you will be able to sign in.")));
    } else if (!$approved) {
      $this->add($this->component()->html($this->view->translate("Welcome! Once we have approved your account, you will be able to sign in.")));
    }
    $this->add($this->component()->html('<a data-role="button" href="' . $this->view->url(array(), 'default', true) . '">' . $this->view->translate("OK, thanks!") . '</a>'));
    $this->renderContent();
  }

  public function signupResendAction()
  {
    $email = $this->_getParam('email');
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity() || !$email) {
      return $this->redirect($this->view->url(array(), 'default', true));
    }

    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $user = $userTable->fetchRow($userTable->select()->where('email = ?', $email));

    if (!$user) {
      $this->view->error = 'That email was not found in our records.';
      return;
    }
    if ($user->verified) {
      $this->view->error = 'That email has already been verified. You may now login.';
      return;
    }

    // resend verify email
    $verifyTable = Engine_Api::_()->getDbtable('verify', 'user');
    $verifyRow = $verifyTable->fetchRow($verifyTable->select()->where('user_id = ?', $user->user_id)->limit(1));

    if (!$verifyRow) {
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $verifyRow = $verifyTable->createRow();
      $verifyRow->user_id = $user->getIdentity();
      $verifyRow->code = md5($user->email
        . $user->creation_date
        . $settings->getSetting('core.secret', 'staticSalt')
        . (string)rand(1000000, 9999999));
      $verifyRow->date = $user->creation_date;
      $verifyRow->save();
    }

    $mailParams = array(
      'host' => $_SERVER['HTTP_HOST'],
      'email' => $user->email,
      'date' => time(),
      'recipient_title' => $user->getTitle(),
      'recipient_link' => $user->getHref(),
      'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
      'queue' => false,
    );

    $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
        'action' => 'verify',
        //'email' => $email,
        //'verify' => $verifyRow->code
      ), 'user_signup', true)
      . '?'
      . http_build_query(array('email' => $email, 'verify' => $verifyRow->code));

    Engine_Api::_()->getApi('mail', 'core')->sendSystem(
      $user,
      'core_verification',
      $mailParams
    );

    $this->add($this->component()->html('<h2>' . $this->view->translate("Verification Email") . '</h2>'));
    if ($this->view->error) {
      $this->add($this->component()->html('<p>' . $this->view->translate($this->view->error) . '</p>'))
        ->add($this->component()->html('<h3>' . $this->view->htmlLink(array('route' => 'default'), $this->view->translate('Back')) . '</h3>'));
    } else {
      $this->add($this->component()->html('<p>' . $this->view->translate('A verification message has been resent to ' .
          'your email address with instructions for activating your account. Once ' .
          'you have activated your account, you will be able to sign in.') . '</p>'))
        ->add($this->component()->html('<h3>' . $this->view->htmlLink(array('route' => 'default'), $this->view->translate('OK, thanks!')) . '</h3>'));
    }
    $this->renderContent();
  }

// } SignupController


// SettingsController {
  public function settingsInit()
  {
    // Can specifiy custom id
    $id = $this->_getParam('id', null);
    $subject = null;
    if (null === $id) {
      $subject = Engine_Api::_()->user()->getViewer();
      Engine_Api::_()->core()->setSubject($subject);
    } else {
      $subject = Engine_Api::_()->getItem('user', $id);
      Engine_Api::_()->core()->setSubject($subject);
    }

    // Set up require's
    $this->_helper->requireUser();
    $this->_helper->requireSubject();
    $this->_helper->requireAuth()->setAuthParams(
      $subject,
      null,
      'edit'
    );

    // Set up navigation
    $navigation = Engine_Api::_()
      ->getApi('menus', 'apptouch')
      ->getNavigation('user_settings', ($id ? array('params' => array('id' => $id)) : array()));

    $this
//      ->add($this->component()->html($this->dom()->new_('h2', array(), $this->view->translate('My Settings'))))
      ->add($this->component()->navigation($navigation));
  }

  public function settingsGeneralAction()
  {
    $this->addPageInfo('contentTheme', 'd');

    // Config vars
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $userSettings = Engine_Api::_()->getDbtable('settings', 'user');
    $user = Engine_Api::_()->core()->getSubject();
    $form = new User_Form_Settings_General(array(
      'item' => $user
    ));

    // Set up profile type options
    /*
    $aliasedFields = $user->fields()->getFieldsObjectsByAlias();
    if( isset($aliasedFields['profile_type']) )
    {
      $options = $aliasedFields['profile_type']->getElementParams($user);
      unset($options['options']['order']);
      $form->accountType->setOptions($options['options']);
    }
    else
    { */
    $form->removeElement('accountType');
    /* } */

    // Removed disabled features
    if ($form->getElement('username') && (!Engine_Api::_()->authorization()->isAllowed('user', $user, 'username') ||
        Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.username', 1) <= 0)
    ) {
      $form->removeElement('username');
    }

    // Facebook
    if ('none' != $settings->getSetting('core.facebook.enable', 'none')) {
      $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
      $facebook = $facebookTable->getApi();
      if ($facebook && $facebook->getUser()) {
        $form->removeElement('facebook');
        $form->getElement('facebook_id')->setAttrib('checked', true);
      } else {
        $form->removeElement('facebook_id');
      }
    } else {
      // these should already be removed inside the form, but lets do it again.
      @$form->removeElement('facebook');
      @$form->removeElement('facebook_id');
    }

    // Twitter
    if ('none' != $settings->getSetting('core.twitter.enable', 'none')) {
      $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
      $twitter = $twitterTable->getApi();
      if ($twitter && $twitterTable->isConnected()) {
        $form->removeElement('twitter');
        $form->getElement('twitter_id')->setAttrib('checked', true);
      } else {
        $form->removeElement('twitter_id');
      }
    } else {
      // these should already be removed inside the form, but lets do it again.
      @$form->removeElement('twitter');
      @$form->removeElement('twitter_id');
    }


    // Check if post and populate
    if (!$this->getRequest()->isPost()) {
      $form->populate($user->toArray());
      $form->populate(array(
        'janrainnoshare' => $userSettings->getSetting($user, 'janrain.no-share', 0),
      ));

      $this->view->status = false;
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Check if valid
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // -- Process --

    $values = $form->getValues();

    // Check email against banned list if necessary
    if (($emailEl = $form->getElement('email')) &&
      isset($values['email']) &&
      $values['email'] != $user->email
    ) {
      $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');
      if ($bannedEmailsTable->isEmailBanned($values['email'])) {
        return $emailEl->addError('This email address is not available, please use another one.');
      }
    }

    // Check username against banned list if necessary
    if (($usernameEl = $form->getElement('username')) &&
      isset($values['username']) &&
      $values['username'] != $user->username
    ) {
      $bannedUsernamesTable = Engine_Api::_()->getDbtable('BannedUsernames', 'core');
      if ($bannedUsernamesTable->isUsernameBanned($values['username'])) {
        return $usernameEl->addError('This profile address is not available, please use another one.');
      }
    }

    // Set values for user object
    $user->setFromArray($values);

    // If username is changed
    $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
    $user->setDisplayName($aliasValues);

    $user->save();


    // Update account type
    /*
    $accountType = $form->getValue('accountType');
    if( isset($aliasedFields['profile_type']) )
    {
      $valueRow = $aliasedFields['profile_type']->getValue($user);
      if( null === $valueRow ) {
        $valueRow = Engine_Api::_()->fields()->getTable('user', 'values')->createRow();
        $valueRow->field_id = $aliasedFields['profile_type']->field_id;
        $valueRow->item_id = $user->getIdentity();
      }
      $valueRow->value = $accountType;
      $valueRow->save();
    }
     *
     */

    // Update facebook settings
    if (isset($facebook) && $form->getElement('facebook_id')) {
      if ($facebook->getUser()) {
        if (empty($values['facebook_id'])) {
          // Remove integration
          $facebookTable->delete(array(
            'user_id = ?' => $user->getIdentity(),
          ));
          $facebook->clearAllPersistentData();
        }
      }
    }

    // Update twitter settings
    if (isset($twitter) && $form->getElement('twitter_id')) {
      if ($twitterTable->isConnected()) {
        if (empty($values['twitter_id'])) {
          // Remove integration
          $twitterTable->delete(array(
            'user_id = ?' => $user->getIdentity(),
          ));
          unset($_SESSION['twitter_token2']);
          unset($_SESSION['twitter_secret2']);
          unset($_SESSION['twitter_token']);
          unset($_SESSION['twitter_secret']);
        }
      }
    }

    // Update janrain settings
    if (!empty($values['janrainnoshare'])) {
      $userSettings->setSetting($user, 'janrain.no-share', true);
    } else {
      $userSettings->setSetting($user, 'janrain.no-share', null);
    }

    // Send success message
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Settings saved.');
    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Settings were successfully saved.'));
    $this
      ->add($this->component()->form($form))
      ->renderContent();
  }

  public function settingsPrivacyAction()
  {
    $user = Engine_Api::_()->core()->getSubject();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $auth = Engine_Api::_()->authorization()->context;

    $form = new User_Form_Settings_Privacy(array(
      'item' => $user,
    ));

    // Init blocked
    $this->view->blockedUsers = array();

    if (Engine_Api::_()->authorization()->isAllowed('user', $user, 'block')) {
      foreach ($user->getBlockedUsers() as $blocked_user_id) {
        $this->view->blockedUsers[] = Engine_Api::_()->user()->getUser($blocked_user_id);
      }
    } else {
      $form->removeElement('blockList');
    }

    if (!Engine_Api::_()->getDbtable('permissions', 'authorization')->isAllowed($user, $user, 'search')) {
      $form->removeElement('search');
    }


    // Hides options from the form if there are less then one option.
    if (count($form->privacy->options) <= 1) {
      $form->removeElement('privacy');
    }
    if (count($form->comment->options) <= 1) {
      $form->removeElement('comment');
    }

    // Populate form
    $form->populate($user->toArray());

    // Set up activity options
    if ($form->getElement('publishTypes')) {
      $actionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getEnabledActionTypesAssoc();
      unset($actionTypes['signup']);
      unset($actionTypes['postself']);
      unset($actionTypes['post']);
      unset($actionTypes['status']);
      $form->publishTypes->setMultiOptions($actionTypes);
      $actionTypesEnabled = Engine_Api::_()->getDbtable('actionSettings', 'activity')->getEnabledActions($user);
      $form->publishTypes->setValue($actionTypesEnabled);
    }

    // Check if post and populate
    if (!$this->getRequest()->isPost()) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $form->save();
    $user->setFromArray($form->getValues())
      ->save();

    // Update notification settings
    if ($form->getElement('publishTypes')) {
      $publishTypes = $form->publishTypes->getValue();
      $publishTypes[] = 'signup';
      $publishTypes[] = 'post';
      $publishTypes[] = 'status';
      Engine_Api::_()->getDbtable('actionSettings', 'activity')->setEnabledActions($user, (array)$publishTypes);
    }

    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    $this
      ->add($this->component()->form($form))
      ->renderContent();
  }

  public function settingsPasswordAction()
  {
    $user = Engine_Api::_()->core()->getSubject();

    $form = new User_Form_Settings_Password();
    $form->populate($user->toArray());

    if (!$this->getRequest()->isPost()) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Check conf
    if ($form->getValue('passwordConfirm') !== $form->getValue('password')) {
      $form->getElement('passwordConfirm')->addError(Zend_Registry::get('Zend_Translate')->_('Passwords did not match'));
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process form
    $userTable = Engine_Api::_()->getItemTable('user');
    $db = $userTable->getAdapter();

    // Check old password
    $salt = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret', 'staticSalt');
    $select = $userTable->select()
      ->from($userTable, new Zend_Db_Expr('TRUE'))
      ->where('user_id = ?', $user->getIdentity())
      ->where('password = ?', new Zend_Db_Expr(sprintf('MD5(CONCAT(%s, %s, salt))', $db->quote($salt), $db->quote($form->getValue('oldPassword')))))
      ->limit(1);
    $valid = $select
      ->query()
      ->fetchColumn();

    if (!$valid) {
      $form->getElement('oldPassword')->addError(Zend_Registry::get('Zend_Translate')->_('Old password did not match'));
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }


    // Save
    $db->beginTransaction();

    try {

      $user->setFromArray($form->getValues());
      $user->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Settings were successfully saved.'));
    $this
      ->add($this->component()->form($form))
      ->renderContent();
  }

  public function settingsNetworkAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($viewer)
      ->order('engine4_network_networks.title ASC');
    //    $this->view->networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);

    // Get networks to suggest
    $network_suggestions = array();
    $table = Engine_Api::_()->getItemTable('network');
    $select = $table->select()
      ->where('assignment = ?', 0)
      ->order('title ASC');

    if (null !== ($text = $this->_getParam('text', $this->_getParam('text')))) {
      $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
    }

    $data = array();
    foreach ($table->fetchAll($select) as $network) {
      if (!$network->membership()->isMember($viewer)) {
        $network_suggestions[] = $network;
      }
    }
    //    $this->view->network_suggestions = $network_suggestions;


    $form = new User_Form_Settings_Network();

    if (!$this->getRequest()->isPost()) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($form->getValue('join_id')) {
      $network = Engine_Api::_()->getItem('network', $form->getValue('join_id'));
      if (null === $network) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else if ($network->assignment != 0) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else {
        $network->membership()->addMember($viewer)
          ->setUserApproved($viewer)
          ->setResourceApproved($viewer);

        if (!$network->hide) {
          // Activity feed item
          Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $network, 'network_join', null, array('is_mobile' => true));
        }
      }
    } else if ($form->getValue('leave_id')) {
      $network = Engine_Api::_()->getItem('network', $form->getValue('leave_id'));
      if (null === $network) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else if ($network->assignment != 0) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else {
        $network->membership()->removeMember($viewer);
      }
    }
    $this
      ->add($this->component()->form($form))
      ->renderContent();
    //
    //    $this->redirect($this->view->url(array()));
  }

  public function settingsNotificationsAction()
  {
    $user = Engine_Api::_()->core()->getSubject();

    // Build the different notification types
    $modules = Engine_Api::_()->getDbtable('modules', 'core')->getModulesAssoc();
    $notificationTypes = Engine_Api::_()->getDbtable('notificationTypes', 'activity')->getNotificationTypes();
    $notificationSettings = Engine_Api::_()->getDbtable('notificationSettings', 'activity')->getEnabledNotifications($user);

    $notificationTypesAssoc = array();
    $notificationSettingsAssoc = array();
    foreach ($notificationTypes as $type) {
      if (in_array($type->module, array('core', 'activity', 'fields', 'authorization', 'messages', 'user'))) {
        $elementName = 'general';
        $category = 'General';
      } else if (isset($modules[$type->module])) {
        $elementName = preg_replace('/[^a-zA-Z0-9]+/', '-', $type->module);
        $category = $modules[$type->module]->title;
      } else {
        $elementName = 'misc';
        $category = 'Misc';
      }

      $notificationTypesAssoc[$elementName]['category'] = $category;
      $notificationTypesAssoc[$elementName]['types'][$type->type] = 'ACTIVITY_TYPE_' . strtoupper($type->type);

      if (in_array($type->type, $notificationSettings)) {
        $notificationSettingsAssoc[$elementName][] = $type->type;
      }
    }

    ksort($notificationTypesAssoc);

    $notificationTypesAssoc = array_filter(array_merge(array(
      'general' => array(),
      'misc' => array(),
    ), $notificationTypesAssoc));

    // Make form
    $form = new Engine_Form(array(
      'title' => 'Notification Settings',
      'description' => 'Which of the these do you want to receive email alerts about?',
    ));

    foreach ($notificationTypesAssoc as $elementName => $info) {
      $form->addElement('MultiCheckbox', $elementName, array(
        'label' => $info['category'],
        'multiOptions' => $info['types'],
        'value' => (array)@$notificationSettingsAssoc[$elementName],
      ));
    }

    $form->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));

    // Check method
    if (!$this->getRequest()->isPost()) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $values = array();
    foreach ($form->getValues() as $key => $value) {
      if (!is_array($value)) continue;

      foreach ($value as $skey => $svalue) {
        if (!isset($notificationTypesAssoc[$key]['types'][$svalue])) {
          continue;
        }
        $values[] = $svalue;
      }
    }

    // Set notification setting
    Engine_Api::_()->getDbtable('notificationSettings', 'activity')
      ->setEnabledNotifications($user, $values);

    $form->addNotice('Your changes have been saved.');
    $this
      ->add($this->component()->form($form))
      ->renderContent();
  }

  public function settingsDeleteAction()
  {
    $user = Engine_Api::_()->core()->getSubject();
    if (!$this->_helper->requireAuth()->setAuthParams($user, null, 'delete')->isValid()) return;

    $this->view->isLastSuperAdmin = false;
    if (1 === count(Engine_Api::_()->user()->getSuperAdmins()) && 1 === $user->level_id) {
      $this->view->isLastSuperAdmin = true;
    }

    // Form
    $form = new User_Form_Settings_Delete();

    if (!$this->getRequest()->isPost()) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('users', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      $user->delete();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    // Unset viewer, remove auth, clear session
    Engine_Api::_()->user()->setViewer(null);
    Zend_Auth::getInstance()->getStorage()->clear();
    Zend_Session::destroy();

    return $this->redirect($this->view->url(array(), 'default', true));
  }

// } SettingsController
  public function userFriendshipBtn($user, $viewer = null)
  {
    if (null === $viewer) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if (!$viewer || !$viewer->getIdentity() || $user->isSelf($viewer)) {
      return '';
    }

    $direction = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

    // Get data
    if (!$direction) {
      $row = $user->membership()->getRow($viewer);
    } else $row = $viewer->membership()->getRow($user);

    // Render

    // Check if friendship is allowed in the network
    $eligible = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    if ($eligible == 0) {
      return '';
    } // check admin level setting if you can befriend people in your network
    else if ($eligible == 1) {

      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $networkMembershipName = $networkMembershipTable->info('name');

      $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
      $select
        ->from($networkMembershipName, 'user_id')
        ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
        ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
        ->where("`{$networkMembershipName}_2`.user_id = ?", $user->getIdentity());

      $data = $select->query()->fetch();

      if (empty($data)) {
        return '';
      }
    }
    $attrs = array(
      'data-role' => 'button',
      'data-mini' => 'true',
    );
    if (!$direction) {
      // one-way mode
      if (null === $row) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'add', 'user_id' => $user->user_id), $this->view->translate('Follow'), $attrs);
      } else if ($row->resource_approved == 0) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'cancel', 'user_id' => $user->user_id), $this->view->translate('Cancel Request'), $attrs);
      } else {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'remove', 'user_id' => $user->user_id), $this->view->translate('Unfollow'), $attrs);
      }

    } else {
      // two-way mode
      if (null === $row) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'add', 'user_id' => $user->user_id), $this->view->translate('Add Friend'), $attrs);
      } else if ($row->user_approved == 0) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'cancel', 'user_id' => $user->user_id), $this->view->translate('Cancel Request'), $attrs);
      } else if ($row->resource_approved == 0) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'confirm', 'user_id' => $user->user_id), $this->view->translate('Accept Request'), $attrs);
      } else if ($row->active) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'remove', 'user_id' => $user->user_id), $this->view->translate('Remove Friend'), $attrs);
      }
    }

    return '';
  }

  public function browseAlbumList(Core_Model_Item_Abstract $item)
  {
    $photo_type = 'thumb.normal';
    if (Engine_Api::_()->apptouch()->isTabletMode()) {
      $photo_type = 'thumb.profile';
    }

    if (isset($item->photo_id)) {
      $photoUrl = $item->getPhotoUrl($photo_type);
    } else {
      $photoUrl = $item->getOwner()->getPhotoUrl($photo_type);
    }

    $customize_fields = array(
      'title' => $item->getTitle(),
      'descriptions' => array(
        $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date)
      ),
      'photo' => $photoUrl,
      'creation_date' => null,
      'counter' => strtoupper($this->view->translate(array('%s photo', '%s photos', $item->count()), $this->view->locale()->toNumber($item->count()))),
    );

    return $customize_fields;
  }

  public function browseVideoList(Core_Model_Item_Abstract $item)
  {
    $photo_type = 'thumb.normal';
    if (Engine_Api::_()->apptouch()->isTabletMode()) {
      $photo_type = 'thumb.profile';
    }

    if (isset($item->photo_id)) {
      $photoUrl = $item->getPhotoUrl($photo_type);
    } else {
      $photoUrl = $item->getOwner()->getPhotoUrl($photo_type);
    }

    $customize_fields = array(
      'title' => $item->getTitle(),
      'descriptions' => array(
        $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date)
      ),
      'photo' => $photoUrl,
      'counter' => strtoupper($this->view->translate(array('%1$s view', '%1$s views', $item->view_count), $this->view->locale()->toNumber($item->view_count))),
    );

    return $customize_fields;
  }

  static protected function _version3PasswordCrypt($method, $salt, $password)
  {
    // For new methods
    if ($method > 0) {
      if (!empty($salt)) {
        list($salt1, $salt2) = str_split($salt, ceil(strlen($salt) / 2));
        $salty_password = $salt1 . $password . $salt2;
      } else {
        $salty_password = $password;
      }
    }

    // Hash it
    switch ($method) {
      // crypt()
      default:
      case 0:
        $user_password_crypt = crypt($password, '$1$' . str_pad(substr($salt, 0, 8), 8, '0', STR_PAD_LEFT) . '$');
        break;

      // md5()
      case 1:
        $user_password_crypt = md5($salty_password);
        break;

      // sha1()
      case 2:
        $user_password_crypt = sha1($salty_password);
        break;

      // crc32()
      case 3:
        $user_password_crypt = sprintf("%u", crc32($salty_password));
        break;
    }

    return $user_password_crypt;
  }

}