<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2010-08-02 16:05 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Installer extends Engine_Package_Installer_Module
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

    if (version_compare($hecore['version'], '4.2.0p1') < 0) {
      $error_message = $translate->_('This plugin requires Hire-Experts Core Module. We found that you has old version of Core module, please download latest version of Hire-Experts Core Module and install. Note: Core module is free.');
      return $this->_error($error_message);
    }

    $select = $db->select()
      ->from('engine4_hecore_modules')
      ->where('name = ?', 'pages');

    $page = $db->fetchRow($select);

    if ($page && version_compare($page['version'], '4.1.9') < 0) {
      $error_message = $translate->_('This plugin requires Pages Plugin. We found that you have old version of Pages module, please download latest version of Pages Module and install.');
      return $this->_error($error_message);
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
    }
    else { //$operation = upgrade|refresh

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

      $error_message = $translate->_('It is paid plugin from Hire-Experts LLC. You need to type License Key to install this module - <a class="smoothbox" href="%s">Click Here</a>');
      $error_message = sprintf($error_message, $register_url);

      return $this->_error($error_message);
    }
  }

  function onInstall()
  {
    parent::onInstall();

    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    $select
      ->from('engine4_core_modules')
      ->where('name = ?', 'page')
      ->limit(1);

    $core_module = $select->query()->fetchObject();

    if ($core_module && $core_module->name) {
      $db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`) "
        . "VALUES ('rate', 'rate.page-review', 16, '{\"title\":\"RATE_REVIEW_TABITEM\", \"titleCount\":true}');");

      $db->query("INSERT IGNORE INTO `engine4_rate_types` (`category_id`, `label`, `order`) "
        . "SELECT `option_id` AS `category_id`, 'Rate' AS `label`, 1 AS `order` "
        . "FROM `engine4_page_fields_options` "
        . "WHERE `field_id` = 1;");
    }

    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    $select
      ->from('engine4_core_modules')
      ->where('name = ?', 'offers')
      ->limit(1);

    $core_module = $select->query()->fetchObject();

    if ($core_module && $core_module->name) {
      $db->query("INSERT IGNORE INTO `engine4_rate_offertypes` (`category_id`, `label`, `order`) "
        . "SELECT `category_id`, 'Rate' AS `title`, 1 AS `order` "
        . "FROM `engine4_offers_categories`;");
    }
  }
}