<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2011-12-14 14:02:00 ulan $
 * @author     Ulan
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_AdminSettingsController extends Core_Controller_Action_Admin
{

  protected $_basePath;

  public function indexAction()
  {
    $core_setting = Engine_Api::_()->getDbTable('settings', 'core');
    $this->view->form = $form = new Apptouch_Form_Admin_Settings_General();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $core_setting->setSetting('apptouch.default', $this->_getParam('set_default', false));
      $core_setting->setSetting('apptouch.integrations.only', $this->_getParam('integrations_only', false));
      $core_setting->setSetting('apptouch.include.tablets', $this->_getParam('include_tablets', false));
      $core_setting->setSetting('apptouch.activity.scrollajax', $this->_getParam('scrollajax', true));
      $core_setting->setSetting('apptouch.content.autoscroll', $this->_getParam('autoscroll', false));
      $core_setting->setSetting('apptouch.cometchat.uri', $this->_getParam('cometchat_uri', 'cometchat'));
      $form->addNotice("APPTOUCH_Changes have been saved.");
    } else {
      $settings = array();
      $settings['set_default'] = $core_setting->getSetting('apptouch.default', false);
      $settings['integrations_only'] = $core_setting->getSetting('apptouch.integrations.only', false);
      $settings['include_tablets'] = $core_setting->getSetting('apptouch.include.tablets', false);
      $settings['scrollajax'] = $core_setting->getSetting('apptouch.activity.scrollajax', true);
      $settings['autoscroll'] = $core_setting->getSetting('apptouch.content.autoscroll', false);
      $settings['cometchat_uri'] = $core_setting->getSetting('apptouch.cometchat.uri', 'cometchat');
      $form->populate($settings);
    }


  }

  public function performanceAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('apptouch_admin_main', array(), 'apptouch_admin_main_performance_settings');

    $cacheSettings = array();
    $pref = 'apptouch.admin.cache.';
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->form = $form = new Apptouch_Form_Admin_Settings_Performance();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $enable = $this->_getParam('enable');
      $min_lifetime = $this->_getParam('min_lifetime');
      $max_lifetime = $this->_getParam('max_lifetime');
      $cache_feature = $this->getRequest()->getPost('type');
      $err = false;
      try {
        if ($min_lifetime <= 0 || $max_lifetime <= 0) {
          $form->addError('APPTOUCH_Time must be positive');
          $err = true;
        }
        if ($min_lifetime > 6000) {
          $form->addError('APPTOUCH_Minimum lifetime is exceeded');
          $err = true;
        }
        if ($max_lifetime > 6000) {
          $form->addError('APPTOUCH_Maximum lifetime is exceeded');
          $err = true;
        }
        if ($max_lifetime <= $min_lifetime) {
          $form->addError('APPTOUCH_Maximum lifetime must be greater than minimum lifetime');
          $err = true;
        }
        if (isset($enable)) {
          $settings->setSetting($pref . 'enable', $enable);
        }
        if (isset($min_lifetime)) {
          $settings->setSetting($pref . 'min_lifetime', $min_lifetime);
        }
        if (isset($max_lifetime)) {
          $settings->setSetting($pref . 'max_lifetime', $max_lifetime);
        }
        if (isset($cache_feature)) {
          $settings->setSetting($pref . 'type', $cache_feature);
        }
        $form->addNotice("APPTOUCH_Changes have been saved.");
      } catch (Exception $e) {
        if (!$err)
          $form->addError($e->getMessage());
        return;
      }
    }
    $cacheSettings['enable'] = $settings->getSetting($pref . 'enable');
    if ($settings->getSetting($pref . 'min_lifetime')) {
      $cacheSettings['min_lifetime'] = $settings->getSetting($pref . 'min_lifetime');
    }
    if ($settings->getSetting($pref . 'max_lifetime')) {
      $cacheSettings['max_lifetime'] = $settings->getSetting($pref . 'max_lifetime');
    }

    if ($settings->getSetting($pref . 'type')) {
      $cacheSettings['type'] = $settings->getSetting($pref . 'type');
    }
    $form->populate($cacheSettings);
  }

  public function UITipsAction()
  {

  }

  public function appIconSetAction()
  {
    $path = DIRECTORY_SEPARATOR .
      'public' .
      DIRECTORY_SEPARATOR .
      'apptouch' .
      DIRECTORY_SEPARATOR .
      'homescreen';
    $this->view->form = $form = new Apptouch_Form_Admin_Settings_AppIconSet();
    // Check if folder exists and is writable
    $has_dir = true;

    // Creating touch folder if not exists
    if (!is_dir(APPLICATION_PATH . '/public/apptouch/')) {
      $has_dir = mkdir(APPLICATION_PATH . '/public/apptouch/');
    }
    // Creating homescreen folder if not exists
    if ($has_dir && !is_dir(APPLICATION_PATH . '/public/apptouch/homescreen/')) {
      $has_dir = mkdir(APPLICATION_PATH . '/public/apptouch/homescreen/');
    }

    if (!$has_dir || !file_exists(APPLICATION_PATH . '/public/admin') ||
      !is_writable(APPLICATION_PATH . '/public/admin')
    ) {
      $form->addError('The public/admin folder does not exist or is not ' .
        'writable. Please create this folder and set full permissions ' .
        'on it (chmod 0777).');
      return;
    }
    // Set base path
    $this->_basePath = realpath(APPLICATION_PATH . $path);

    if (!$this->getRequest()->isPost()) {
      if (!$this->view->homeScreen()) {
        $form->removeElement('original');
        $form->removeElement('preview');
        $form->removeElement('enable');
      }
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('appmanager')){
      $form->addNotice('APPTOUCH_This feature is available only for Tablet & Mobile sites (NOT FOR APPLICATIONS)');
    }

    if ($form->Filedata->getValue() !== null) {
      $fileElement = $form->Filedata;
      $fileName = $fileElement->getFileName();
      $extension = ltrim(strrchr(basename($fileName), '.'), '.');
      $original = $this->_getPath() . DIRECTORY_SEPARATOR . 'original.' . $extension;
      $icon_57x57 = $this->_getPath() . DIRECTORY_SEPARATOR . '57x57.' . $extension;
      $icon_114x114 = $this->_getPath() . DIRECTORY_SEPARATOR . '114x114.' . $extension;
      $icon_144x144 = $this->_getPath() . DIRECTORY_SEPARATOR . '144x144.' . $extension;
      if (file_exists($original))
        unlink($original);

      if (file_exists($icon_57x57))
        unlink($icon_57x57);

      if (file_exists($icon_114x114))
        unlink($icon_114x114);

      if (file_exists($icon_144x144))
        unlink($icon_144x144);

      rename($fileName, $original);

      // Resize 57 x 57
      $image = Engine_Image::factory();
      $image->open($original);

      $size = min($image->height, $image->width);
      $x = ($image->width - $size) / 2;
      $y = ($image->height - $size) / 2;
      $w = $h = $size;

      $image->open($original)
        ->resample($x, $y, $w, $h, 57, 57)
        ->write($icon_57x57)
        ->destroy();

      // Resize 114 x 114
      $image = Engine_Image::factory();
      $image->open($original)
        ->resample($x, $y, $w, $h, 114, 114)
        ->write($icon_114x114)
        ->destroy();

      // Resize 144 x 144
      $image = Engine_Image::factory();
      $image->open($original)
        ->resample($x, $y, $w, $h, 144, 144)
        ->write($icon_144x144)
        ->destroy();

      Engine_Api::_()->getDbTable('settings', 'core')->setSetting('apptouch.homescreen.extension', $extension);
      if (file_exists($original))
        $form->getElement('submit');

    }
    elseif ($form->getValue('coordinates') && $extension = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('apptouch.homescreen.extension', false)) {
      $original = $this->_getPath() . DIRECTORY_SEPARATOR . 'original.' . $extension;
      $icon_57x57 = $this->_getPath() . DIRECTORY_SEPARATOR . '57x57.' . $extension;
      $icon_114x114 = $this->_getPath() . DIRECTORY_SEPARATOR . '114x114.' . $extension;
      $icon_144x144 = $this->_getPath() . DIRECTORY_SEPARATOR . '144x144.' . $extension;

      if (file_exists($icon_57x57))
        unlink($icon_57x57);

      if (file_exists($icon_114x114))
        unlink($icon_114x114);

      if (file_exists($icon_144x144))
        unlink($icon_144x144);

      list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
      $x += .1;
      $y += .1;
      $w -= .1;
      $h -= .1;
      // Resize 57 x 57
      $image = Engine_Image::factory();
      $image->open($original)
        ->resample($x, $y, $w, $h, 57, 57)
        ->write($icon_57x57)
        ->destroy();

      // Resize 114 x 114
      $image = Engine_Image::factory();
      $image->open($original)
        ->resample($x, $y, $w, $h, 114, 114)
        ->write($icon_114x114)
        ->destroy();

      // Resize 144 x 144
      $image = Engine_Image::factory();
      $image->open($original)
        ->resample($x, $y, $w, $h, 144, 144)
        ->write($icon_144x144)
        ->destroy();
    }

    if ($form->getValue('enable') !== null)
      Engine_Api::_()->getDbTable('settings', 'core')->setSetting('apptouch.homescreen.enabled', $form->getValue('enable'));
  }

  public function siteLogoAction()
  {
    $path = DIRECTORY_SEPARATOR .
      'public' .
      DIRECTORY_SEPARATOR .
      'apptouch' .
      DIRECTORY_SEPARATOR .
      'sitelogo';
    $this->view->form = $form = new Apptouch_Form_Admin_Settings_SiteLogo();
    // Check if folder exists and is writable
    $has_dir = true;

    // Creating touch folder if not exists
    if (!is_dir(APPLICATION_PATH . '/public/apptouch/')) {
      $has_dir = mkdir(APPLICATION_PATH . '/public/apptouch/');
    }
    // Creating sitelogo folder if not exists
    if ($has_dir && !is_dir(APPLICATION_PATH . '/public/apptouch/sitelogo/')) {
      $has_dir = mkdir(APPLICATION_PATH . '/public/apptouch/sitelogo/');
    }

    if (!$has_dir || !file_exists(APPLICATION_PATH . '/public/admin') ||
      !is_writable(APPLICATION_PATH . '/public/admin')
    ) {
      $form->addError('The public/admin folder does not exist or is not ' .
        'writable. Please create this folder and set full permissions ' .
        'on it (chmod 0777).');
      return;
    }
    // Set base path
    $this->_basePath = realpath(APPLICATION_PATH . $path);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    if ($form->Filedata->getValue() !== null) {
      $fileElement = $form->Filedata;
      $fileName = $fileElement->getFileName();
      $extension = ltrim(strrchr(basename($fileName), '.'), '.');
      $original = $this->_getPath() . DIRECTORY_SEPARATOR . 'sitelogo.' . $extension;
      if (file_exists($original))
        unlink($original);
      rename($fileName, $original);
      Engine_Api::_()->getDbTable('settings', 'core')->setSetting('apptouch.sitelogo', 'sitelogo.' . $extension);
    }
  }

  public function faceApiAction()
  {
    $this->view->form = $form = new Apptouch_Form_Admin_Settings_FaceDetectionApi();

//    $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');
//    $checksSel = $checksTbl->select()
//      ->from($checksTbl->info('name'), array(new Zend_Db_Expr('COUNT(*)')))
//      ->where('place_id = ?', 0);
//    $rowCount = $checksTbl->getAdapter()->fetchOne($checksSel);
//
//    if ($rowCount) {
//      $linkHTML = $this->view->htmlLink($this->view->url(array('module' => 'checkin', 'controller' => 'index', 'action' => 'convert'), 'admin_default', true), $this->view->translate('Upgrade'), array('class' => 'smoothbox'));
//      $description = sprintf($this->view->translate('CHECKIN_Your database has old formatted %s records. You need to upgrade them. Please click %s'), $rowCount, $linkHTML);
//      $form->addNotice($description);
//    }

    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
      foreach ($values as $key => $value) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('apptouch.sky_biometry_' . $key, $value);
      }

      $form->addNotice('Your changes have been saved.');
    }
  }

  protected function _getPath($key = 'path')
  {
    return $this->_checkPath(urldecode($this->_getParam($key, '')), $this->_basePath);
  }

  protected function _getRelPath($path, $basePath = null)
  {
    if (null === $basePath) $basePath = $this->_basePath;
    $path = realpath($path);
    $basePath = realpath($basePath);
    $relPath = trim(str_replace($basePath, '', $path), '/\\');
    return $relPath;
  }

  protected function _checkPath($path, $basePath)
  {
    // Sanitize
    //$path = preg_replace('/^[a-z0-9_.-]/', '', $path);
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');
    $path = $basePath . '/' . $path;

    // Resolve
    $basePath = realpath($basePath);
    $path = realpath($path);
    // Check if this is a parent of the base path
    if ($basePath != $path && strpos($basePath, $path) !== false) {
      return $this->_helper->redirector->gotoRoute(array());
    }

    return $path;
  }

}