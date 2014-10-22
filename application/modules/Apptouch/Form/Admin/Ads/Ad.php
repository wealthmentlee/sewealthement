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
class Apptouch_Form_Admin_Ads_Ad extends Engine_Form
{
  public function init()
  {
    $view = Zend_Registry::get('Zend_View');
    // Set form attributes
    $this->setTitle('Create Advertisement');
    $this->setDescription('Follow this guide to design and create a new advertisement.');
    $this->setAttrib('id', 'form-upload');
    $this->setAttrib('class', 'adc-not-selected');
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $table = Engine_Api::_()->getDbtable('adcampaigns', 'core');
    $select = $table->select()
      ->order('adcampaign_id DESC');
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($paginator->getTotalItemCount());
    $adCampaign = array();
    if(!Zend_Controller_Front::getInstance()->getRequest()->getParam('id'))
      $adCampaign = array('' => '');
    foreach($paginator as $campaign){
      $adCampaign[$campaign->getIdentity()] = $campaign->name;
    }

    $this->addElement('Select', 'ad_campaign', array(
      'label' => 'Ad Campaign',
      'required' => true,
      'onchange' => 'adCampaignSelected(this)',
      'allowEmpty' => false,
      'multiOptions' => $adCampaign,
      'value' => 0
    ));
    $this->addElement('Dummy', 'add_new_camp', array(
      'content' => '<a target="_blank" class="buttonlink admin_ads_create" href='.$view->url(array('action' => 'create', 'controller' => 'ads', 'module' => 'core'), 'admin_default', true).'>' . $view->translate('Create New Campaign') . '</a>'
    ));

    // Title
    $this->addElement('Text', 'name', array(
      'allowEmpty' => false,
      'placeholder' => 'Advertisement Name',
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));
    $this->addElement('Button', 'preview_html', array(
      'label' => 'Play',
      'ignore' => true,
      'onclick'=>'javascript:preview();',
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Checkbox', 'fixed', array(
      'label' => 'APPTOUCH_Set Fixed',
      'value' => 1,
      'onclick' => 'setFixed(this)'
    ));
    $this->addElement('Radio', 'position', array(
      'id'=>'position',
      'label' => 'APPTOUCH_Advertisement Position',
      'onchange' => "setPos(this)",
      'multiOptions' => array("0"=>"Top", "1"=>"Bottom"),
      'value' => 0,
    ));
    $this->addElement('Radio', 'anim_type', array(
      'id'=>'anim_type',
      'label' => 'APPTOUCH_Animation Type',
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => array("0"=>"Slide", "1"=>"Pop", "2"=>"Fade"),
      'value' => 0,
    ));
    $this->addElement('Text', 'anim_delay', array(
      'allowEmpty' => false,
      'label' => 'APPTOUCH_Animation Delay (sec.)',
      'required' => true,
      'allowEmpty' => false,
      'value' => 10,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 3)),
      ),
      'maxlength' => 3,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));
    $this->addElement('Text', 'anim_duration', array(
      'allowEmpty' => false,
      'label' => 'APPTOUCH_Animation Duration (sec.)',
      'required' => true,
      'value' => .5,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 3)),
      ),
      'maxlength' => 3,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));
    $this->addElement('Radio', 'media_type', array(
      'id'=>'mediatype',
      'label' => 'Advertisement Media',
      'onchange' => "updateTextFields(this)",
      'multiOptions' => array("0"=>"Upload Banner Image", "1"=>"Insert Banner HTML"),
    ));

//    $this->media->getDecorator('Description')->setOption('placement', 'append');


    // Init file

    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
                ->addDecorator('FormFancyUpload')
                ->addDecorator('viewScript', array(
                  'viewScript' => 'admin/_FancyUpload.tpl',
                  'placement'  => '',
                  ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $fancyUpload->setLabel("Upload Banner Image");
    $this->addElement($fancyUpload);
    $this->addElement('Hidden', 'photo_id');

    $this->addDisplayGroup(array('file'), 'upload_image');
    $upload_image_group = $this->getDisplayGroup('upload_image');

    $this->addElement('Textarea', 'html_code', array(
      'placeholder' => 'HTML Code',
    ));
    // Buttons
    $this->addDisplayGroup(array('html_code'), 'html_field');
    $html_code_group = $this->getDisplayGroup('html_code');

    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}