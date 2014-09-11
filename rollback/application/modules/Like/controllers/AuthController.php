<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AuthController.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_AuthController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->_helper->layout->setLayout('default-simple');
    $return_url = $this->_getParam('return_url');
    $_SESSION['fb_tw_url'] = $return_url;
    
    // Already logged in
    if( Engine_Api::_()->user()->getViewer()->getIdentity() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('You are already signed in.');

      if( null === $this->_helper->contextSwitch->getCurrentContext())
      {
        $this->_redirectCustom($return_url);
      }
      return;
    }

    $this->view->form = $form = new User_Form_Login();
    $this->view->form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'like_login'));
    $form->return_url->setValue($return_url);
    if( !$this->getRequest()->isPost() )
    {     
      return;
    }
    // Facebook login
    $fbApi = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook.key');
    if ($fbApi && User_Model_DbTable_Facebook::authenticate($form))
    {
      // Facebook login succeeded, redirect to home
      $this->_helper->redirector->gotoRoute(array(), 'home');
    }
    // Form not valid
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
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

    // Check if user exists
    if( empty($user) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.');
      $form->addError(Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.'));
      return;
    }

    if( !$user->verified || !$user->enabled ) {
      $this->view->status = false;

      $error = Zend_Registry::get('Zend_Translate')->_('This account still requires either email verification or admin approval.');

      if( !empty($user) && !$user->verified ) {
        $error .= ' Click <a href="%s">here</a> to resend the email.';
      }
      $error = Zend_Registry::get('Zend_Translate')->_($error);

      if( !empty($user) && !$user->verified ) {
        $resend_url = $this->_helper->url->url(array('action' => 'resend', 'email' => $email), 'user_signup', true);
        $error = sprintf($error, $resend_url);
      }

      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
      return;
    }

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
          Engine_Api::_()->user()->getAuth()->getStorage()->write($user->email);
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
        return;
      }
    }

    // Remember   
    if( $remember )
    {
      $lifetime = 1209600; // Two weeks
      Zend_Session::getSaveHandler()->setLifetime($lifetime, true);
      Zend_Session::rememberMe($lifetime);
    }

    // Increment sign-in count
    Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.logins');

    // Test activity @todo remove
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() )
    {
      $viewer->lastlogin_date = date("Y-m-d H:i:s");
      $viewer->lastlogin_ip   = $_SERVER['REMOTE_ADDR'];
      $viewer->save();
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $viewer, 'login');
    }

    // Assign sid to view for json context
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Login successful');
    $this->view->sid = Zend_Session::getId();
    $this->view->sname = Zend_Session::getOptions('name');
    // Do redirection only if normal context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      // Redirect by form
      $uri = $form->getValue('return_url');
      if( $uri )
      {
         return $this->_redirect($uri, array('prependBase' => false));
      }
    }
  }

  public function likeAction()
  {
    $layoutHelper = $this->_helper->layout;
    $layoutHelper->disableLayout();
    unset($_SESSION['fb_tw_url']);

    $this->view->error = 0;
    $this->view->html = '';

    $this->view->subject = $subject = Engine_Api::_()->getItem((string)$this->_getParam('object'), (int)$this->_getParam('object_id'));
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('like_You are not logged in.');
      if( null === $this->_helper->contextSwitch->getCurrentContext() ){
        $this->_helper->redirector->gotoRoute(array(), 'like_login');
      }
      return;
    }

    if (!$viewer->getIdentity()) {
      $this->view->error =  3;
      $this->view->html =  'You should be logged in.';
      return ;
    }
		if (!Engine_Api::_()->like()->isAllowed($subject)) {
      $this->view->error = 2;
      $this->view->html = Zend_Registry::get('Zend_Translate')->_('LIKE_AUTH_ERROR');
			return ;
		}
		if (Engine_Api::_()->like()->isLike($subject)) {
      $this->view->error = 4;
      $this->view->html = Zend_Registry::get('Zend_Translate')->_('like_Already liked.');
			return ;
		}
    if ($this->view->error) {
      return ;
    }
		if (!Engine_Api::_()->like()->like($subject)) {
      $this->view->error = 5;
			$this->view->html = 'like_You did not like it. Try later.';
		}


  }
}