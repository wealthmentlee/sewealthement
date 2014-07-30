<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ItemsController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_ItemsController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {
    $this->view->fn = $fn = $this->_getParam('fn');
    $this->view->m = $module = $this->_getParam('m');
    $this->view->search = $search = $this->_getParam('search');

    $page = (int)$this->_getParam('page', 1);

    $this->view->params = $params = array(
      'subject' => $this->_getParam('subject'),
      'page' => $page,
      'search' => $search
    );

    $items = null;

    if (empty($fn) || empty($module)){
      return ;
    }

    try {

      $api = Engine_Api::_()->getApi('core', $module);
      $method = 'getItems' . ucfirst($fn);

      if (is_callable(array($api, $method))){

        $paginator = $api->{$method}($params);

        if ($paginator instanceof Zend_Paginator){

          $this->view->paginator = $paginator;
          $paginator->setItemCountPerPage(12);
          $paginator->setCurrentPageNumber($page);


          $this->view->items = $paginator->getCurrentItems();
          $this->view->prev = (isset($paginator->getPages()->previous)) ? 1 : 0;
          $this->view->next = (isset($paginator->getPages()->next)) ? 1 : 0;


          // Ajax
          if ($this->_getParam('format') == 'json'){
            $this->view->html = $this->view->render('items/pagination.tpl');
            return ;
          }

        }
      }

    } catch (Exception $e){

    }

    

  }


  public function selectAction()
  {
    $this->view->fn = $fn = $this->_getParam('fn');
    $this->view->m = $module = $this->_getParam('m');
    $this->view->search = $search = $this->_getParam('search');
    $this->view->selected = $selected = $this->_getParam('selected');

    $page = (int)$this->_getParam('page', 1);

    $this->view->params = $params = array(
      'subject' => $this->_getParam('subject'),
      'page' => $page,
      'search' => $search
    );

    $items = null;

    if (empty($fn) || empty($module)){
      return ;
    }

    try {

      $api = Engine_Api::_()->getApi('core', $module);
      $method = 'getSelectItems' . ucfirst($fn);

      if (is_callable(array($api, $method))){

        $paginator = $api->{$method}($params);

        if ($paginator instanceof Zend_Paginator){

          $this->view->paginator = $paginator;
          $paginator->setItemCountPerPage(12);
          $paginator->setCurrentPageNumber($page);


          $this->view->items = $paginator->getCurrentItems();
          $this->view->prev = (isset($paginator->getPages()->previous)) ? 1 : 0;
          $this->view->next = (isset($paginator->getPages()->next)) ? 1 : 0;


          // Ajax
          if ($this->_getParam('format') == 'json'){
            $this->view->html = $this->view->render('items/pagination.tpl');
            return ;
          }

        }
      }

    } catch (Exception $e){

    }



  }

}