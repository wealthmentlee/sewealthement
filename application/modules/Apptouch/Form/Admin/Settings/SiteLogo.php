<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 02.04.12
 * Time: 12:01
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Form_Admin_Settings_SiteLogo extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('name', 'EditSiteLogo');

    $this->addElement('Image', 'logo', array(
      'label' => 'APPTOUCH_Site Logo',
      'ignore' => true,
      'decorators' => array(
        array('ViewScript',
          array(
            'viewScript' => 'admin/_formSiteLogo.tpl',
            'class' => 'form element',
          )
        )
      )
    ));
    Engine_Form::addDefaultDecorators($this->logo);

    $this->addElement('File', 'Filedata', array(
      'label' => 'APPTOUCH_Choose New Image',
      'destination' => APPLICATION_PATH . '/public/temporary/',
      'multiFile' => 1,
      'validators' => array(
        array('Count', false, 1),
        // array('Size', false, 612000),
        array('Extension', false, 'jpg,jpeg,png,gif'),
      ),
      'onchange' => 'javascript:if(window.uploadHomeScreenPhoto) uploadHomeScreenPhoto(); else this.form.submit()'
    ));


    $this->addElement('Button', 'done', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper'
      ),
    ));
  }
}
