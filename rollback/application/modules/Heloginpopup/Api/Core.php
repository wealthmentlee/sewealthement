<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 24.09.13
 * Time: 15:43
 */
class Heloginpopup_Api_Core extends Core_Api_Abstract
{
  public function facebookLoginButton($connect_text = 'Connect with Facebook')
  {
    $settings  = Engine_Api::_()->getApi('settings', 'core');
    $facebook  = Engine_Api::_()->getDbtable('facebook', 'user')->getApi();

    if( !$facebook ) {
      return;
    }

    $href = Zend_Controller_Front::getInstance()->getRouter()
      ->assemble(array('module' => 'heloginpopup', 'controller' => 'index',
        'action' => 'facebook'), 'default', true);

    $return_url  = $_SERVER['REQUEST_URI'];
    $href = $href . '?return_url=' . $return_url;

    $view = Zend_Registry::get('Zend_View');
    $text = $view->translate('Connect Facebook');
    return '
      <a target="_top" href="'.$href.'" class="heloginpopup_socialbutton facebook-connect">
      '. $text .'
      </a>
    ';
  }

  public function twitterLoginButton($connect_text = 'Sign-in with Twitter')
  {
    $href = Zend_Controller_Front::getInstance()->getRouter()
      ->assemble(array('module' => 'heloginpopup', 'controller' => 'index',
        'action' => 'twitter'), 'default', true);

    $return_url  = $_SERVER['REQUEST_URI'];
    $href = $href . '?return_url=' . $return_url;

    $view = Zend_Registry::get('Zend_View');
    $text = $view->translate('Connect Twitter');

    return '
      <a target="_top" href="'.$href.'" class="heloginpopup_socialbutton twitter-connect">
      '. $text . '
      </a>
    ';
  }

   public function janrainLoginButton($mode = null)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $translate = Zend_Registry::get('Zend_Translate');
    $view = Zend_Registry::get('Zend_View');
    $locale = Zend_Registry::get('Locale');

    $janrainAccountType = $settings->core_janrain_type;
    $janrainUsername = $settings->core_janrain_username;
    $jainrainActionText = Zend_Json::encode(true ? '' : $translate->translate('Sign in using your account with'));
    $formMode = Zend_Json::encode($mode);
    $accountType = Zend_Json::encode($janrainAccountType);
    if( $mode == 'page' ) {
      $extraClass = 'janrainPageMode';
    } else {
      $extraClass = 'janrainColumnMode';
    }
    $janrainCode ='';

    // Add link/widget
    if( $janrainAccountType == 'pro' ) {
      $janrainCode .= '<div id="janrainEngageEmbed" class="$extraClass"></div>';
    } else {
      $janrainProviders = explode(',', $settings->core_janrain_providers);
      $imgStr = '';
      $baseUrl = Zend_Registry::get('StaticBaseUrl');
      foreach( $janrainProviders as $janrainProvider ) {
        $imgStr .= '<img onclick="loginPopup.hidePopup();" src="'
          . $baseUrl . 'application/modules/User/externals/images/janrain/' . $janrainProvider . '.png'
          . '" alt="'
          . $janrainProvider
          . '" title="'
          . $janrainProvider
          . '"/>';
      }
      $janrainCode .= '<span class="janrainEngageLabel">'
        . $translate->translate('Or sign in using:')
        . '<br />'
        . '<a target="_top" class="janrainEngage" href="#">'
        . $imgStr
        . '<wbr />'
        . '</a>'
        . '</span>'
      ;
    }

    return $janrainCode;
  }

  public function hasSocialIntegration()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if( 'none' != $settings->getSetting('core_facebook_enable', 'none') && $settings->core_facebook_secret )
      return true;

    if( 'none' != $settings->getSetting('core_twitter_enable', 'none') && $settings->core_twitter_secret )
      return true;

    if( 'none' != $settings->getSetting('core_janrain_enable', 'none') && $settings->core_janrain_key )
      return true;

    return false;
  }
}