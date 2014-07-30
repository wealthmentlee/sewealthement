<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ListController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Hecore_ListController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {

    $this->view->params = $params = (array)$this->_getParam('params');
    $this->view->title = $title = $this->_getParam('t');
    $this->view->module = $module = $this->_getParam('mm'); // fixed bug
    $this->view->list = $list = $this->_getParam('l');
    $this->view->page = $page = $this->_getParam('page');
    $this->view->return_url = $return_url = $this->_getParam('return_url');
    $this->view->not_logged_in = $not_logged_in = $this->_getParam('nli', 0);

    $viewer = Engine_Api::_()->user()->getViewer();

    if ($this->_getParam('keyword')){
      $params['keyword'] = $this->_getParam('keyword');
    }
    if ($this->_getParam('list_type')){
      $params['list_type'] = $this->_getParam('list_type');
    }
    $this->view->list_type = (isset($params['list_type'])) ? $params['list_type'] : 'all';

    $this->view->url_params = array(
      'params' => $params,
      't' => $title,
      'mm' => $module,
      'l' => $list,
      'return_url' => $return_url,
      'not_logged_in' => $not_logged_in,
    );

    if (!$not_logged_in && !$viewer->getIdentity()) {
      $this->view->error = 1;
      $this->view->message = $this->view->translate("hecore_You should be logged in to view this page.");
      return ;
    }

    $this->view->module = $module = trim(strtolower($module));

    $table = Engine_Api::_()->getDbTable('modules', 'core');
    $select = $table->select()
        ->where("name = ?", $module);

    if (!$table->getAdapter()->fetchOne($select)) {
      $this->view->error = 2;
      $this->view->message = "Module does not exists.";
      return ;
    }
    $api = Engine_Api::_()->getApi('core', $module);
    if (!is_callable(array($api, $list))) {
      $this->view->error = 3;
      $this->view->message = "Method does not exists in module's API.";
      return ;
    }
    $api = Engine_Api::_()->$module();
    if (!method_exists($api, $list)) {
      $this->view->error = 5;
      $this->view->message = "Method '$list' does not exists.";
      return ;
    }
    $this->view->items = $items = Engine_Api::_()->$module()->$list($params);
    if ($items === null) {
      $this->view->error = 4;
      $this->view->message = "Method returned null.";
      return ;
    }
    if ($items instanceof Zend_Paginator) {
      $items->setCurrentPageNumber($page);
      $this->view->total = $items->getTotalItemCount();
      $this->view->current_count = $items->getCurrentItemCount();
    }



  }

}