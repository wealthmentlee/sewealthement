<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ListController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_ListController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {
    if (!$this->_helper->requireUser->isValid()){
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $manifest_tabs = Engine_Api::_()->wall()->getManifestType('wall_list');
    $this->view->tabs = $tabs = array_keys($manifest_tabs);

    $this->view->search = $search = $this->_getParam('search');
    $this->view->tab = $tab = $this->_getParam('tab');


    $tab = (in_array($tab, $tabs)) ? $this->_getParam('tab') : 'all';

    $params = array(
      'search' => $search
    );

    if ($tab == 'all'){

      $db = Engine_Db_Table::getDefaultAdapter();

      $select = $db->select();

      foreach ($manifest_tabs as $manifest_tab){
        $plugin = Engine_Api::_()->loadClass($manifest_tab['plugin']);
        $select->union(array('(' .$plugin->getSelect($viewer, $params) . ')'));
      }

    } else {

      $plugin = Engine_Api::_()->loadClass($manifest_tabs[$tab]['plugin']);
      $select = $plugin->getSelect($viewer, $params);

    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(12);

    $this->view->browse = $browse = Engine_Api::_()->wall()->getItems($paginator->getCurrentItems());

    $this->view->prev = (isset($paginator->getPages()->previous)) ? 1 : 0;
    $this->view->next = (isset($paginator->getPages()->next)) ? 1 : 0;
    $this->view->count = $paginator->getTotalItemCount();

    // Ajax
    if ($this->_getParam('format') == 'json'){
      $this->view->html = $this->view->render('list/pagination.tpl');
      return ;
    }

    // Smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $list_id = (int) $this->_getParam('list_id');

    if (!$list_id){
      return ;
    }

    $table = Engine_Api::_()->getDbTable('lists', 'wall');
    $list = $table->findRow($list_id);

    if (!$list){
      return ;
    }

    $items = $list->getItems();

    $this->view->edit = true;
    $this->view->list = $list;

    $this->view->selected = Engine_Api::_()->wall()->getItems($items);
    $this->view->guids = array_keys(Engine_Api::_()->wall()->setItemsGuid($items));

  }


  public function saveAction()
  {
    $this->view->result = false;
    $this->view->message = $this->view->translate('WALL_ERROR');

    if (!$this->_helper->requireUser->isValid()){
      return;
    }

    $form = new Wall_Form_List();
    $viewer = Engine_Api::_()->user()->getViewer();

    $list_id = (int) $this->_getParam('list_id');

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      $this->view->message = $this->view->translate('WALL_LIST_INVALID');
      return ;
    }

    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {

      $table = Engine_Api::_()->getDbTable('lists', 'wall');

      if ($list_id){
        $list = $table->findRow($list_id);
        if (!$list && $list->user_id != $viewer->getIdentity()){
          return ;
        }
      } else {
        $list = $table->createRow();
      }
      $list->setFromArray($values);
      $list->user_id = $viewer->getIdentity();
      $list->save();

      $items = Engine_Api::_()->wall()->guidsToItems($this->_getParam('guids'));
      $models = Engine_Api::_()->wall()->getItems($items);

      $list->clearItems();

      foreach ($models as $model){
        if ($model instanceof Core_Model_Item_Abstract){
          $list->addItem($model);
        }
      }

      $db->commit();

      $this->view->result = true;
      if ($list_id){
        $this->view->message = $this->view->translate('WALL_LIST_EDITED');
      } else {
        $this->view->message = $this->view->translate('WALL_LIST_CREATED');
      }

      $params = array(
        'types' => array_keys(Engine_Api::_()->wall()->getManifestType('wall_type')),
        'lists' => $table->getPaginator($viewer),

      );

      $this->view->list_id = $list->list_id;
      $this->view->html = $this->view->partial('_list.tpl', null, $params);


    } catch (Exception $e){
      return ;
    }

  }


  public function removeAction()
  {
    $this->view->result = false;
    $this->view->message = $this->view->translate('WALL_ERROR');

    if (!$this->_helper->requireUser->isValid()){
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('lists', 'wall');

    $list_id = (int) $this->_getParam('list_id');

    $list = $table->findRow($list_id);
    if (!$list || $list->user_id != $viewer->getIdentity()){
      return ;
    }

    $list->delete();

    $this->view->result = true;
    $this->view->message = $this->view->translate('WALL_LIST_REMOVED');

    $setting = Engine_Api::_()->wall()->getUserSetting($viewer);

    $params = array(
      'setting' => $setting,
      'types' => array_keys(Engine_Api::_()->wall()->getManifestType('wall_type')),
      'lists' => $table->getPaginator($viewer)
    );

    $this->view->html = $this->view->partial('_list.tpl', null, $params);

  }

  


}
