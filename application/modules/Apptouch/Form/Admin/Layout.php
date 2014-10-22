<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Layout.php 2012-12-03 11:18:13 ulan t $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_Form_Admin_Layout extends Engine_Form
{
  protected $_currentPage;

  public function getCurrentPage()
  {
    return $this->_currentPage;
  }

  public function setCurrentPage($item)
  {
    $this->_currentPage = $item;
    return $this;
  }

  public function __construct($currentPage)
  {
    $this->_currentPage = $currentPage;
    parent::__construct();
  }

  public function init()
  {
    $this->setTitle($this->_currentPage->displayname)
      ->setDescription('APPTOUCH_Layout_Editor_Desc')
      ->setAttrib('id', 'apptouch_layout_form')
      ;

    $content = Engine_Api::_()->getDbTable('content', 'apptouch')->getContent($this->_currentPage->getIdentity());

    foreach( $content as $item ) {
      $this->addElement('Checkbox', $item->component_name . '_' . $item->content_id, array(
        'label' => vsprintf($this->getTranslator()->translate('APPTOUCH_COMPONENT_NAME'), array($item->component_name)),
        'description' => vsprintf($this->getTranslator()->translate('APPTOUCH_COMPONENT_DESC'), array($item->component_name)),
        'checkedValue' => $item->content_id,
        'uncheckedValue' => -$item->content_id,
        'value' => $item->enabled ? $item->content_id : -$item->content_id,
      ));
    }

    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper'
      )
    ));
  }
}