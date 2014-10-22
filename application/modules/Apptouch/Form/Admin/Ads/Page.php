<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     Jung
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Apptouch_Form_Admin_Ads_Page extends Engine_Form
{
  protected $_currentPage;
  public function __construct($currentPage)
  {
    $this->_currentPage = $currentPage;
    parent::__construct();
  }

  public function init()
  {
    // Set form attributes
    $view = Zend_Registry::get('Zend_View');
    $this->setTitle($view->translate('APPTOUCH_Add Ad Campaign for: ') . $this->_currentPage->displayname);
    if(!$this->_currentPage->enable_ad)
      $this->setAttrib('class', 'ads-not-enabled');
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()) . '?page=' . Zend_Controller_Front::getInstance()->getRequest()->getParam('page'));

    $table = Engine_Api::_()->getDbtable('adcampaigns', 'core');
    $select = $table->select()
      ->order('adcampaign_id DESC');
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($paginator->getTotalItemCount());

    $adCampaign = array('' => '');
    foreach($paginator as $campaign){
      $adCampaign[$campaign->getIdentity()] = $campaign->name;
    }

    $this->addElement('Checkbox', 'enable_ad', array(
      'label' => 'APPTOUCH_Enable ads for this page',
      'value' => 0,
      'onclick' => 'setAdEnable(this)'
    ));
    $this->addElement('Select', 'adcampaign_id', array(
      'label' => 'Ad Campaign',
      'onchange' => 'adCampaignSelected(this)',
      'allowEmpty' => false,
      'multiOptions' => $adCampaign,
      'value' => 0
    ));
    $this->addElement('Dummy', 'add_new_adv', array(
      'content' => '<a target="_blank" class="create_new_adv buttonlink admin_ads_create" href='.$view->url(array('ad_campaign' => 0, 'action' => 'create', 'controller' => 'ads', 'module' => 'apptouch'), 'admin_default', true).'>' . $view->translate('APPTOUCH_Add New Ad') . '</a>'.
        '<a target="_blank" class="create_new_camp buttonlink admin_ads_create" href='.$view->url(array('action' => 'create', 'controller' => 'ads', 'module' => 'core'), 'admin_default', true).'>' . $view->translate('Create New Campaign') . '</a>'
    ));
    $this->addElement('Dummy', 'advs', array(
      'label' => 'APPTOUCH_Banners',
      'content' => '<div class="loader adv-banner"></div>'
    ));

    // Title

    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}