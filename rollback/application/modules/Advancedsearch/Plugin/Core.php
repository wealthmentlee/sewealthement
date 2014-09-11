<?php
/***/
class Advancedsearch_Plugin_Core extends Zend_Controller_Plugin_Abstract {
  public function onRenderLayoutDefault($event)
  {
    $view = Zend_Registry::get('Zend_View');
    $types = Engine_Api::_()->advancedsearch()->getAvailableTypes();
    $asTypes = '';
    $db = Engine_Db_Table::getDefaultAdapter();
    $iconTable = Engine_Api::_()->getDbTable('icons', 'advancedsearch');
    $itemIcons = $iconTable->select()
      ->from(array('i' => $iconTable->info('name')), array('item','icon'));
    $itemIcons = $db->fetchPairs($itemIcons);
    $translate = Zend_Registry::get('Zend_Translate');
    foreach ($types as $type) {
            if (isset($itemIcons[$type])) $icon = $itemIcons[$type]; else $icon = 'icon-globe';
            $asTypes .= '<div class="as_type_global_container"><span data-type="' . $type . '"><i class=" ' . $icon . '"></i>' . $translate->translate(strtoupper('ITEM_TYPE_' . $type))  . '</span><div style="clear: both"></div></div>';    }
    $script = "var ASTypes = '$asTypes';";

    $view->headScript()
      ->appendScript($script);
    $view->headTranslate(array(
      'AS_Nothing found'
    ));
  }
}
