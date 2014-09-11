<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ModuleController.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_ModuleController extends Core_Controller_Action_Admin
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
            ->addActionContext('license', 'json')
            ->addActionContext('edit', 'json')
            ->initContext();
    }

    public function licenseAction()
    {
        $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');

        $install_result = $this->_getParam('result', false);
        $format = $this->_getParam('format', false);

        $this->view->product = $product = $this->_getParam('name', null);
        $this->view->version = $version = $this->_getParam('version', null);
        $this->view->target_version = $target_version = $this->_getParam('target_version');

        $modulesTable = Engine_Api::_()->getDbTable('modules', 'hecore');
        $module = $modulesTable->findByName($product);

        if (!$module) {
            $module = $modulesTable->createRow();
            $module_arr = array(
                'name' => $product,
                'version' => $version,
                'key' => '',
                'installed' => 0,
                'modified_stamp' => time()
            );

            $module->setFromArray($module_arr);
            $module->save();
        }

        $new_version = ($target_version) ? $target_version : $version;

        if ($module->installed && $module->key && $module->version == $new_version) {
            $this->view->pluginInstalled = true;
            return;
        }

        $module_arr = $module->toArray();
        $this->view->form = $form = new Hecore_Form_License();

        if ($format == 'smoothbox') {
            $form->addError($this->view->translate('HECORE_LICENSE_VERIVICATION_FAILED'));
        }

        if (!$this->getRequest()->isPost()) {
            $form->name->setValue($module_arr['name']);
            $form->version->setValue($module_arr['version']);
            $form->target_version->setValue($target_version);
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $values = $form->getValues();
        $result = false;
        $error_message = '';

        $parameters = array(
            'task' => 'install',
            'license' => $values['license'],
            'product' => $values['name'],
            'domain' => $_SERVER['HTTP_HOST']
        );

        if ($target_version) {
            if (version_compare($version, $target_version) === 0) {
                $parameters['task'] = 'check_refresh';
            } else {
                $parameters['task'] = 'check_upgrade';
            }
        }

        $url_params = array(
            'module' => 'hecore',
            'controller' => 'module',
            'action' => 'license',
            'name' => $product,
            'version' => $new_version,
            'format' => 'smoothbox'
        );

        $route = Zend_Controller_Front::getInstance()->getRouter();
        $register_url = $route->assemble($url_params, 'default', true);

        $register_url = str_replace('/install', '', $register_url);
        $translate = Zend_Registry::get('Zend_Translate');

        $server_result = $hecoreApi->checkLicense($parameters);

        try {
            eval($server_result);
        } catch (Exception $e) {
            if (strstr(get_class($e), 'Zend_Db_Statement_') !== false && $e->getCode() == 2006) {
                $db = Engine_Api::_()->hecore()->checkDbConnect();
                eval($server_result);
            } else {
                print_log($e . '');
            }
        }

        $add_error_message = false;

        if ($target_version) {
            if ($result) {
                $version = $target_version;
                $this->view->pluginInstalled = true;
            } else {
                $add_error_message = true;
            }
        }

        if (!$this->view->pluginInstalled) {
            $install_result = 'failed';
        }

        if (isset($this->view->pluginInstalled) && $this->view->pluginInstalled && !$add_error_message) {
            $db = $hecoreApi->checkDbConnect();
            $db->update(
                $modulesTable->info('name'),
                array('installed' => 1, 'key' => $values['license'], 'version' => $version, 'modified_stamp' => time()),
                "name = '{$product}'"
            );
            $install_result = 'success';
        }

        if ($install_result) {
            $errors = $form->getErrorMessages();
            $error_message = ($error_message) ? $error_message : (isset($errors[0]) && $errors[0] ? $errors[0] : '');
            header("Pragma: no-cache");
            header("Content-Type: application/json");


            echo Zend_Json::encode(array('result' => $install_result, 'message' => $error_message));
            die();
        }

        $url_params = array(
            'module' => 'hecore',
            'controller' => 'module',
            'action' => 'license',
            'name' => $product,
            'version' => $version,
            'result' => $install_result,
            'format' => 'smoothbox'
        );

        $this->_helper->redirector->gotoRouteAndExit($url_params, 'default');
    }

    public function editAction()
    {
        $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
        $this->view->product = $product = $this->_getParam('name', null);

        $this->view->form = $form = new Hecore_Form_License();

        $modulesTable = Engine_Api::_()->getDbTable('modules', 'hecore');
        $module = $modulesTable->findByName($product);

        if (!$module) {
            $module = $modulesTable->createRow();
            $module_arr = array(
                'name' => $product,
                'version' => '4.0.0',
                'key' => '',
                'installed' => 0,
                'modified_stamp' => time()
            );

            $module->setFromArray($module_arr);
            $module->save();
        }

        $module_arr = $module->toArray();

        if (!$this->getRequest()->isPost()) {
            $form->name->setValue($module_arr['name']);
            $form->version->setValue($module_arr['version']);

            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $values = $form->getValues();

        $parameters = array(
            'task' => 'update',
            'license' => $values['license'],
            'product' => $values['name'],
            'domain' => $_SERVER['HTTP_HOST']
        );

        $result = $hecoreApi->checkLicense($parameters);
        eval($result);

        if (isset($this->view->keyUpdated) && $this->view->keyUpdated) {
            $module->setFromArray(array(
                'version' => $values['version'],
                'key' => $values['license'],
                'installed' => 1,
                'modified_stamp' => time()
            ));

            $module->save();
        }
    }

    public function upgradeAction()
    {
        $start = $this->_getParam('start', false);
        $operation = $this->_getParam('operation', 'upgrade');
        $he_operation = in_array($operation, array('upgrade', 'refresh')) ? $operation : 'refresh';

        $this->view->product = $product = $this->_getParam('name', '');
        $this->view->version = $version = $this->_getParam('version', '');
        $this->view->target_version = $target_version = $this->_getParam('target_version', '');
        $module_version = ($target_version) ? $target_version : $version;

        if (!$start) {
            return;
        }

        $modulesTbl = Engine_Api::_()->getDbTable('modules', 'hecore');
        $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');

        $module = $modulesTbl->findByName($product);

        if (!$module || !$module->key || !$module->installed) {
            header("Pragma: no-cache");
            header("Content-Type: application/json");
            echo Zend_Json::encode(array('result' => 'failed', 'message' => ''));
            die();
        }

        $values = array(
            'task' => $he_operation,
            'license' => $module->key,
            'product' => $module->name,
            'version' => $version,
            'target_version' => $target_version,
            'domain' => $_SERVER['HTTP_HOST']
        );

        $parameters = $values;

        $url_params = array(
            'module' => 'hecore',
            'controller' => 'module',
            'action' => 'license',
            'name' => $product,
            'version' => $module_version,
            'format' => 'smoothbox'
        );

        $route = Zend_Controller_Front::getInstance()->getRouter();
        $register_url = $route->assemble($url_params, 'default', true);

        $register_url = str_replace('/install', '', $register_url);
        $translate = Zend_Registry::get('Zend_Translate');

        $result = false;
        $add_error_message = false;
        $form = new Engine_Form();
        $server_result = $hecoreApi->checkLicense($parameters);

        try {
            eval($server_result);
        } catch (Exception $e) {
            if (strstr(get_class($e), 'Zend_Db_Statement_') !== false && $e->getCode() == 2006) {
                $db = $hecoreApi->checkDbConnect();
                eval($server_result);
            } else {
                print_log($e . '');
            }
        }

        if ($target_version) {
            if ($result || $this->view->pluginInstalled) {
                $version = $target_version;
                $this->view->pluginInstalled = true;
            } else {
                $add_error_message = true;
            }
        }

        if (!$this->view->pluginInstalled) {
            $install_result = 'failed';
        }

        if (isset($this->view->pluginInstalled) && $this->view->pluginInstalled && !$add_error_message) {
            $db = $hecoreApi->checkDbConnect();
            $db->update($modulesTbl->info('name'), array('installed' => 1, 'version' => $module_version, 'modified_stamp' => time()), "name = '{$product}'");
            $install_result = 'success';
        }

        header("Pragma: no-cache");
        header("Content-Type: application/json");
        echo Zend_Json::encode(array('result' => $install_result, 'message' => ''));
        die();
    }

    public function apptouchAction()
    {
        $step = $this->_getParam('step');
        switch ($step) {
            case 'disable_touch':
                $db = Engine_Api::_()->getDbTable('modules', 'core');
                $module = $db->getModule('touch');
                if ($module) {
                    $module->enabled = 0;
                    $module->save();
                }
                $this->view->redirect = true;
                break;
            case 'import_settings':
                if ($this->_getParam('import')){
                    $this->importSettings();
                    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('apptouch.settings.import', true);
                } else {
                    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('apptouch.settings.import', false);
                }
                $this->view->redirect = true;
                break;
            default:
                $this->view->redirect = false;
                break;
        }
    }

    private function importSettings()
    {
        $settingsDb = Engine_Api::_()->getDbTable('settings', 'core');

        // general settings
      $settings = array();
      $settings['apptouch.default'] = $settingsDb->getSetting('touch.default');
//      $settings['apptouch.integrations.only'] = $settingsDb->getSetting('touch.integrations.only'); todo this setting is unused
      $settings['apptouch.include.tablets'] = $settingsDb->getSetting('touch.include.tablets');
      $settings['apptouch.homescreen.extension'] = $settingsDb->getSetting('touch.homescreen.extension');
      $settings['apptouch.homescreen.enabled'] = $settingsDb->getSetting('touch.homescreen.enabled');
      foreach($settings as $key => $val){
        if($val !== null)
          $settingsDb->setSetting($key, $val);
      }
        // icon settings
        $apptouch_path = DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'apptouch' . DIRECTORY_SEPARATOR . 'homescreen';
        $has_app_dir = true;
        if (!is_dir(APPLICATION_PATH . '/public/apptouch/')) {
            $has_app_dir = mkdir(APPLICATION_PATH . '/public/apptouch/');
        }
        if ($has_app_dir && !is_dir(APPLICATION_PATH . '/public/apptouch/homescreen/')) {
            $has_app_dir = mkdir(APPLICATION_PATH . '/public/apptouch/homescreen/');
        }


        $touch_path = DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'touch' . DIRECTORY_SEPARATOR . 'homescreen';
        // Check if folder exists and is writable
        $has_dir = true;

        // Creating touch folder if not exists
        if (!is_dir(APPLICATION_PATH . '/public/touch/')) {
            $has_dir = mkdir(APPLICATION_PATH . '/public/touch/');
        }
        // Creating homescreen folder if not exists
        if ($has_dir && !is_dir(APPLICATION_PATH . '/public/touch/homescreen/')) {
            $has_dir = mkdir(APPLICATION_PATH . '/public/touch/homescreen/');
        }
        if (!($has_app_dir && $has_dir)) {
            return;
        }
        $this->copy_files(APPLICATION_PATH . '/' . $touch_path, APPLICATION_PATH . '/' . $apptouch_path);

    }

    private function copy_files($src, $dest)
    {
        $files = scandir($src);
        if(!empty($files)) {
            foreach($files as $file) {
                if(is_file($src . '/' . $file)) {
                    copy($src . '/' . $file, $dest . '/' . $file);
                }
            }
        }
    }

    private function dircpy($basePath, $source, $dest, $overwrite = false)
    {
        if (!is_dir($basePath . $dest)) //Lets just make sure our new folder is already created. Alright so its not efficient to check each time... bite me
            mkdir($basePath . $dest);
        if ($handle = opendir($basePath . $source)) { // if the folder exploration is sucsessful, continue
            while (false !== ($file = readdir($handle))) { // as long as storing the next file to $file is successful, continue
                if ($file != '.' && $file != '..') {
                    $path = $source . '/' . $file;
                    if (is_file($basePath . $path)) {
                        if (!is_file($basePath . $dest . '/' . $file) || $overwrite)
                            if (!@copy($basePath . $path, $basePath . $dest . '/' . $file)) {
                                echo '<font color="red">File (' . $path . ') could not be copied, likely a permissions problem.</font>';
                            }
                    } elseif (is_dir($basePath . $path)) {
                        if (!is_dir($basePath . $dest . '/' . $file))
                            mkdir($basePath . $dest . '/' . $file); // make subdirectory before subdirectory is copied
                        $this->dircpy($basePath, $path, $dest . '/' . $file, $overwrite); //recurse!
                    }
                }
            }
            closedir($handle);
        }
    }
}