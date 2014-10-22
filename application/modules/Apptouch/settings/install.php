<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_Installer extends Engine_Package_Installer_Module
{
    public function onPreInstall()
    {
        parent::onPreInstall();

        $db = $this->getDb();
        $translate = Zend_Registry::get('Zend_Translate');
        $select = $db->select()
            ->from('engine4_core_modules')
            ->where('name = ?', 'hecore')
            ->where('enabled = ?', 1);

        $hecore = $db->fetchRow($select);

        if (!$hecore) {
            $error_message = $translate->_('Error! This plugin requires Hire-Experts Core module. It is free module and can be downloaded from Hire-Experts.com');
            return $this->_error($error_message);
        }

        if (version_compare($hecore['version'], '4.2.2') < 0) {
            $error_message = $translate->_('This plugin requires Hire-Experts Core Module. We found that you has old version of Core module, please download latest version of Hire-Experts Core Module and install. Note: Core module is free.');
            return $this->_error($error_message);
        }
      if(((float)phpversion()) >= 5){
        $setting = $db->select()
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'apptouch.use.dev.scripts');
        $setting = $db->fetchRow($setting);
        $headers = get_headers(rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']), 1);
        $val = strpos(@$headers['Server'], 'cloudflare-nginx') !== false || strpos($_SERVER['SERVER_ADDR'], '5.9.') === 0;
          if(!$setting){
            $db->insert('engine4_core_settings', array(
                    'name' => 'apptouch.use.dev.scripts',
                    'value' => $val,
                  ));
          } else {
            $db->query("UPDATE `engine4_core_settings` SET `value` = '{$val}' WHERE `name` = 'apptouch.use.dev.scripts'");
          }
      }
        $operation = $this->_databaseOperationType;
        $module_name = $this->getOperation()->getTargetPackage()->getName();

        $select = $db->select()
            ->from('engine4_hecore_modules')
            ->where('name = ?', $module_name);

        $module = $db->fetchRow($select);

        if ($module && isset($module['installed']) && $module['installed']
            && isset($module['version']) && $module['version'] == $this->_targetVersion
            && isset($module['modified_stamp']) && ($module['modified_stamp'] + 1000) > time()
        ) {
            return;
        }
        if ($operation == 'install') {
            $select = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'touch');

            $touch = $db->fetchRow($select);

            if ($touch && $touch['enabled']) {
                $route = Zend_Controller_Front::getInstance()->getRouter();
                if ($route->getParam('disable_touch')) {
                    $db->update('engine4_core_modules', array(
                        'enabled' => 0
                    ), array(
                        'name = ?' => 'touch'
                    ));
                } else {
                    $url_params = array(
                        'module' => 'hecore',
                        'controller' => 'module',
                        'action' => 'apptouch',
                        'step' => 'disable_touch',
                        'format' => 'smoothbox'
                    );
                    $url = $route->assemble($url_params, 'default', true);
                    $url = str_replace('/install', '', $url);
                    $message = $translate->_('We found that you has old Hire-Experts Touch-Mobile Plugin, we strongly recommend to disable old Touch-Mobile Plugin for stable functional of New Touch-Mobile/Application Plugin. <a class="smoothbox" href="%s">Disable old Touch-Mobile</a>');
                    $message = sprintf($message, $url);

                    return $this->_error($message);
                }
            }
            if ($touch) {
                $route = Zend_Controller_Front::getInstance()->getRouter();
                $select = $db->select()->from('engine4_core_settings')->where('name = ?', 'apptouch.settings.import');
                $apptouch_settings_import = $db->fetchRow($select);
                if (!$apptouch_settings_import) {
                    $message = $translate->_('Would you like to import some settings from old Touch-Mobile Plugin? <a class="smoothbox" href="%s">Yes, would be Great</a> - <a class="smoothbox" href="%s">No, Thanks</a>');
                    $url_params = array(
                        'module' => 'hecore',
                        'controller' => 'module',
                        'action' => 'apptouch',
                        'step' => 'import_settings',
                        'import' => true,
                        'format' => 'smoothbox'
                    );
                    $url_yes = $route->assemble($url_params, 'default', true);
                    $url_yes = str_replace('/install', '', $url_yes);
                    $url_params['import'] = false;
                    $url_no = $route->assemble($url_params, 'default', true);
                    $url_no = str_replace('/install', '', $url_no);
                    $message = sprintf($message, $url_yes, $url_no);

                    return $this->_error($message);
                }
            }

        } elseif ($operation == 'upgrade') {
          $this->_patchServerScripts($db);
        }
        $select = $db->select()
            ->from('engine4_hecore_modules')
            ->where('name = ?', 'timeline')
            ->where('installed = ?', 1);

        $timeline = $db->fetchRow($select);

        if ($timeline && version_compare($timeline['version'], '4.0.1p1') < 0) {
            $error_message = $translate->_('We found that you has old version of Hire-Experts Timeline Plugin, please download latest version of Timeline Plugin and upgrade.');
            return $this->_error($error_message);
        }

        if ($operation == 'install') {

            if ($module && $module['installed']) {
                return;
            }

            $url_params = array(
                'module' => 'hecore',
                'controller' => 'module',
                'action' => 'license',
                'name' => $module_name,
                'version' => $this->_targetVersion,
                'format' => 'smoothbox'
            );

            $route = Zend_Controller_Front::getInstance()->getRouter();
            $register_url = $route->assemble($url_params, 'default', true);
            $register_url = str_replace('/install', '', $register_url);

            $error_message = $translate->_('It is paid plugin from Hire-Experts LLC. You need to type License Key to install this module - <a class="smoothbox" href="%s">Click Here</a>');
            $error_message = sprintf($error_message, $register_url);

            return $this->_error($error_message);
        } else {

            $url_params = array(
                'module' => 'hecore',
                'controller' => 'module',
                'action' => 'upgrade',
                'name' => $module_name,
                'version' => $this->_currentVersion,
                'target_version' => $this->_targetVersion,
                'operation' => $operation,
                'format' => 'smoothbox'
            );

            $route = Zend_Controller_Front::getInstance()->getRouter();
            $register_url = $route->assemble($url_params, 'default', true);
            $register_url = str_replace('/install', '', $register_url);

            $error_message = $translate->_('It is paid plugin from Hire-Experts LLC. You need to type License Key to upgrade this module - <a class="smoothbox" href="%s">Click Here</a>');
            $error_message = sprintf($error_message, $register_url);
            return $this->_error($error_message);
        }

    }

  private function _patchServerScripts(Zend_Db_Adapter_Abstract $db){
    try{
      @$db->query("UPDATE `engine4_apptouch_menuitems` SET `plugin`='Apptouch_Plugin_PageMenus' WHERE `name`='page_quick_create';");
      @$db->query("UPDATE `engine4_apptouch_menuitems` SET `plugin`='Apptouch_Plugin_Menus' WHERE `name`='user_profile_send_credits';");
//      $result = $db->query("SHOW COLUMNS FROM `engine4_apptouch_menuitems` LIKE 'appenabled'");
//      $result->fetch('field');
//      if(!mysql_num_rows($result))
      @$db->query("ALTER TABLE `engine4_apptouch_menuitems` ADD COLUMN `appenabled` TINYINT(1) NOT NULL DEFAULT '1' AFTER `enabled`;"); // 4.2.1p2
      @$db->query("DELETE FROM `engine4_aptouch_content` WHERE `content_id` = 189;"); // 4.2.2p1

      @$db->query("INSERT IGNORE INTO `engine4_apptouch_content` (`page_id`, `component_name`, `order`) VALUES (16, 'comments', 5);"); // 4.2.4

    } catch(Exception $e){};
  }
}