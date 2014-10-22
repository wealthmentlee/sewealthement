<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 8758 2011-03-30 23:50:30Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Apptouch_Widget_AdminStatisticsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // License info
    $this->view->license = Engine_Api::_()->getApi('settings', 'core')->core_license;

    // check if it is a trial license
    if (!Core_Form_Admin_Settings_License::validateKey($this->view->license['key'])) {
      $this->view->license['key'] = $this->view->htmlLink(array('action' => 'license-key'), $this->view->translate('Update License Key'), array('class' => 'smoothbox'));
    }
    $this->view->licenseKey = Engine_Api::_()->getDbtable('modules', 'hecore')->select()
      ->from('engine4_hecore_modules', 'key')
      ->where('name = ?', 'apptouch')
      ->query()
      ->fetchColumn();

    // Get the core module version
    $this->view->apptouchVersion = Engine_Api::_()->getDbtable('modules', 'core')->select()
      ->from('engine4_core_modules', 'version')
      ->where('name = ?', 'apptouch')
      ->query()
      ->fetchColumn();
    $tabletVersion = Engine_Api::_()->getDbtable('modules', 'core')->select()
            ->from('engine4_core_modules', 'version')
            ->where('name = ?', 'apptablet')
            ->query()
            ->fetchColumn();
    if($tabletVersion){
      $this->view->tabletVersion = $tabletVersion;
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('apptablet'))
        $this->view->tablet = 'installed';
      else
        $this->view->tablet = 'disabled';
    } else {
      $this->view->tablet = false;
      $this->view->tabletVersion = '';

    }
    $appVersion = Engine_Api::_()->getDbtable('modules', 'core')->select()
            ->from('engine4_core_modules', 'version')
            ->where('name = ?', 'appmanager')
            ->query()
            ->fetchColumn();
    if($appVersion){
      $this->view->appVersion = $appVersion;
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('appmanager'))
        $this->view->app = 'installed';
      else
        $this->view->app = 'disabled';
    } else {
      $this->view->app = false;
      $this->view->appVersion = '';

    }

    $this->view->pluginsCount = Engine_Api::_()->getDbtable('modules', 'core')->select()
      ->from('engine4_core_modules', 'count(type)')
      ->where('type = ?', 'extra')
      ->group('type')
      ->query()
      ->fetchColumn();
    $this->view->supportedIntegrations = 24;

    // Statistics
    $statistics = array();

    // views
    $statistics['core.views'] = array(
      'label' => 'Page Views',
      'apptouch' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('apptouch.core.views'),
      'ios' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('ios.core.views'),
      'total' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('core.views'),
    );

    // signups
    $statistics['user.creations'] = array(
      'label' => 'Members',
      'apptouch' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('apptouch.user.creations'),
      'ios' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('ios.user.creations'),
      'total' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('user.creations'),
    );

    // logins
    $statistics['user.logins'] = array(
      'label' => 'Sign-ins',
      'apptouch' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('apptouch.user.logins'),
      'ios' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('ios.user.logins'),
      'total' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('user.logins'),
    );

    // messages
    $statistics['messages.creations'] = array(
      'label' => 'Private Messages',
      'apptouch' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('apptouch.messages.creations'),
      'ios' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('ios.messages.creations'),
      'total' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('messages.creations'),
    );

    // friendships
    // @todo this only works properly for two-way, verified friendships for now
    $statistics['user.friendships'] = array(
      'label' => 'Friendships',
      'apptouch' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('apptouch.user.friendships'),
      'ios' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('ios.user.friendships'),
      'total' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('user.friendships'),
    );

    // comments
    // @todo this doesn't include activity feed, users, group, or events for now
    $statistics['core.comments'] = array(
      'label' => 'Comments',
      'apptouch' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('apptouch.core.comments'),
      'ios' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('ios.core.comments'),
      'total' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('core.comments'),
    );

    //likes
    $statistics['core.likes'] = array(
      'label' => 'Likes',
      'apptouch' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('apptouch.core.likes'),
      'ios' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('ios.core.likes'),
      'total' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('core.likes'),
    );

    // comments
    // @todo this doesn't include activity feed, users, group, or events for now
    $statistics['core.comments'] = array(
      'label' => 'Comments',
      'apptouch' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('apptouch.core.comments'),
      'ios' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('ios.core.comments'),
      'total' => Engine_Api::_()->getDbtable('statistics', 'core')->getTotal('core.comments'),
    );


    // Hooks
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onAdminStatistics');
    $statistics += (array)$event->getResponses();

    if (!$this->_getParam('show_as_chart')) {
      // Online users
      //      $onlineTable = Engine_Api::_()->getDbtable('online', 'user');
      //      $onlineUserCount = $onlineTable->select()
      //        ->from($onlineTable->info('name'), new Zend_Db_Expr('COUNT(DISTINCT user_id)'))
      //        ->where('user_id > ?', 0)
      //        ->where('active > ?', new Zend_Db_Expr('DATE_SUB(NOW(),INTERVAL 20 MINUTE)'))
      //        ->query()
      //        ->fetchColumn(0)
      //        ;
      //
      //      $statistics['users.online'] = array(
      //        'label' => 'Online Members',
      //        'touch' => null,
      //        'total' => $onlineUserCount,
      //      );
      // Assign
      $this->view->statistics = $statistics;
      return;
    }
    $columns = array(
      array('string', $this->view->translate("Statistics")),
      array('number', $this->view->translate("APPTOUCH_Via Mobile Devices")),
      array('number', $this->view->translate("APPTOUCH_Via Application")),
      array('number', $this->view->translate("APPTOUCH_On Standard Site"))
    );
    $rows = array();
    $options = array(
      'title' => $this->view->translate('Statistics'),
      'height' => 600
    );
    $i = 0;

    foreach ($statistics as $key => $stat) {
      $apptouch = (float)$stat['apptouch'];
      $ios = (float)$stat['ios'];
      $std = (float)($stat['total'] - $apptouch - $ios);

      if ($apptouch == 0 && $std == 0)
        continue;
      //      if($touch>1000 || $std > 1000){
      //        $touch/1000;
      //        $std/=1000;
      //      }
      //

      $rows[$i] = array($this->view->translate($stat['label']) . ' (' . $apptouch . '/' . $ios . '/' . $std . ')', $apptouch, $ios, $std);
      $i++;
    }
    $chart_params = array(
      'type' => 'column',
      'options' => $options,
      'data' => array(
        'columns' => $columns,
        'rows' => $rows
      )
    );
    $this->view->chart_params = $chart_params;
  }
}