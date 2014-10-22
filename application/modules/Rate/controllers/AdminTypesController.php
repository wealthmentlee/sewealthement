<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminTypesController.php 2010-07-02 19:27 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_AdminTypesController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('rate_admin_main', array(), 'rate_admin_main_review');

    // if pages not installed or disbled
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
      $this->_forward('notfound', 'error', 'core');
      return;
  	}

    $categories = Engine_Api::_()->getApi('core', 'rate')->getPageCategories();

    $this->view->categories = array();
    $category_ids = array();
    foreach ($categories as $category) {
      $category_ids[] = $category->option_id;
      $this->view->categories[$category->option_id] = $category->label;
    }

    $category_id = $this->_getParam('category_id', false);
    if (!$category_id || !in_array($category_id, $category_ids)) {
      $category_id = $category_ids[0];
    }
    $this->view->category_id = $category_id;


    $type_key = 'type_';

    $tbl_type = Engine_Api::_()->getDbTable('types', 'rate');
    $select = $tbl_type->select()
        ->where('category_id = ?', $category_id)
        ->order('order');
    $types = $tbl_type->fetchAll($select);

    $form = new Rate_Form_Review_TypeEdit();

    $create_link = '<div class="create">'.$this->view->htmlLink(
      array(
        'module' => 'rate',
        'controller' => 'types',
        'action' => 'create',
        'category' => $category_id
      ),
      $this->view->translate('RATE_REVIEW_TYPECREATE_TITLE'),
      array('class' => 'smoothbox')
    ).'</div>';

    $form->submit->addDecorator('TypeSubmit', array('element2' => $create_link));

    $type_ids = array();
    $counter = 0;
    $count = count($types);

    if (!$count){
      $form->addElement('Hidden', 'tip', array(
        'ignore' => true,
        'order' => 1
      ));
      $form->tip->addDecorator('TypeTip', array(
        'text' => $this->view->translate('RATE_REVIEW_TYPEEDITFORM_TIP')
      ));
    }

    foreach ($types as $type){

      $type_id = $type->getIdentity();
      $name = $type_key.$type_id;

      $form->addElement('Text', $name, array('value' => $type->label));

      $elements = "<div class='options'>";
      if ($counter != 0){
        $title = $this->view->translate('RATE_REVIEW_TYPEEDITFORM_MOVEUP');
        $img = $this->view->htmlImage($this->view->baseUrl().'/application/modules/Rate/externals/images/moveup.gif', $title);
        $link = $this->view->url(array('id' => $type_id, 'moveup' => 'true'));
        $elements .= $this->view->htmlLink($link, $img, array('title' => $title));
      }
      if ($counter != $count-1){
        $title = $this->view->translate('RATE_REVIEW_TYPEEDITFORM_MOVEDOWN');
        $img = $this->view->htmlImage($this->view->baseUrl().'/application/modules/Rate/externals/images/movedown.gif', $title);
        $link = $this->view->url(array('id' => $type_id, 'movedown' => 'true'));
        $elements .= $this->view->htmlLink($link, $img, array('title' => $title));
      }

      $title = $this->view->translate('RATE_REVIEW_TYPEEDITFORM_DELETE');
      $img = $this->view->htmlImage($this->view->baseUrl().'/application/modules/Rate/externals/images/delete.png', $title);
      $link = $this->view->url(array('id' => $type_id, 'delete' => 'true'));
      // if demoadmin
      if (Engine_Api::_()->user()->getViewer()->getIdentity() == 1250) {
        $link = $this->view->url(array('id' => $type_id));
      }
      $elements .= $this->view->htmlLink($link, $img, array(
        'title' => $title,
        'onClick' => 'return confirm("'.$this->view->translate('RATE_REVIEW_TYPE_DELETE').'");'
      ));

      $elements .= "</div>";

      $form->getElement($name)->addDecorator('TypeItem', array('element2' => $elements));

      $type_ids[] = $type_id;
      $counter++;

    }

    $this->view->form = $form;
    $this->view->count_types = $counter;

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())){
      $values = $form->getValues();
      foreach ($values as $key => $value){
        $key_id = substr($key, strlen($type_key), strlen($key)-strlen($type_key));
        if (!in_array($key_id, $type_ids)){ continue; }
        $tbl_type
            ->findRow($key_id)
            ->setFromArray(array('label' => $value))
            ->save();
      }
    }

    $row_id = $this->_getParam('id', false);
    if ($row_id){
      $row = $tbl_type->findRow($row_id);
      if ($row){
        $category_id = $row->category_id;
        if ($this->_getParam('delete', false)){ $row->delete(); }
        if ($this->_getParam('moveup', false)){ $row->changeOrder(true); }
        if ($this->_getParam('movedown', false)){ $row->changeOrder(false); }
        $this->_redirectCustom($this->view->url(array(
          'module' => 'rate',
          'controller' => 'types',
          'action' => 'index'
        ), 'admin_default', true) . '?category_id='.$category_id);
        return;
      }
    }
  }

  public function createAction(){

    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')){
      $this->_forward('notfound', 'error', 'core');
      return;
  	}

    $this->_helper->layout->setLayout('default-simple');
    $categories = Engine_Api::_()->getApi('core', 'rate')->getPageCategories();

    $this->view->categories = array();
    $category_ids = array();
    foreach ($categories as $category){
      $category_ids[] = $category->option_id;
    }

    $category_id = $this->_getParam('category', false);
    if (!$category_id || !in_array($category_id, $category_ids)){
      $category_id = $category_ids[0];
    }
    $this->view->category_id = $category_id;

    $this->view->form = $form = new Rate_Form_Review_TypeCreate;
    $form->getElement('category')->setValue($category_id);

    $this->view->redirect = false;

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())){

      $tbl_type = Engine_Api::_()->getDbTable('types', 'rate');

      $select = $tbl_type->select()
          ->from($tbl_type->info('name'), 'MAX(`order`)')
          ->where('category_id = ?', $category_id);
      $max = (int)$tbl_type->getAdapter()->fetchOne($select);

      $row =  array(
        'category_id' => $category_id,
        'label' => $form->getValue('label'),
        'order' => $max+1
      );
      $tbl_type->createRow($row)->save();

      $this->view->redirect = true;

    }

  }

}