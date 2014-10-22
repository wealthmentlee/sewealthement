<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_Api_Core extends Core_Api_Abstract
{
  const HESEA = "<HESEA>";
  private $appInfo = null;
  public $supportedModules = array(
    'activity' => 'activity',
    'advalbum' => 'advalbum',
    'advgroup' => 'advgroup',
    'album' => 'album',
    'article' => 'article',
    'blog' => 'blog',
    'chat' => 'chat',
    'checkin' => 'checkin',
    'classified' => 'classified',
    'core' => 'core',
    'credit' => 'credit',
    'event' => 'event',
    'forum' => 'forum',
    'group' => 'group',
    'hebadge' => 'hebadge',
    'hecore' => 'hecore',
    'hegift' => 'hegift',
    'hequestion' => 'hequestion',
    'invite' => 'invite',
    'inviter' => 'inviter',
    'like' => 'like',
    'messages' => 'messages',
    'music' => 'music',
    'network' => 'network',
    'offers' => 'offers',
    'pagealbum' => 'pagealbum',
    'pageblog' => 'pageblog',
    'pagecontact' => 'pagecontact',
    'page' => 'page',
    'pagediscussion' => 'pagediscussion',
    'pageevent' => 'pageevent',
    'pagemusic' => 'pagemusic',
    'pagevideo' => 'pagevideo',
    'payment' => 'payment',
    'photoviewer' => 'photoviewer',
    'poll' => 'poll',
    'rate' => 'rate',
    'store' => 'store',
    'suggest' => 'suggest',
    'timeline' => 'timeline',
    'user' => 'user',
    'video' => 'video',
    'wall' => 'wall',
    'ultimatenews' => 'ultimatenews',
    'hecontest' => 'hecontest',
    'heevent' => 'heevent',
    'donation' => 'donation',
  );

  protected $availableModules = array();
  public function __construct(){
    if( $this->isApp() ) {
      unset($this->supportedModules['store']);
      unset($this->supportedModules['offers']);
      unset($this->supportedModules['heevent']);
      unset($this->supportedModules['donation']);
    }
  }
  public function getSupportedModules(){
    return $this->supportedModules;
  }

  public function getAvailableModules()
  {
    if(empty($this->availableModules)){
      $enabledModules = $this->getEnabledModuleNames();
      foreach($enabledModules as $module) {
        if(in_array($module, $this->supportedModules)) {
          $this->availableModules[] = $module;
        };
      }
    }
    return $this->availableModules;
  }

  public function getVideoThumbs(Core_Model_Item_Abstract $video, $thumbType)
  {
    $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');

    if(strpos($video->getType(), 'video') !== false && isset($video->type) && isset($video->code)){
      if ($video->type == 1) {
        $thumbTypes = array(
          'icon' => 'default.jpg',
          'normal' => 'mqdefault.jpg',
          'profile' => 'hqdefault.jpg',
          'full' => 'maxresdefault.jpg',
        );
        return $prefix."img.youtube.com/vi/" . $video->code . "/" . $thumbTypes[$thumbType];
      } else if ($video->type == 2) {
        $thumbTypes = array(
          'icon' => 'thumbnail_small',
          'normal' => 'thumbnail_medium',
          'profile' => 'thumbnail_large',
          'full' => 'thumbnail_large',
        );
        $hash = unserialize(file_get_contents($prefix."vimeo.com/api/v2/video/" . $video->code . ".php"));
        return $hash[0][$thumbTypes[$thumbType]];
      } else if ($video->type == 3) {
        return $video->getPhotoUrl($thumbType);
      }
    }
  }

  public function isApptouchMode(Zend_Controller_Request_Abstract $request = null)
  {
    if (isset($_GET['apptouch-site-mode']) && ($_GET['apptouch-site-mode'] == 'apptouch' || $_GET['apptouch-site-mode'] == 'apptablet'))
      return true;
    $session = new Zend_Session_Namespace('apptouch-site-mode');
    $mode = '';
    if ($session->__isset('mode'))
    {
			$mode	= $session->__get('mode');
			if ($mode === 'apptouch' || $mode === 'apptablet')
				return $mode;
			elseif($mode === 'standard')
				return false;
    }
    if (count(explode('smoothbox', $_SERVER['REQUEST_URI'])) == 1 &&
      isset($_COOKIE['windowwidth']) && $_COOKIE['windowwidth'] === "337" ||/*for simulator*/
      Engine_Api::_()->getDbTable('settings', 'core')->getSetting('apptouch.default', false)
    )
      return true;


    if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {

      $userAgent = $this->getUserAgent();
      $includeTablets = false;
      if (
        !Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('apptablet') &&
        Engine_Api::_()->getDbTable('settings', 'core')->getSetting('apptouch.include.tablets', false) &&
        $this->isTablet()
      ) {
        $includeTablets = true;
      }

      if ($mode != 'standard'){

        if ($this->isTabletMode()){
          return true;
        }
        if ($includeTablets || preg_match('/ip(hone|od)|bb10|android|iemobile\/9.0|iemobile\/10|nokia808pureview|imageuploader/i', $userAgent)){
          return true;
        } else {
          return false;
        }

      } else {
        return false;
      }
    }


  }

  public function isApp($getInfo = false){
    if($this->appInfo === null){
      $appInfo = explode(self::HESEA, $_SERVER['HTTP_USER_AGENT']);
      if(count($appInfo) == 2){
        $this->appInfo = Zend_Json::decode($appInfo[1]);
      } else
        $this->appInfo = false;
    }

    $returnValue = $getInfo ? $this->appInfo : (!(!$this->appInfo));
    return $returnValue;

  }

  public function getAppInfo(){
    return $this->isApp(true);
  }

  public function isTabletMode()
  {
    $isTabletMode = false;
    if (Engine_Api::_()->hasModuleBootstrap('apptablet')){
      if (Engine_Api::_()->apptablet()->isActive())
        $isTabletMode = true;
    } elseif(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('apptablet')){
      if ($this->isTablet())
        $isTabletMode = true;
    }
    return $isTabletMode;
  }

  public function isTablet(){
    $userAgent = Engine_Api::_()->apptouch()->getUserAgent();
    $session = new Zend_Session_Namespace('apptouch-site-mode');
    if ($session->__isset('mode'))
    {
			$mode	= $session->__get('mode');
			if ($mode === 'apptablet')
				return true;
      else
        return false;
    }
    if (strstr($userAgent, 'android')) {
            if(!strstr($userAgent, 'mobile'))
                return true;
            else
                return false;
        } else {
            if (preg_match('/tablet|ipad|xoom|viewpad|playbook|kindle|bolt|touchpad|gt-p|sgh-t|sch-i|shw-m180s|AT100|sch-t|mz609|mz617|mid7015|tf101|g-v|ct1002|transformer| tab/i', $userAgent)) {
                return true;
            } else {
                return false;
            }
        }
  }

  public function resetMobi(Zend_Controller_Request_Abstract $request)
   {
 		$module = $request->getModuleName();
 		$controller = $request->getControllerName();
 		$action = $request->getActionName();

 		if($module == "mobi")

 			if ($controller == 'index' && $action == 'index') {
 				$request->setModuleName('core');
 				$request->setControllerName('index');
 				$request->setActionName('index');
       }

 			if($controller == "index" && $action == "userhome") {
 				$request->setModuleName('user');
 				$request->setControllerName('index');
 				$request->setActionName('home');
 			}

 			if($controller == "index" && $action == "profile") {
 				$request->setModuleName('user');
 				$request->setControllerName('profile');
 				$request->setActionName('index');
     }

 			if($controller == "group" && $action == "profile") {
 				$request->setModuleName('group');
 				$request->setControllerName('profile');
 				$request->setActionName('index');
     }

 			if($controller == "event" && $action == "profile") {
 				$request->setModuleName('event');
 				$request->setControllerName('profile');
 				$request->setActionName('index');
     }

 		return $request;
     }

  public function getSupportModules()
  {
    $support_modules = array(
      array(
        'album',
        'article',
        'blog',
        'chat',
        'checkin',
        'classified',
        'core',
        'credit',
        'event',
        'forum',
        'group',
        'hebadge',
        'hecore',
        'hegift',
        'hequestion',
        'invite',
        'inviter',
        'like',
        'messages',
        'music',
        'network',
        'offers',
        'pagealbum',
        'pageblog',
        'pagecontact',
        'page',
        'pagediscussion',
        'pageevent',
        'pagemusic',
        'pagevideo',
        'payment',
        'poll',
        'rate',
        'store',
        'suggest',
        'timeline',
        'user',
        'video',
        'wall'
      )
    );

    return $support_modules;

  }


  public function setLayout()
  {

    // Create layout
    $layout = Zend_Layout::startMvc();

    // Set options
    $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Apptouch/layouts", 'Core_Layout_View')
      ->setViewSuffix('tpl')
      ->setLayout(null);

    $view = $layout->getView();
    $view->dashboard = Engine_Api::_()->getApi('menus', 'apptouch')->getNavigation('core_dashboard');

    $view->footer_navigation = Engine_Api::_()->getApi('menus', 'apptouch')
      ->getNavigation('core_footer');

    // Add global site title etc
    $siteinfo = Engine_Api::_()->getApi('settings', 'core')->__get('core.general.site', array());
    $siteinfo = array_filter($siteinfo);
    $siteinfo = array_merge(array(
      'title' => 'Social Network',
      'description' => '',
      'keywords' => '',
    ), $siteinfo);

    $layout->siteinfo = $siteinfo;

    return $layout;
  }

  public function isMaintenanceMode()
  {
    $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
    if (file_exists($global_settings_file)) {
      $generalConfig = include $global_settings_file;
    } else {
      $generalConfig = array();
    }
    return (!empty($generalConfig['maintenance']['enabled'])) ? true : false;
  }

  public function   getUserAgent()
  {
    if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
      return strtolower($_SERVER['HTTP_USER_AGENT']);
    }

    else '';
  }

  public function getEnabledModuleNames()
  {
    $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');

    return $modulesTable->select()
      ->from($modulesTable, 'name')
      ->where('enabled = ?', true)
      ->where('version Like ?', '4.%')
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);
  }

  public function getFacebookLoginButton($connect_text = 'Connect with Facebook')
  {
    $imgHref = Zend_Registry::get('StaticBaseUrl')
      . 'application/modules/User/externals/images/facebook-sign-in.gif';
    $view = new Zend_View();
    $fbConnectText = $view->translate('APPTOUCH_FB_CONNECT');
    $href = Zend_Controller_Front::getInstance()->getRouter()
      ->assemble(array('module' => 'user', 'controller' => 'auth',
        'action' => 'facebook'), 'default', true);
    $onclick = '';
    if( $this->isApp() ) {
      $onclick = "window.plugins.facebookIntegration.showLoginPage();";
      $href = '';
    }

    $fbBtn = <<<HTML
    			<a id="fb-connect" onclick="{$onclick}" href="{$href}" class="ui-icon ui-icon-facebook"  data-ajax="false"></a>
HTML;

    return $fbBtn;
  }
  public function getFBApiInfo(){
    return Zend_Json::decode($_SESSION['facebook_api_info']);
  }

  // Twitter Login button
  public function getTwitterLoginButton($connect_text = 'Sign in with Twitter')
  {
    $imgHref = Zend_Registry::get('StaticBaseUrl')
      . 'application/modules/User/externals/images/twitter-sign-in.png';
    $view = new Zend_View();
    $fbConnectText = $view->translate('APPTOUCH_Twitter_CONNECT');
    $href = Zend_Controller_Front::getInstance()->getRouter()
      ->assemble(array('module' => 'user', 'controller' => 'auth',
        'action' => 'twitter'), 'default', true);
    $onclick = '';
    if( $this->isApp() ) {
      $onclick = "window.plugins.twitterIntegration.twitterLogin();";
      $href = '';
    }

    $twBtn = <<<HTML
    			<a id="tw-connect" onclick="{$onclick}" href="{$href}" class="ui-icon ui-icon-twitter" data-ajax="false"></a>
HTML;

    return $twBtn;
  }
  public function getTWApiInfo(){
    return Zend_Json::decode($_SESSION['twitter_api_info']);
  }
}