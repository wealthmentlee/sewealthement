<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 23.07.13
 * Time: 15:29
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Form_Admin_Settings_FaceDetectionApi extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('APPTOUCH_Sky Biometry Face Detection API')
      ->setDescription('APPTOUCH_FACE_DETECTION_DESCRIPTION');

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Text', 'key', array(
      'label' => 'APPTOUCH_Face Detection API Key',
      'value' => $settings->getSetting('apptouch.sky_biometry_key', ''),
      'style' => 'width: 300px'
    ));
    $this->addElement('Text', 'secret', array(
      'label' => 'APPTOUCH_Face Detection API Secret',
      'value' => $settings->getSetting('apptouch.sky_biometry_secret', ''),
      'style' => 'width: 300px'
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
