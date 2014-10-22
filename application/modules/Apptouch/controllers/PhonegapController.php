<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 05.11.12
 * Time: 9:56
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_PhonegapController extends Apptouch_Controller_Action_Bridge
{

  public function indexAction()
  {
    $this->registerUserDevice();
    $this->settingsAction();
    $this->templatesAction();
    $this->renderContent();
    $viewer = Engine_Api::_()->user()->getViewer();
    $return_url = $this->_getParam('return_url', false);
    if(!$return_url){
      if($viewer->getIdentity())
        $return_url = $this->view->url(array('action' => 'home'), 'user_general', true);
      else
        $return_url = $this->view->url(array(), 'user_login', true);
    }
    $this->redirect($return_url);
  }

  public function templatesAction()
  {
    $this->view->templates = $this->view->templates()->render();
  }

  public function settingsAction()
  {
    $settings = array();
    $settingsFormat = array();

    $settings['locale'] = $this->getLocale();
    $settings['siteinfo'] = $this->getSiteInfo();
    $settings['location'] = $this->getLocation();
    $settings['templates'] = $this->view->templates()->render();

    $settingsFormat['states'] = array();
    $settingsFormat['langs'] = $this->getLangs();
    foreach($settings as $settingKey => $setting){
      $settingJson = Zend_Json::encode($setting);
//      $settings[$settingKey] = $settingJson;
      if(($settingLen = strlen($settingJson))!= (int)$this->_getParam($settingKey)){
        $settingsFormat[$settingKey] = $settings[$settingKey];
      };
      $settingsFormat['states'][$settingKey] = $settingLen;
    }
    $this->view->settings = $settingsFormat;
  }

  protected function getLocale()
  {
    $locale = array(
      'name' => $this->view->locale()->getLocale()->__toString(),
      'formats' => array(
        'date' => $this->view->localeFormats()->date(),
      ),
    );
    return $locale;
  }

  protected function getLocation()
  {
    $location = array(
      'host' => $_SERVER['HTTP_HOST'],
      'hostname' => $_SERVER['SERVER_NAME'],
      'port' => $_SERVER['SERVER_PORT'],
      'pathname' => $_SERVER['REQUEST_URI'],
      'protocol' => constant('_ENGINE_SSL') ? 'https:' : 'http:',

    );
    $location['href'] = $location['protocol'] . '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $location;
  }

  protected function getSiteInfo()
  {
    $siteinfo = $this->view->layout()->siteinfo;
    $siteinfo['APPLICATION_ENV'] = APPLICATION_ENV;
    $siteinfo['theme'] = Engine_Api::_()->getDbTable('themes', 'apptouch')->getActiveThemeName();
    $siteinfo['host'] = $_SERVER['HTTP_HOST'];
    $siteinfo['baseUrl'] = rtrim($this->view->baseUrl(), '/') . '/';
    $siteinfo['staticBaseUrl'] = $this->view->layout()->staticBaseUrl;
    $siteinfo['basehref'] = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->baseUrl(), '/') . '/';

    $siteinfo['title'] = $this->view->layout()->siteinfo['title'];
    $siteinfo['logo'] = $this->view->siteLogo()->url();
    $siteinfo['description'] = $this->view->layout()->siteinfo['description'];
    $siteinfo['copyrightString'] = $this->view->translate('Copyright &copy;%s', date('Y'));

    return $siteinfo;
  }

  protected function getLangs()
  {
    $langs = array(
      'Loading...',
      'APPTOUCH_There are some troubles with Internet connection',
      'APPTOUCH_Your post will be posted as soon as the connection resumes',
      'APPTOUCH_Lazy Commit Starts!',
      'APPTOUCH_Lazy Commit Complete!'

    );
    $translated = array();
    foreach ($langs as $lang) {
      $translated[$lang] = $this->view->translate($lang);
    }
    return $translated;
  }

  private function registerUserDevice()
  {
    $session = new Zend_Session_Namespace('tokenStorage');
    $apnsToken = $this->getRequest()->getParam('apnsToken');
    $gcmRegId = $this->getRequest()->getParam('gcmRegId');
    if($apnsToken){
      $session->__set('apnsToken', $apnsToken);
    } elseif($gcmRegId){
      $session->__set('gcmRegId', $gcmRegId);
    }

  }

}