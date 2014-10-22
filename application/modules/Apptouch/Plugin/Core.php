<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Apptouch_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
    }

    public function checkSiteMode(Zend_Controller_Request_Abstract $request)
    {
        if ($mode = $request->getParam('apptouch-site-mode')) {

            $session = new Zend_Session_Namespace('apptouch-site-mode');
            $oldPlugin = new Zend_Session_Namespace('standard-mobile-mode');
            $mobi = new Zend_Session_Namespace('mobile');
            $mobi->mobile = false;
            if ($mode === 'apptouch' || $mode === 'apptablet' || $mode === 'standard' || $mode === 'simulator') {
                $session->__set('mode', $mode);
                $oldPlugin->__set('mode', $mode);
            } elseif ($mode) {
                $session->__unset('mode');
                $oldPlugin->__unset('mode');
            }
            return $mode;
        }
    }

    private function detectSimulator(Zend_Controller_Request_Abstract $request)
    {
        $view = Zend_Registry::get('Zend_View');
        if ($view instanceof Zend_View) {
            $user = Engine_Api::_()->user()->getViewer();
            if (isset($user->level_id) && $user->level_id < 4 && $request->getParam('REQUEST_TYPE') != 'xmlhttprequest') {
                $script = "
        Cookie.write('windowwidth', window.innerWidth, {path: '/'});
        window.onfocus = function () {
        Cookie.write('windowwidth', window.innerWidth, {path: '/'});
        };
        ";
                $view->headScript()
                    ->appendScript($script);
            }
        }
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        if ($this->checkSiteMode($request) != 'apptouch' && $this->checkSiteMode($request) != 'apptablet')
            $this->detectSimulator($request);
        if (!Engine_Api::_()->apptouch()->isApptouchMode($request)) {
            return;
        }
        $session = new Zend_Session_Namespace('mobile');

        if ($session->mobile) {
            $session->mobile = false;
            $request = Engine_Api::_()->apptouch()->resetMobi($request);
        }

        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $moduleM = Engine_Api::_()->getDbTable('modules', 'core')->getModule('album');
        $HEAAM = Engine_Api::_()->getDbTable('modules', 'core')->getModule('headvancedalbum');
        $settings = Engine_Api::_()->getApi('settings', 'core');

        if ($HEAAM && $HEAAM->enabled && $moduleM && $moduleM->enabled && $settings->getSetting('page.browse.pagealbum')) {
            if ($request->getModuleName() == 'headvancedalbum' && $request->getControllerName() == 'index' && ($request->getActionName() == 'browse' || $request->getActionName() == 'manage')) {
                $request->setModuleName('pagealbum');
                $request->setControllerName('albums');
            }
        }
        //todo use common function

        if ($module == 'hecore' && $controller == 'module') {
            return;
        }

        if ($module == 'apptouch' || $module == 'appmanager') {
            return;
        }
        if (preg_match('/^admin-/', $controller)) {
            return;
        }

        if ($this->redirect($request)) {
            if (!($request->getParam('format') && $request->getParam('format') === 'json')) {
                Engine_Api::_()->apptouch()->setLayout();
            }
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (Engine_Api::_()->apptouch()->isApptouchMode($request)) {

            Zend_Registry::set('pus_redirect', false);


            $front = Zend_Controller_Front::getInstance();
            $module = $front->getRequest()->getModuleName();
            $controller = $front->getRequest()->getControllerName();
            $action = $front->getRequest()->getActionName();

            if ($module != 'apptouch' || $controller != 'user' || $action != 'signup-index') {
                return;
            }

            $settings = Engine_Api::_()->getApi('settings', 'core');
            // check settings
            if ($settings->getSetting('user.signup.inviteonly') == 0) {
                return;
            }

            $session = new Zend_Session_Namespace('inviter');

            $invite_code = ($session->__isset('invite_code')) ? $session->__get('invite_code') : false;
            $invite_email = ($session->__isset('invite_email')) ? $session->__get('invite_email') : false;
            $tmp_invite_row = ($session->__isset('tmp_invite_row')) ? $session->__get('tmp_invite_row') : false;

            $code = isset($_REQUEST['code']) ? $_REQUEST['code'] : false;
            $email = isset($_REQUEST['email']) ? $_REQUEST['email'] : false;

            $inviterTbl = Engine_Api::_()->getDbTable('invites', 'inviter');
            $coreTbl = Engine_Api::_()->getDbtable('invites', 'invite');

            if ($invite_code && !$code && !$email && $tmp_invite_row) {
                $coreSel = $coreTbl->select()
                    ->orWhere("code = ?", $invite_code)
                    ->orWhere("recipient = ?", $invite_email);

                $coreInvites = $coreTbl->fetchAll($coreSel);

                foreach ($coreInvites as $coreInvite) {
                    $coreInvite->delete();
                }

                return;
            }

            if (!$invite_code || !$code || !$email || $invite_code != $code) {
                return;
            }

            $inviterSel = $inviterTbl->select()
                ->where('code = ?', $invite_code)
                ->where('new_user_id = ?', 0);

            $invites = $inviterTbl->fetchAll($inviterSel);
            if ($invites->count() == 0) {
                return;
            }

            $invite = $invites->getRow(0);

            if (!$invite) {
                return;
            }

            $coreSel = $coreTbl->select()
                ->where('code = ?', $invite_code)
                ->where('new_user_id = ?', 0);

            if ($coreTbl->fetchRow($coreSel)) {
                return;
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $inviteCoreItem = $coreTbl->createRow();
                $inviteCoreItem->setFromArray(array(
                    'user_id' => $invite->user_id,
                    'recipient' => $email,
                    'code' => $invite_code,
                    'timestamp' => $invite->sent_date,
                ));

                $inviteCoreItem->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
            }

            $session->__set('tmp_invite_row', true);

            if (!$invite_email) {
                $session->__set('invite_email', $email);
            }

        }
        return;
    }

    // Ignore all module customizations --=: Ulan :=--
    protected function ignoreCustomizations(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $action = $request->getActionName();
        $oldM = $module;
        //Ignoring Social DNA plugin (User customization)
        if ($module == 'socialdna') {
            $module = 'user';
        }
        // Ignoring SocialEngineAddOns Sitealbum plugin (Std. Album customization)
        if ($module == 'sitealbum') {
            $module = 'album';
            if ($action == 'index')
                $request->setActionName('browse');
        }

        if ($module == 'ialbum') {
            $module = 'album';
        }

        if ($module == 'businesstheme') {
            $module = 'core';
        }

        if ($module == 'headvancedalbum') {
            $module = 'album';
            //    todo blitz
            $c = $request->getControllerName();
            $a = $request->getActionName();
            if ($a == 'view' && $c == 'index') {
                $request->setControllerName('album');
                $request->setParam('controller', 'album');
            }
            //    todo blitz
            //      if($a = 'view' && $c = 'index'){
            //        $request->setControllerName('album');
            //      }
        }

        // Ignoring YouNet Advanced Music plugin (Std. Music customization)
        if ($module == 'ynmusic') {
            $module = 'music';
        }

        // Ignoring YouNet Advanced Forum plugin (Std. Music customization)
        if ($module == 'ynforum') {
            $module = 'forum';
        }

        // Ignoring YouNet Advanced Group plugin (Std. Group customization)
        /*if ($module == 'advgroup') {
            $module = 'group';
        }*/

        // Ignoring YouNet Avatar plugin (Std. Profile customization)
        if ($module == 'avatar') {
            $module = 'user';
        }
        if ($module == 'advancedmembers') {
            $module = 'user';
            $request->setParam('controller', 'index');
            $request->setParam('action', 'browse');

            $request->setControllerName('index');
            $request->setActionName('browse');
        }

        // Ignoring YouNet Advanced Search plugin (Std. Search customization)
        if ($module == 'ynadvsearch') {
            $module = 'core';
        }
/*        if ($module == 'heevent') {
            $module = 'event';
        }*/
        if ($module == 'advancedsearch') {
            $module = 'core';
            $request->setParam('controller', 'search');
            $request->setParam('action', 'index');

            $request->setControllerName('search');
            $request->setActionName('index');
            // Ignoring Web Hive Timeline plugin
        }

        if ($request->getActionName() == 'grandopening') {
            $module = 'grandopening';
            $request->setControllerName('email');
//      if(@$_GET['REQUEST_TYPE'] == 'xmlhttprequest')

            $request->setParam('controller', 'email');

            if (Engine_Api::_()->apptouch()->isApp())
                $request->setParam('format', 'json');
            $request->setActionName('add');
        }
        // Ignoring Web Hive Timeline plugin
        if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('timeline') && $module == 'timeline') {
            $module = 'user';
        }
        if ($request->getModuleName() == 'home') {
            $module = 'core';
        }

        if ($oldM != $module) {
            $request->setParam('module', $module);
            $request->setModuleName($module);
        }

        return $module;
    }

    // --=: Ulan :=-- {
    private function redirect(Zend_Controller_Request_Abstract $request)
    {

        $request->setParam('controller', $request->getControllerName());
        $controller = $this->ignoreCustomizations($request);
        $oldActionName = ($request->getActionName() ? $request->getActionName() : 'index');
        $action = $request->getControllerName() . '-' . $oldActionName;
        $request->setModuleName('apptouch');
        $request->setControllerName($controller);
        $request->setActionName($action);
        $request->setParam('action', $oldActionName);
        return $request;
    }

    // } --=: Ulan :=--

    /* for Timeline */
    public function onUserCoverPhotoUpload($event)
    {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return;
        }

        $payload = $event->getPayload();

        if (empty($payload['user']) || !($payload['user'] instanceof Core_Model_Item_Abstract)) {
            return;
        }
        if (empty($payload['file']) || !($payload['file'] instanceof Storage_Model_File)) {
            return;
        }

        $viewer = $payload['user'];
        $file = $payload['file'];

        // Get album
        $table = Engine_Api::_()->getDbtable('albums', 'timeline');
        $album = $table->getSpecialAlbum($viewer, 'cover');

        $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
        $photo = $photoTable->createRow();
        $photo->setFromArray(array(
            'owner_type' => 'user',
            'owner_id' => $viewer->getIdentity()
        ));
        $photo->save();
        $photo->setPhoto($file);

        $photo->album_id = $album->album_id;
        $photo->save();

        if (!$album->photo_id) {
            $album->photo_id = $photo->getIdentity();
            $album->save();
        }

        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
        $auth->setAllowed($album, 'everyone', 'view', true);
        $auth->setAllowed($album, 'everyone', 'comment', true);

        $event->addResponse($photo);
    }

    public function onUserBornPhotoUpload($event)
    {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return;
        }

        $payload = $event->getPayload();

        if (empty($payload['user']) || !($payload['user'] instanceof Core_Model_Item_Abstract)) {
            return;
        }
        if (empty($payload['file']) || !($payload['file'] instanceof Storage_Model_File)) {
            return;
        }

        $viewer = $payload['user'];
        $file = $payload['file'];

        // Get album
        $table = Engine_Api::_()->getDbtable('albums', 'timeline');
        $album = $table->getSpecialAlbum($viewer, 'birth');

        $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
        $photo = $photoTable->createRow();
        $photo->setFromArray(array(
            'owner_type' => 'user',
            'owner_id' => $viewer->getIdentity()
        ));
        $photo->save();
        $photo->setPhoto($file);

        $photo->album_id = $album->album_id;
        $photo->save();

        if (!$album->photo_id) {
            $album->photo_id = $photo->getIdentity();
            $album->save();
        }

        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
        $auth->setAllowed($album, 'everyone', 'view', true);
        $auth->setAllowed($album, 'everyone', 'comment', true);

        $event->addResponse($photo);
    }
    /* for Timeline */

}