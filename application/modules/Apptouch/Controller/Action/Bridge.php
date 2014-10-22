<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bridge.php 2012-08-16 11:18:13 ulan $
 * @author     Ulan
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
abstract class Apptouch_Controller_Action_Bridge extends Core_Controller_Action_Standard
{
  private $module_name = null;
  protected $browse_navigation;

  private $page_info = null;
  private $page_title = null;
  private $layout = array();
  private $layout_indexes = array(
    'header' => 0,
    'content' => 0,
    'footer' => 0,
  );
  private $page_types = null;
  private $components = null;
  private $page_format = null;
  private $prepareFormat_pref = 'prepareFormat';
  private $componentHelper = null;
  protected $page_structures = array();

  private $module_settings = null;
  private $content_settings = null;
  private $page_uri = '';
  protected $page = array();
  protected $searchForm = null;
  protected $_noPhotos;
  protected static $_itemTypes;

  public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
  {
    // Parent
    parent::__construct($request, $response, $invokeArgs);
    self::$_itemTypes = array_flip(Engine_Api::_()->getApi('search', 'core')->getAvailableTypes());
    // init Module name
//    todo blitz
    $m = $request->getParam('module');
    if ($request->getParam('module') == 'headvancedalbum' || $request->getParam('module') == 'sitealbum')
      $m = 'album';
    $this->module_name = $m;
//    todo blitz

    // load module settings
    $this->module_settings = Apptouch_Content::getModuleSettings($this->module_name);

    // load content settings
    $this->content_settings = Apptouch_Content::getContentSettings();

    // load page types
    $this->page_types = Apptouch_Content::getManifestData('page_model_types');

    // JSON Only
    $this->_helper->viewRenderer->setNoRender(true);
    $this->handleIosQuirk('photos');
  }

  private function handleIosQuirk($name)
  {
    if ($_FILES)
      foreach ($_FILES[$name]['name'] as $key => $filename) {
        if ($_FILES[$name]['name'][$key])
          $_FILES[$name]['name'][$key] = $key . $filename;
      }
  }

  public final function init()
  {
    //    todo shit code
    //        if(!$this->isAjaxRequest() && strpos($_SERVER['REQUEST_URI'], 'format=json')){
    //          $this->_helper->contextSwitch->setAutoSwitchLayout(false);
    //          unset($this->autoContext);
    //          Engine_Api::_()->apptouch()->setLayout();
    //        }
    //    todo shit code
    // ignore Zend_Validate_File_Upload::INI_SIZE
    $files = $this->getPicupFiles();
    if ($files && is_array($files) && empty($_FILES)) {
      foreach ($files as $key => $val)
        $_FILES[$key] = array(
          'name' => '',
          'type' => '',
          'tmp_name' => '',
          'error' => 4,
          'size' => 0
        );
    }
    // Default langs {
    $this->lang(array('Cancel', 'Loading...'));
    // } Default langs

    // load available component list
    $this->components = Apptouch_Content::getManifestData('ui_components');


    $method = $this->getInitFunctionName();
    if (method_exists($this, $method)) {

      $this->$method();
    }
    // todo Temp {
//        if (!$this->isAjaxRequest()) {
    $this->checkIMChat();
//        }
    // todo } Temp
  }

  protected function isAjaxRequest()
  {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || $this->_getParam('REQUEST_TYPE') == 'xmlhttprequest';
  }

  public function getInitFunctionName()
  {
    $controller = $this->getRequest()->getParam('controller', '');
    $fnName = '';
    foreach (explode('-', $controller) as $part) {
      $fnName .= ucfirst($part);
    }
    $fnName .= 'Init';
    $fnName = strtolower(substr($fnName, 0, 1)) . substr($fnName, 1);
    return $fnName;
  }

  /**
   * @return Apptouch_Controller_Action_Helper_Components
   */

  public function component()
  {
    if ($this->componentHelper)
      return $this->componentHelper;
    return $this->componentHelper = $this->_helper->components();
  }

  public function subject(Core_Model_Item_Abstract $subject = null, $params = array())
  {
    if (!$subject) {
      if (Engine_Api::_()->core()->hasSubject()) {
        $subject = Engine_Api::_()->core()->getSubject();
      }
    }

    if (!$subject) {
      return null;
    }

    $format = array(
      'id' => $subject->getIdentity(),
      'type' => $subject->getType(),
      'title' => $subject->getTitle(),
      'href' => $subject->getHref(),
      'photo' => array(
        'icon' => $this->itemPhoto($subject, 'thumb.icon'),
        'mini' => $this->itemPhoto($subject, 'thumb.mini'),
        'normal' => $this->itemPhoto($subject, 'thumb.normal'),
        'profile' => $this->itemPhoto($subject, 'thumb.profile'),
        'full' => $this->itemPhoto($subject),
//        'icon_nophoto' => $this->getNoPhoto($subject, 'thumb.icon'),
//        'normal_nophoto' => $this->getNoPhoto($subject, 'thumb.normal'),
//        'profile_nophoto' => $this->getNoPhoto($subject, 'thumb.profile')
      )
    );
//        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
//
//        $icon = false;
//        $empty = array();
//        foreach( $format['photo'] as $key => $value ){
//
//            $Headers = @get_headers($host_url . $value);
//            if(!strpos($Headers[0], '200')) {
////                $format['photo'][$key] = 'OOPS';
//                $empty[] = $key;
//            } else {
//                $icon = $value;
//            }
//        }
//        foreach($empty as $key) {
//            if($icon) {
//                $format['photo'][$key] = $icon;
//            }
//        }

    if (!empty($params)) {
      if (!empty($params['show_desc'])) {
        $format['description'] = $subject->getDescription();
        unset($params['show_desc']);
      }
      if (!empty($params['short_desc'])) {
        $format['short_desc'] = Engine_String::substr($subject->getDescription(), 0, 255) . '...';
        unset($params['short_desc']);
      }
      $format = array_merge($params, $format);
    }

    return $format;
  }


  public function itemPhoto($item, $type = 'thumb.profile')
  {
    // Whoops
    if (!($item instanceof Core_Model_Item_Abstract)) {
      throw new Zend_View_Exception("Item must be a valid item");
    }

    // Get url
    $src = $item->getPhotoUrl($type);
    $safeName = ($type ? str_replace('.', '_', $type) : 'main ');

    return $src;
  }

  public function getNoPhoto($item, $type)
  {
    $type = ($type ? str_replace('.', '_', $type) : 'main');

    if (($item instanceof Core_Model_Item_Abstract)) {
      $item = $item->getType();
    } else if (!is_string($item)) {
      return '';
    }

    if (!Engine_Api::_()->hasItemType($item)) {
      return '';
    }


    // Use default
    if (!isset($this->_noPhotos[$item][$type])) {
      $shortType = $item;
      if (strpos($shortType, '_') !== false) {
        list($null, $shortType) = explode('_', $shortType, 2);
      }
      $module = Engine_Api::_()->inflect(Engine_Api::_()->getItemModule($item));
      $this->_noPhotos[$item][$type] = //$this->view->baseUrl() . '/' .
        $this->view->layout()->staticBaseUrl . 'application/modules/' .
        $module .
        '/externals/images/nophoto_' .
        $shortType . '_'
        . $type . '.png';
    }

    return $this->_noPhotos[$item][$type];
  }

  public function viewer()
  {
    if ($viewer = Engine_Api::_()->user()->getViewer()) {
      return $this->subject($viewer);
    } else {
      return null;
    }
  }

  public function redirect($redirectTo, $messages = false, $status = null)
  {
    if (!is_null($status))
      $this->view->status = $status;

    if ($messages)
      $this->view->message = $messages;

    if ($redirectTo instanceof Core_Model_Item_Abstract) {
      $this->view->redirect_url = $redirectTo->getHref();

    } elseif (is_string($redirectTo)) {
      if ($redirectTo == $this->view->baseUrl() . '/')
        $this->view->redirect_url = $this->view->url(array('nocache' => rand(0, 1000)), 'default', true);
      else
        $this->view->redirect_url = $redirectTo;
    }
  }

  public function refresh()
  {
    return $this->redirect('refresh');
  }

  public function clearClientCache()
  {
    $this->view->domCache = 'clearAll';
    return $this;
  }

  /**
   * @return Apptouch_Api_Navigator
   */
  public function navigator()
  {
    return Engine_Api::_()->getApi('navigator', 'apptouch');
  }

  /**
   * @return Apptouch_Controller_Action_Helper_JsonML
   */
  public function dom()
  {
    return $this->_helper->jsonML();
  }

  /**
   * @param null $data
   * @return Apptouch_View_Helper_Lang
   */

  public function lang($data = null)
  {
    if (is_array($data) || is_string($data)) {
      $this->view->lang()->add($data);
      return;
    } elseif (!$data) {
      return $this->view->lang();
    }
  }

  public function postDispatch()
  {
    parent::postDispatch();
    $layoutHelper = $this->_helper->layout;
    if ('default' == $layoutHelper->getLayout() && $this->_getParam('module', false)) {
      // Increment page views and referrer
      if (Engine_Api::_()->apptouch()->isApp()) {
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('ios.core.views');
      } else {
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('apptouch.core.views');
      }

      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.views');
    }
  }

  protected function getPageInfo()
  {
    if ($this->getRequest()->isPost()) {
      $this->attrPage('data-dom-cache', false);
    }
    $requestParams = $this->getRequest()->getParams();
    unset($requestParams['module']);
    $requestParams['module'] = $this->getRequest()->getControllerName();
    $page_info = array(
      'title' => $this->getPageTitle(),
      'key' => $this->getPageKey(),
      'url' => $this->getPageUrl(),
      'viewer' => $this->viewer(),
      'subject' => $this->subject(),
      'params' => $requestParams,
      'lang' => $this->lang()->toArray()
    );

    if (!$this->page_info) {
      $this->page_info = $page_info;
    } else {
      $this->page_info = array_merge($page_info,
        $this->page_info
      );
    }
    return $this->page_info;
  }

  /**
   * @return Apptouch_Form_Search
   * */
  public function getSearchForm()
  {
    if (!$this->searchForm
    )
      // load General Search Form
      $this->searchForm = new Apptouch_Form_Search();
    //    $this->searchForm->getElement('search')->setValue($this->_getParam('search'));

    return $this->searchForm;
  }

  protected function addPageInfo($name, $value)
  {
    $this->page_info[$name] = $value;
    return $this;
  }

  public function attrPage($arg1 = false, $arg2 = null)
  {
    if (!isset($this->page_info['attrs']))
      $this->page_info['attrs'] = array();

    if (is_array($arg1))
      $this->page_info['attrs'] = array_merge($this->page_info['attrs'], $arg1);
    elseif (is_string($arg1) && (is_string($arg2) || is_bool($arg2) || is_numeric($arg2))) {
      $this->page_info['attrs'] = array_merge($this->page_info['attrs'], array($arg1 => $arg2));
      return $this;
    } elseif (is_string($arg1) && $arg2 === null) {
      return isset($this->page_info['attrs'][$arg1]) ? $this->page_info['attrs'][$arg1] : null;
    } else
      return $this->page_info['attrs'];
  }

  public function getPageTitle()
  {

    if (is_null($this->page_title)) {

      $pageKey = $this->_getParam('module') . '_' . $this->_getParam('controller') . '_' . $this->_getParam('action');

      $pagesTbl = Engine_Api::_()->getDbTable('pages', 'core');
      $pagesSel = $pagesTbl->select()
        ->from($pagesTbl->info('name'), array('title'))
        ->where('name = ?', $pageKey);

      $page = $pagesTbl->getAdapter()->fetchOne($pagesSel);

      if ($page)
        $page = $this->view->translate($page);

      if (!$page) {
        $pageTitleKey = 'pagetitle-' . str_replace('_', '-', $pageKey);
        $page = $this->view->translate($pageTitleKey);

        if ($page && $page == $pageTitleKey) {
          $page = '';
        }
      }
      if ($this->view->subject() && $this->view->subject()->getIdentity()) {
        if (trim($page) != '' && !trim($page) != '-')
          $page .= ' - ' . $this->view->subject()->getTitle();
        else
          $page = $this->view->subject()->getTitle();
      }
      $this->page_title = $page;
    }

    return $this->page_title;

  }

  public function setPageTitle($title)
  {
    $this->page_title = $title;

    return $this;

  }

  protected function getPageKey()
  {
    $params = $this->getRequest()->getParams();
    //    $pageKey = $this->_getParam('module') . '_' . $this->_getParam('controller') . '_' . $this->_getParam('action');

    if ($this->getRequest()->getParam('format', false))
      unset($params['format']);

    if ($this->getRequest()->getParam('REQUEST_TYPE', false))
      unset($params['REQUEST_TYPE']);

    $pageKey = $params['module'] . '_' . $params['controller'] . '_' . @$params['action'] . '_';

    unset($params['module']);
    unset($params['controller']);
    unset($params['action']);

    $pageKey .= implode('_', array_keys($params));

    if ($sbj = $this->subject()) {
      $pageKey .= '_' . $sbj['type'] . '_' . $sbj['id'];
    }
    return $pageKey;
  }

  protected function getPageUrl()
  {
    if (empty($this->page_uri)) {
      $page_uri = str_replace('/format/json', '', str_replace('?format=json&', '?', str_replace('?format=json', '', str_replace('&format=json', '', str_replace('?format=json&', '?', $_SERVER['REQUEST_URI'])))));
      $page_uri = str_replace('?REQUEST_TYPE=xmlhttprequest&', '?', str_replace('?REQUEST_TYPE=xmlhttprequest', '', str_replace('&REQUEST_TYPE=xmlhttprequest', '', str_replace('?REQUEST_TYPE=xmlhttprequest&', '?', $page_uri))));
      $this->page_uri = $page_uri;
    }
    return $this->page_uri;
  }


  private function getLayout()
  {
    $this->addAdCampaign();
    foreach ($this->layout as $parentName => $parentContent) {
      $this->layout[$parentName] = array_values($parentContent);
    }
    return $this->layout;
  }

  public function addAdCampaign()
  {
    $pagekey = $this->getRequest()->getControllerName() . "_" . implode('_', explode('-', $this->getRequest()->getActionName()));
    $pageTable = Engine_Api::_()->getDbTable('pages', 'apptouch');
    $page = $pageTable->fetchRow($pageTable->select()->where('name=?', $pagekey));
    if ($page->enable_ad && $page->adcampaign_id && ($campaign = Engine_Api::_()->getItem('core_adcampaign', $page->adcampaign_id))) {
      $ad = $this->component()->adCampaign($campaign);
      if ($ad) {
        $order = $ad['params']['ad']['position'] ? 100 : -100;
        $this->add($ad, $order);
      }
    }
  }

  /****************************************************
   *  DEBUG FUNCTIONS
   ********************************************************/
  public function printArr($var)
  {
    $this->add($this->component()->html(print_arr($var, true)));
    return $this;
  }

  /****************************************************
   *  DEBUG FUNCTIONS
   ********************************************************/

  protected function setFormat($page_type)
  {
    try {
      if (!in_array($page_type, $this->page_types)) {
        throw new Apptouch_Controller_Action_BridgeException('Unknown Page Format: "' . $page_type . '"');
      }

      $method = $this->prepareFormat_pref . ucfirst($page_type);
      if ($this->page_format) {
        throw new Apptouch_Controller_Action_BridgeException('Page Format "' . $this->page_format . '" already has been set');
      }

      $this->page_format = $page_type;
      $this->addPageInfo('type', $page_type);
      $this->attrPage('type', $page_type);
      $this->$method();

      return $this;

    } catch (Exception $e) {
      throw $e; // todo setup exception
    }
  }

  public function getFormat()
  {
    return @$this->page_format . '';
  }

//  protected function addFormat($name) todo temporary disabled
//  {
//
//    if (in_array($name, $this->page_types))
//      throw new Apptouch_Controller_Action_BridgeException('Page Format "' . $name . '" already exists');
//
//    $method = $this->prepareFormat_pref . ucfirst($name);
//    if (method_exists($this, $method)) {
//      array_push($this->page_types, $name);
//      return $this;
//    } else {
//      throw new Apptouch_Controller_Action_BridgeException('Before adding new "' . $name . '" Page format You must implement public "' . $method . '()" method');
//    }
//  }

// todo  prepareFormat Functions {

  private function prepareFormatBrowse()
  {
    $this
      ->add($this->component()->navigation('main'), -1);
    if (Engine_Api::_()->user()->getViewer()->getIdentity())
      $this->add($this->component()->quickLinks('quick'));
  }

  private function prepareFormatManage()
  {
    return $this->prepareFormatBrowse();
  }

  private function prepareFormatCreate()
  {
    return $this->prepareFormatBrowse();
  }

  private function prepareFormatEdit()
  {
    $this
      ->add($this->component()->quickLinks('quick'));
  }

  private function prepareFormatHtml()
  {
    // todo prepareFormatHtml()
  }

  private function prepareFormatProfile()
  {
    $this
      ->addPageInfo('contentTheme', 'd')
      ->add($this->component()->quickLinks('profile'))
      ->add($this->component()->subjectPhoto(), 0)
      ->add($this->component()->rate(array('subject' => $this->view->subject())), 1)
      ->add($this->component()->like(array('subject' => $this->view->subject())), 2)
      ->add($this->component()->tabs(), 5);
  }

  private function prepareFormatView()
  {
    $this->add($this->component()->comments(), 1000);
  }

//  } prepareFormat Functions

  protected function getOptions(Core_Model_Item_Abstract $item, $name = false, $module = null)
  {
    if ($name === false && !$this->page_info['type'])
      return array(); // todo
    if (!is_string($name))
      $name = $this->page_info['type'];
    if (is_string($module)) {
      $moduleSettings = Apptouch_Content::getModuleSettings($module);
      if (isset($moduleSettings [$name])) {
        $setting = $moduleSettings [$name];
      } else {
        $setting = false;
      }
    } else {
      $module = $this->module_name;
      $setting = $this->getModuleSetting($name);
    }
    $param = isset($setting['identity_param']) ? $setting['identity_param'] : $module . '_id';
    $options = array();
    if (is_array($setting['options']))
      foreach ($setting['options'] as $option) {

        $option['href']['url_options'][$param] = $item->getIdentity();
        $url_options = $option['href']['url_options'];
        $options[] = array(
          'label' => $this->view->translate($option['label']),
          'attrs' => array(
            'href' => $this->view->url($url_options, $option['href']['route'], true),
            'class' => $option['class']
          ),
        );
      }
    return array_filter($options);
  }

  protected function getOption(Core_Model_Item_Abstract $item, $key, $name = false, $module = null)
  {
    if ($name === false && !$this->page_info['type'])
      return array(); // todo
    if (!is_string($name))
      $name = $this->page_info['type'];
    if (is_string($module)) {
      $moduleSettings = Apptouch_Content::getModuleSettings($module);
      if (isset($moduleSettings [$name])) {
        $setting = $moduleSettings [$name];
      } else {
        $setting = false;
      }
    } else {
      $module = $this->module_name;
      $setting = $this->getModuleSetting($name);
    }

    //    $setting = $this->getModuleSetting($name);
    @$option = $setting['options'][$key];

    if (!$option)
      return array();

    $param = isset($setting['identity_param']) ? $setting['identity_param'] : $module . '_id';
    $option['href']['url_options'][$param] = $item->getIdentity();
    $url_options = $option['href']['url_options'];
    return array_filter(array(
      'label' => $this->view->translate($option['label']),
      'attrs' => array(
        'href' => $this->view->url($url_options, $option['href']['route'], true),
        'class' => $option['class']
      ),
    ));
  }

  public function add($component, $order = null, $parentContentName = null, $replace = false)
  {
    if (!$component) {
      return $this;
    }

    $name = $component['name'];

    if (!is_string($parentContentName)) {
      if (!($parentContentName = @$this->components[$name]['parent']))
        return $this;
    }

    if ($order === null) {
      if (isset($this->layout_indexes[$parentContentName])) {
        $order = $this->layout_indexes[$parentContentName];
      } else {
        $order = 0;
      }

      @$this->layout_indexes[$parentContentName]++;
    }

    if (is_integer($order)) {
      if ($replace) {
        $replace = 1;
      } else {
        $replace = 0;
      }

      if (@is_array($this->layout[$parentContentName][$order])) {
        @array_splice($this->layout[$parentContentName], $order, $replace, array($component));
      } else {
        $this->layout[$parentContentName][$order] = $component;
      }

      ksort($this->layout[$parentContentName]);
    } else {
      throw new Apptouch_Controller_Action_BridgeException('Bad index: ' . $order);
    }

    return $this;
  }

  protected function renderContent()
  {
    if ($this->page !== $this->view->page) {
      $this->page['info'] = $this->getPageInfo();

      $this->page['layout'] = $this->getLayout();

      $this->view->page = $this->page;
    } else {
      throw new Apptouch_Controller_Action_BridgeException('Content already has been rendered.');
    }
  }

  public function getModuleName()
  {
    return $this->module_name;
  }

  public function getModuleSetting($name, $default_value = null)
  {
    if (isset($this->module_settings[$name]) /* && (is_numeric($this->module_settings[$name]) || is_string($this->module_settings[$name]))*/) {
      return $this->module_settings[$name];
    } else {
      return $default_value;
    }
  }

  public function is_iPhoneUploading()
  {
    $this->view->chash = $this->_getParam('chash');
    return (preg_match('/imageuploader/', Engine_Api::_()->apptouch()->getUserAgent()) &&
      $this->_getParam('owner_id', false)
    );
  }

//  Common Tabs
  public function tabUpdates($active = false)
  {
    if ($active)
      $this->add($this->component()->feed(), 6);

    return true;
  }

  public function tabLinks()
  {
    $subject = Engine_Api::_()->core()->getSubject();

    // Get paginator
    $table = Engine_Api::_()->getDbtable('links', 'core');
    $select = $table->select()
      ->where('parent_type = ?', $subject->getType())
      ->where('parent_id = ?', $subject->getIdentity())
      ->where('search = ?', 1)
      ->order('creation_date DESC');

    $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {

      return;
    }

    return $paginator;
  }

  public function tabFields($active = FALSE)
  {
//        if (Engine_Api::_()->getApi('core', 'apptouch')->isTabletMode()) {
//            $this->addPageInfo()$this->_bridge->getHelper('fields')->toArray($subject, $structure)
//            return true;
//        }

    if ($active)
      $this->add($this->component()->fieldsValues(), 10);
    return true;
  }

  public function tabLikebox($active = false)
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')) {
      return false;
    }

    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject();

    if (!$subject->getIdentity()) {
      return false;
    }

    if (!Engine_Api::_()->like()->isAllowed($subject)) {
      return false;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('like.likes_count', 9);
    $period = $settings->getSetting('like.likes_period', 1);
    $page = $this->_getParam('page', 1);

    $paginator = Engine_Api::_()->like()->getLikes($subject);
    if ($active) {
      $type = $this->_getParam('type', 'all');
      if ($type != 'all' && $type != 'month' && $type != 'week' || !$period)
        $type = 'all';

      $paginator = Engine_Api::_()->like()->getLikes($subject, $type);

      if (!$paginator) {
        return false;
      }

      $paginator->setCurrentPageNumber($page);
      $paginator->setItemCountPerPage($ipp);

      $like_count = $this->view->translate(array("like_%s like", "like_%s likes", $paginator->getTotalItemCount()), ($paginator->getTotalItemCount()));

      if ($period) {
        $all_btn = $this->dom()->new_('a', array('data-role' => 'button', 'href' => $subject->getHref() . '/tab/likebox/type/all'), $this->view->translate('LIKE_Overall'));
        $month_btn = $this->dom()->new_('a', array('data-role' => 'button', 'href' => $subject->getHref() . '/tab/likebox/type/month'), $this->view->translate('LIKE_This Month'));
        $week_btn = $this->dom()->new_('a', array('data-role' => 'button', 'href' => $subject->getHref() . '/tab/likebox/type/week'), $this->view->translate('LIKE_This Week'));
        $group = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-type' => 'horizontal'));

        $group->append($all_btn)->append($month_btn)->append($week_btn);

        $this->add($this->component()->html($group), 10);
      }

      $this
        ->add($this->component()->html($like_count), 11)
        ->add($this->component()->itemList($paginator), 12)
        ->add($this->component()->paginator($paginator), 13);;

    }

    return array(
      'showContent' => false,
      'response' => $paginator
    );
  }

// todo   Has Clone on Apptouch_Plugin_Signup_Photo
  public function getPicupFiles($name = null)
  {
    $files = Zend_Json::decode($this->_getParam('picup_files'), '{}');
    if ($name)
      return @$files[$name];

    return $files;
  }

  public function getUploadedFiles($name = null)
  {

    $files = Zend_Json::decode($this->_getParam('picup_files'), '{}');

    if ($name)
      return @$files[$name];

    return $files;
  }

  public function userSessionStart()
  {
    $this->view->domCache = 'clearAll';
    $this->view->userSession = 'start';
    $this->view->templates = $this->view->templates()->render();
  }

  public function userSessionClose()
  {
    $this->view->domCache = 'clearAll';
    $this->view->userSession = 'close';
    $this->view->templates = $this->view->templates()->render();
  }

  public static function hasItemType($type)
  {
    return isset(self::$_itemTypes[$type]);
  }

  public function isModuleEnabled($name)
  {
    return Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled($name);
  }

//  ============================== todo Temporary ============================
  public function checkIMChat()
  {
    $isEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('chat');

    if ($isEnabled) {
      $view = $this->view;
      $viewer = Engine_Api::_()->user()->getViewer();

      if ($view instanceof Zend_View) {

        // Check if enabled
        $canChat = Engine_Api::_()->authorization()->isAllowed('chat', $viewer, 'chat');
        $canIM = Engine_Api::_()->authorization()->isAllowed('chat', $viewer, 'im');

        // Check if friends-only or all members
        $memberIm = Engine_Api::_()->getApi('settings', 'core')->getSetting('chat.im.privacy', 'friends');
        $memberIm = 'everyone' === $memberIm
          ? 'true'
          : 'false';

        $identity = sprintf('%d', $viewer->getIdentity());
        $delay = Engine_Api::_()->getApi('settings', 'core')->getSetting('chat.general.delay', '5000');

        $canIM = ($canIM ? 'true' : 'false');
        $canChat = ($canChat ? 'true' : 'false');

        $this->view->chatSettings = array(
          'imOptions' => array('memberIm' => $memberIm),
          'identity' => $identity,
          'delay' => $delay,
          'enableIM' => $canIM,
          'canChat' => $canChat,
          'join_url' => $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'join'), 'default'),
          'ping_url' => $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'ping'), 'default'),
          'leave_url' => $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'leave'), 'default'),
          'list_url' => $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'list'), 'default'),
          'status_url' => $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'status'), 'default'),
          'send_url' => $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'send'), 'default'),
          'whisper_url' => $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'whisper'), 'default'),
          'whisper_close_url' => $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'whisper-close'), 'default'),
          'settings_url' => $this->view->url(array('module' => 'chat', 'controller' => 'ajax', 'action' => 'settings'), 'default')
        );

        $this->lang(array(
          'The chat room has been disabled by the site admin.', 'Browse Chatrooms',
          'You are sending messages too quickly - please wait a few seconds and try again.',
          '%1$s has joined the room.', '%1$s has left the room.', 'Settings',
          'Online ', 'None of your friends are online.',
          'Members Online', 'No members are online.', 'Go Offline',
          'Open Chat', 'General Chat', 'Introduce Yourself', '%1$s person',
          'You',
        ));
      }

    }
  }
//  ============================== todo Temporary ============================
}