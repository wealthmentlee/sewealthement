<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 31.05.12
 * Time: 18:11
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Content
{
  protected static $map = null;

//  Returns type of the page by request
  public static function getPageType(Zend_Controller_Request_Abstract $request)
  {
    $type = null;

    $requestKey = Apptouch_Content::getRequestKey($request);

    $request_list = Apptouch_Content::getManifestData('request_list');
    $page_types = Apptouch_Content::getManifestData('page_model_types');

    if (isset($request_list[$requestKey]) && in_array($type = $request_list[$requestKey], $page_types)) {
      return $type;
    }
  }

  protected static function getRequestKey(Zend_Controller_Request_Abstract $request)
  {
    return $request->getParam('module') . '_' . $request->getParam('controller') . '_' . $request->getParam('action');
  }

  public static function getManifestData($key)
  {
    if (!Apptouch_Content::$map) {
      Apptouch_Content::$map = Zend_Registry::get("Apptouch_Content");
    }

    if (!$key) {
      return null;
    }

    return @Apptouch_Content::$map[$key];
  }

  public static function getModuleSettings($module_name)
  {
    if ($module_name) {
      $modules = self::getManifestData('modules_settings');
      $settings = @$modules[$module_name];
      if (!$settings) {
        $settings = array();
      }
      $settings = array_merge($modules['default'], $settings);
      return $settings;
    }
  }

  public static function getContentSettings()
  {
    return self::getManifestData('content_settings');
  }
}
