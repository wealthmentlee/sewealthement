<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Facebook.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Form_Service_Facebook extends Wall_Form_Subform
{

  public function init()
  {
    $this->setTitle('WALL_FACEBOOK_FORM_TITLE');
    $this->setDescription('WALL_FACEBOOK_FORM_DESCRIPTION');

    $this->addElement('Checkbox', 'enabled', array(
      'description' => 'WALL_FACEBOOK_FORM_ENABLED',
      'onclick' => '$$("#clientid-wrapper, #clientsecret-wrapper").set("style", "opacity:" + (this.checked) ? 1 : 0.7);$$("#clientid-wrapper input, #clientsecret-wrapper input").set("disabled", !this.checked);',
    ));

    $this->addElement('Text', 'clientid', array(
     'label' => 'WALL_FACEBOOK_CLIENT_ID',
     'description' => 'WALL_FACEBOOK_CLIENT_ID_DESCRIPTION',
    ));

    $this->addElement('Text', 'clientsecret', array(
      'label' => 'WALL_FACEBOOK_CLIENT_SECRET',
      'description' => 'WALL_FACEBOOK_CLIENT_SECRET_DESCRIPTION'
    ));


  }

  public function attachVisuals($values)
  {
    if (!empty($values['enabled'])){
      $this->clientid->getDecorator('HtmlTag2')->setOption('style', 'opacity:1');
      $this->clientsecret->getDecorator('HtmlTag2')->setOption('style', 'opacity:1');
      $this->clientid->setOptions(array('disabled' => null));
      $this->clientsecret->setOptions(array('disabled' => null));
    } else  {
      $this->clientid->getDecorator('HtmlTag2')->setOption('style', 'opacity:0.7');
      $this->clientsecret->getDecorator('HtmlTag2')->setOption('style', 'opacity:0.7');
      $this->clientid->setOptions(array('disabled' => false));
      $this->clientsecret->setOptions(array('disabled' => false));
    }
  }


  public function applyDefaults()
  {
    $setting = Engine_Api::_()->getDbTable('settings', 'core');

    $this->getElement('enabled')->setValue($setting->getSetting('wall.service.facebook.enabled'));
    $this->getElement('clientid')->setValue($setting->getSetting('wall.service.facebook.clientid'));
    $this->getElement('clientsecret')->setValue($setting->getSetting('wall.service.facebook.clientsecret'));

    $values = $this->getValues();
    $this->attachVisuals($values['facebook']);

  }

  public function saveValues()
  {
    $setting = Engine_Api::_()->getDbTable('settings', 'core');

    $setting->setSetting('wall.service.facebook.enabled', (string) $this->getElement('enabled')->getValue());
    $setting->setSetting('wall.service.facebook.clientid', (string) $this->getElement('clientid')->getValue());
    $setting->setSetting('wall.service.facebook.clientsecret', (string) $this->getElement('clientsecret')->getValue());

    $values = $this->getValues();
    $this->attachVisuals($values['facebook']);

  }


}