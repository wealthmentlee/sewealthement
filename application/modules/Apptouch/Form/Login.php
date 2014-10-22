<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Login.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_Form_Login extends Apptouch_Form_Standard
{
  public function init()
  {
    $isApp = Engine_Api::_()->apptouch()->isApp();
    $translate = Zend_Registry::get('Zend_Translate');
    $signupUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_signup', true);
//    $description = Zend_Registry::get('Zend_Translate')->_("If you already have an account, please enter your details below. If you don't have one yet, please <a href='%s'>sign up</a> first.");
//    $description= sprintf($description, Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_signup', true));
//
//    // Init form
//    if(!$isApp){
//      $this->setTitle('Member Sign In');
//      $this->setDescription($description);
//    }
    $this->setAttrib('id', 'user_form_login');
    $this->setAttrib('class', 'global_form');
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $content = '';
    if ('none' != $settings->__get('core_facebook_enable', 'none') && $settings->core_facebook_secret) {
      $content .= Engine_Api::_()->apptouch()->getFacebookLoginButton();
    }

    // Init twitter login link todo
    if ('none' != $settings->getSetting('core_twitter_enable', 'none')
      && $settings->core_twitter_secret
    ) {
      $content .= Engine_Api::_()->apptouch()->getTwitterLoginButton();
    }

    if($content){

      $this->addElement('Dummy', 'social_login', array(
        'content' => '<p>' . $translate->_('APPTOUCH_Login With:') . '</p>' . $content . '<p>' . $translate->_('APPTOUCH_or, log in using email address:') . '</p>',
      ));
    }

    $email = $translate->_('Email Address');

    // Init email
    $this->addElement('Text', 'email', array(
      'label' => $email,
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        'StringTrim',
      ),
      'validators' => array(
        'EmailAddress'
      ),
      'tabindex' => 1,
    ));

    $password = Zend_Registry::get('Zend_Translate')->_('Password');
    // Init password
    $this->addElement('Password', 'password', array(
      'label' => $password,
      'required' => true,
      'allowEmpty' => false,
      'tabindex' => 2,
      'filters' => array(
        'StringTrim',
      ),
    ));

    $this->addElement('Hidden', 'return_url', array(
    ));

    if ($settings->core_spam_login) {
      $this->addElement('captcha', 'captcha', array(
        'label' => 'Human Verification',
        'description' => 'Please validate that you are not a robot by typing in the letters and numbers in this image:',
        'captcha' => 'image',
        'required' => true,
        'tabindex' => 3,
        'captchaOptions' => array(
          'wordLen' => 6,
          'fontSize' => '30',
          'timeout' => 300,
          'imgDir' => APPLICATION_PATH . '/public/temporary/',
          'imgUrl' => $this->getView()->baseUrl() . '/public/temporary',
          'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
        )));
    }

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Sign In',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => 5,
    ));

    // Init remember me
      $this->addElement('Hidden', 'remember', array(
        'tabindex' => 6,
        'value' => 1
      ));
    // Init forgot password link
    $this->addElement('Dummy', 'forgot', array(
      'content' => '<a data-role="button" href="'.Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'auth', 'action' => 'forgot'), 'default', true).'">' . Zend_Registry::get('Zend_Translate')->_("APPTOUCH_FORGOT_PASSWORD") . '</a>'
    ));

    // Init facebook login link todo
    $this->addElement('Dummy', 'signup', array(
      'content' => '<a data-role="button" href="'.$signupUrl.'">' . Zend_Registry::get('Zend_Translate')->_("Sign Up") . '</a>'
    ));

    $this->addDisplayGroup(array(
      'forgot',
      'signup'
    ), 'buttons');
    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login'));
  }
}