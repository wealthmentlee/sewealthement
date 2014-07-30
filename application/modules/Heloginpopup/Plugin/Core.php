<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heloginpopup
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Core.php 24.09.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Heloginpopup
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heloginpopup_Plugin_Core
{
  public function onRenderLayoutDefault($event)
  {
    $view = $event->getPayload();
    $viewer = Engine_Api::_()->user()->getViewer();

    if( $viewer && $viewer->getIdentity() ) {
      return;
    }

    $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
    $controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
    $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

    if( $module == 'user' && $controller == 'auth' && $action == 'login' ) {
      return;
    }

    if( $module == 'user' && $controller == 'signup') {
      return;
    }

    if( $module == 'user' && $controller == 'auth' && $action == 'forgot' ) {
      return;
    }

    if( $module == 'heloginpopup' && $controller == 'index' && $action == 'index') {
      return;
    }


    $maxday = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('heloginpopup.max.day', 30);
    $url = $view->url(array('module' => 'heloginpopup', 'controller' => 'index'), 'default');

    $forgotUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'auth', 'action' => 'forgot'), 'default', true);
    $signupUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_signup', true);
    $facebookUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'heloginpopup', 'controller' => 'index', 'action' => 'facebook'), 'default', true);
    $twitterUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'auth', 'action' => 'twitter'), 'default', true);

    $return_url  = $_SERVER['REQUEST_URI'];

    $content = $view->action('index', 'index', 'heloginpopup', array('return_url' => $return_url));
    $content = preg_replace(array('/\r/', '/\n/'), '', $content);

    $content = json_encode($content, JSON_HEX_QUOT);

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $janrainCode = '';
    if( 'none' != $settings->getSetting('core_janrain_enable', 'none') && $settings->core_janrain_key ) {
      $janrainUsername = $settings->core_janrain_username;
      $formMode = 'page';
      $janrainAccountType = $settings->core_janrain_type;
      $accountType = Zend_Json::encode($janrainAccountType);
      $translate = Zend_Registry::get('Zend_Translate');
      $jainrainActionText = Zend_Json::encode(true ? '' : $translate->translate('Sign in using your account with'));

      $janrainCode = <<<EOF
(function() {
    var formMode = '$formMode';
    var accountType = $accountType;

    // Custom
    if( accountType == 'pro' ) {
      janrain.settings.type = 'embed';
      janrain.settings.providersPerPage = '6';
      if( $jainrainActionText ) {
        janrain.settings.actionText = $jainrainActionText;
      }
      if( formMode == "page" ) {
        janrain.settings.format = 'one row';
        janrain.settings.width = '400';
      } else {
        janrain.settings.format = 'one column';
        janrain.settings.width = '168';
      }
    } else {
      janrain.settings.type = 'modal';
    }

    function isReady() { janrain.ready = true; };
    if (document.addEventListener) {
      document.addEventListener("DOMContentLoaded", isReady, false);
    } else {
      window.attachEvent('onload', isReady);
    }

    var e = document.createElement('script');
    e.type = 'text/javascript';
    e.id = 'janrainAuthWidget';

    if (document.location.protocol === 'https:') {
      e.src = 'https://rpxnow.com/js/lib/$janrainUsername/engage.js';
    } else {
      e.src = 'http://widget-cdn.rpxnow.com/js/lib/$janrainUsername/engage.js';
    }

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(e, s);
})();
EOF;
    }

    $script = <<<EOF
  loginPopup.return_url = '$return_url';
  loginPopup.formUrl = '$url';
  loginPopup.forgotUrl = '$forgotUrl';
  loginPopup.signupUrl = '$signupUrl';
  loginPopup.facebookUrl = '$facebookUrl';
  loginPopup.twitterUrl = '$twitterUrl';
  loginPopup.dateLimit = $maxday;
  loginPopup.content = Elements.from($content, false);
  loginPopup.content.addClass('heloginpopup_hide heloginpopup_modal heloginpopup_fade');
  loginPopup.modalView = new Element('div', {
    'class': 'heloginpopup_modal_backdrop heloginpopup_fade',
    events: {
        click: function(){
            loginPopup.hidePopup();
        }
    }
});
  window.addEvent('domready', function(){
    loginPopup.init();
  });
EOF;

    if($janrainCode) {
      $view->headScript()
        ->appendScript($janrainCode);
    }

    $view->headScript()
      ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Heloginpopup/externals/scripts/core.js')
      ->appendScript($script);

  }
}