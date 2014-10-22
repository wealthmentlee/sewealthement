<?php

class Apptouch_HecoreController extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->params = $params = (array)$this->_getParam('params');
    $this->callback = $callback = $this->_getParam('c');
    $this->module = $module = $this->_getParam('m');
    $this->list = $list = $this->_getParam('l');
    $this->not_logged_in = $not_logged_in = $this->_getParam('nli', 0);
    $this->p = $p = (int)$this->_getParam('p', 1);
    $this->contacts = $contacts = (array)$this->_getParam('contacts', array());
    $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->disabled_label = !empty($this->params['disabled_label']) ? $this->params['disabled_label'] : "";
    $this->ipp = $ipp = (isset($params['ipp'])) ? $params['ipp'] : (int)$this->_getParam('ipp', 30);
    $this->title = $title = $this->_getParam('t', !empty($params['object_type']) ? $this->view->translate('suggest_popup_title_'.$params['object_type'].' %s', Engine_Api::_()->getItem($params['object_type'], $params['object_id'])->getTitle()) : '');

    // Params
    $keyword = $this->_getParam('search');
    $list_type = $this->_getParam('list_type');

    if ($keyword) {
      $params['keyword'] = $keyword;
    }

    if ($list_type) {
      $params['list_type'] = $list_type;
    }

    $this->list_type = (isset($params['list_type'])) ? $params['list_type'] : 'all';

    // User logged in or not
    if (!$not_logged_in && !$viewer->getIdentity()) {
      $this->view->error = 1;
      $this->view->message = $this->view->translate("hecore_You should be logged in to view this page.");
      return ;
    }

    $this->module = $module = trim(strtolower($module));

    // Sanity checks
    $table = Engine_Api::_()->getDbTable('modules', 'core');
    $select = $table->select();
    $select
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

    // Get Items and check return result
    $this->items = $items = Engine_Api::_()->$module()->$list($params);
    if ($items === null) {
      $this->view->error = 4;
      $this->view->message = "Method returned null.";
      return ;
    }

    if ($items instanceof Zend_Paginator) {
      $this->total = $items->getTotalItemCount();
      $this->current_count = $items->getCurrentItemCount();
    }

    $listDisabled = $list . 'Disabled';
    if (method_exists($api, $listDisabled)) {
      $disabledItems = Engine_Api::_()->$module()->$listDisabled($params);
      $this->disabledItems = $disabledItems;
    } else {
      $this->disabledItems = array();
    }
    $listChecked = $list . 'Checked';
    if (method_exists($api, $listChecked)) {
      $checkedItems = Engine_Api::_()->$module()->$listChecked($params);
      $this->checkedItems = (array)$checkedItems;
    } else {
      $this->checkedItems = array();
    }

    $this->checkedItems = array_merge($this->checkedItems, $contacts);

    $listPotential = $list . 'Potential';
    if (method_exists($api, $listPotential) && isset($params['potential']) && $params['potential']) {
      $potentialItems = Engine_Api::_()->$module()->$listPotential($params);
      $this->potentialItems = $potentialItems;
    } else {
      $this->potentialItems = array();
    }

  }

  public function indexListAction()
  {


    $module = $this->module;
    $list = $this->_getParam('l');
    $listname = strtolower($module) . '_' . strtolower($list);
    $params = (array)$this->_getParam('params');

    // don't use to secure
    //$title = $this->_getParam('t', '');
    //$callback = $this->_getParam('c');


    // get title
    $translate_key = 'APPTOUCH_' . strtoupper($listname);
    $translate = Zend_Registry::get('Zend_Translate');
    if ($translate->isTranslated($translate_key)) {
      $title = $this->view->translate($translate_key);
    } else {
      $title = $this->view->translate('APPTOUCH_HECORE_LIST');
    }


    $not_logged_in = $this->_getParam('nli', 0);
    $p = $this->_getParam('p', $this->_getParam('page', 1));
    $contacts = (array)$this->_getParam('contacts', array());
    $viewer = Engine_Api::_()->user()->getViewer();
    $disabled_label = !empty($params['disabled_label']) ? $params['disabled_label'] : "";
    $ipp = (isset($params['ipp'])) ? $params['ipp'] : (int)$this->_getParam('ipp', 30);

    $keyword = $this->_getParam('keyword', $this->_getParam('search'));
    $list_type = $this->_getParam('list_type');

    if ($keyword) {
      $params['keyword'] = $keyword;
    }

    if ($list_type) {
      $params['list_type'] = $list_type;
    }

    $list_type = (isset($params['list_type'])) ? $params['list_type'] : 'all';

    if (!$not_logged_in && !$viewer->getIdentity()) {
      $this->view->error = 1;
      $this->view->message = $this->view->translate("hecore_You should be logged in to view this page.");
      $this->add($this->component()->html($this->view->message));
      return;
    }

    $module = trim(strtolower($module));

    // Sanity checks
    $table = Engine_Api::_()->getDbTable('modules', 'core');
    $select = $table->select();
    $select
      ->where("name = ?", $module);

    if (!$table->getAdapter()->fetchOne($select)) {
      $this->view->error = 2;
      $this->view->message = "Module does not exists.";
      $this
        ->setFormat('html')
        ->add($this->component()->html($this->view->message))
        ->renderContent();

      return;
    }

    $api = Engine_Api::_()->getApi('core', $module);
    if (!is_callable(array($api, $list))) {
      $this->view->error = 3;
      $this->view->message = "Method does not exists in module's API.";
      $this
        ->setFormat('html')
        ->add($this->component()->html($this->view->message))
        ->renderContent();
      return;
    }

    $api = Engine_Api::_()->$module();
    if (!method_exists($api, $list)) {
      $this->view->error = 5;
      $this->view->message = "Method '$list' does not exists.";
      $this
        ->setFormat('html')
        ->add($this->component()->html($this->view->message))
        ->renderContent();
      return;
    }

    // Get Items and check return result
    $items = Engine_Api::_()->$module()->$list($params);
    if ($items === null) {
      $this->view->error = 4;
      $this->view->message = "Method returned null.";
      $this
        ->setFormat('html')
        ->add($this->component()->html($this->view->message))
        ->renderContent();
      return;
    }

    if ($items instanceof Zend_Paginator) {
      $total = $items->getTotalItemCount();
      $current_count = $items->getCurrentItemCount();
    }

    $listDisabled = $list . 'Disabled';
    if (method_exists($api, $listDisabled)) {
      $disabledItems = Engine_Api::_()->$module()->$listDisabled($params);
    } else {
      $disabledItems = array();
    }

    $listChecked = $list . 'Checked';
    if (method_exists($api, $listChecked)) {
      $checkedItems = Engine_Api::_()->$module()->$listChecked($params);
      $checkedItems = (array)$checkedItems;
    } else {
      $checkedItems = array();
    }

    $checkedItems = array_merge($checkedItems, $contacts);

    $listPotential = $list . 'Potential';
    if (method_exists($api, $listPotential) && isset($params['potential']) && $params['potential']) {
      $potentialItems = Engine_Api::_()->$module()->$listPotential($params);
    } else {
      $potentialItems = array();
    }


    if (!empty($items)) {
      $items->setItemCountPerPage(9);
      $items->setCurrentPageNumber($p);
    }

    $formFilter = new Apptouch_Form_Search();
    $defaultValues = $formFilter->getValues();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $values = array();
    }


    $translate_key = 'APPTOUCH_TITLE1_' . strtoupper($listname);
    $translate = Zend_Registry::get('Zend_Translate');
    if ($translate->isTranslated($translate_key)) {
      $title1 = $this->view->translate($translate_key);
    } else {
      $title1 = $this->view->translate('Everyone');
    }
    $translate_key = 'APPTOUCH_TITLE2_' . strtoupper($listname);
    $translate = Zend_Registry::get('Zend_Translate');
    if ($translate->isTranslated($translate_key)) {
      $title2 = $this->view->translate($translate_key);
    } else {
      $title2 = $this->view->translate('Mutual');
    }


    $title1_href = $this->view->url(array(
      'module' => 'hecore',
      'controller' => 'index',
      'action' => 'list',
    ), 'default', true);
    $title1_href .= '?' . http_build_query(array(
      'm' => $module,
      'l' => $list,
      'params' => array_merge($params, array('list_type' => 'all')),
    ));

    $title2_href = $this->view->url(array(
      'module' => 'hecore',
      'controller' => 'index',
      'action' => 'list'
    ), 'default', true);
    $title2_href .= '?' . http_build_query(array(
      'm' => $module,
      'l' => $list,
      'params' => array_merge($params, array('list_type' => 'mutual')),
    ));

    $navigation = new Zend_Navigation(array(
      'pages' =>
      array(
        'label' => $title1,
        'uri' => $title1_href,
        'data_attrs' => array('data-rel' => 'dialog'),
        'active' => ($list_type != 'mutual')
      ),
      array(
        'label' => $title2,
        'uri' => $title2_href,
        'data_attrs' => array('data-rel' => 'dialog'),
        'active' => ($list_type == 'mutual')
      )
    ));

    $formFilter->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
      'module' => 'hecore',
      'controller' => 'index',
      'action' => 'list',
    ), 'default').'?m='.$this->module.'&l='.$this->list.'&params[item_type]='.$this->params['item_type'].'&params[item_id]='.$this->params['item_id']);

    $this->setFormat('browse')
      ->setPageTitle($title)
      ->add($this->component()->itemSearch($formFilter))
      ->add($this->component()->itemList($items, "listItemData", array('listPaginator' => true,)))
//      ->add($this->component()->paginator($items))
      ->add($this->component()->navigation($navigation))
      ->renderContent();
  }

  public function indexContactsAction()
  {
    if (isset($this->items) && ($this->items instanceof Zend_Paginator)) {
      $this->items->setItemCountPerPage($this->ipp);
    }

    if (isset($this->p) && ($this->items instanceof Zend_Paginator)) {
      $this->items->setCurrentPageNumber($this->p);
    }

    $need_pagination = (bool)($this->p < count($this->items));

    if ( isset($this->params['scriptpath']) ) {
      if (null !== ($scriptpath = $this->params['scriptpath'])){
        $this->view->setScriptPath($scriptpath);
      }
    }

    $controlGroup = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal', 'style' => 'text-align: center'));

    $controlGroup->append($this->dom()->new_('a',
      array(
        'data-role' => 'button',
        'data-icon' => 'check',
        'class' => 'showAll',
        'href' => null), $this->view->translate('All')))

    ->append($this->dom()->new_('a',
      array(
        'data-icon' => 'check',
        'data-role' => 'button',
        'class' => 'showSelected',
        'href' => null), $this->view->translate('Selected')));
    $contacts = array();
    $contactsPhotos = array();
    if(is_array($this->items) && !($this->items instanceof Zend_Paginator))
    {
      $items = (@$this->items['potential'] instanceof Zend_Paginator) ? $this->items['potential'] : array();
      if(!count($items))
        $items = (@$this->items['all'] instanceof Zend_Paginator) ? $this->items['all'] : array();

    } else {
      $items = $this->items;
    }

    foreach($items as $item){
      if((bool)in_array($item->getIdentity(), $this->disabledItems))
        continue;
      $contacts[$item->getIdentity()] = $item->getTitle();
      $contactsPhotos[$item->getIdentity()] = $item->getPhotoUrl('thumb.icon');
    }
    $form = new Engine_Form();
    if(!empty($contacts)){
      $form->addAttribs(array('class'=>'hecontacts-form'))->setMethod('post')->setAction($this->_getParam('c'))->addElement('MultiCheckbox', 'uids', array(
            'multiOptions' => $contacts,
          ))
        ->addElement('Checkbox', 'all', array(
          'label' => 'Select All',
          'checkedValue' => 'all',
          'uncheckedValue' => '',
        ))

        ->addElement('Button', 'send', array(
              'label' => 'Send',
              'type' => 'submit',
              'ignore' => true,
              'decorators' => array(
                'ViewHelper',
              ),
            ));
      $frm = $this->getSearchForm();
      $frm->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble($this->_getAllParams()));
      $this
      ->addPageInfo('hecontacts', array(
        'photos' => $contactsPhotos
      ))
      ->add($this->component()->itemSearch($frm))
      ->add($this->component()->html('<br/>'))
      ->add($this->component()->html($controlGroup))
      ->add($this->component()->form($form));
      if($need_pagination)
        $this->add($this->component()->paginator($items));
    } else {
      $this->add($this->component()->tip($this->view->translate('There is no contacts.'), $this->view->translate('No contacts')));
    }
    $this
      ->setPageTitle($this->title);
//      ->add($this->component()->itemList($this->items, 'contactsListItem'));
    $this->renderContent();
//    $this->_helper->layout->disableLayout();
  }

  public function listItemData(Core_Model_Item_Abstract $item)
  {
    $customize_fields = array(
      'creation_date' => null,
      'descriptions' => array()
    );
    return $customize_fields;
  }
  public function contactsListItem(Core_Model_Item_Abstract $item)
  {
    $customize_fields = array(
      'creation_date' => null,
      'href' => null,
      'descriptions' => array(),
      'attrsLi' => array(
        'data-icon' => false
      ),
      'attrsA' => array()
    );
    return $customize_fields;
  }


}
