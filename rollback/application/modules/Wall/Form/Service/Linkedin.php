<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Linkedin.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Form_Service_Linkedin extends Wall_Form_Subform
{

  public function init()
  {
    $this->setTitle('WALL_LINKEDIN_FORM_TITLE');

    $description = $this->getTranslator()->translate('WALL_LINKEDIN_FORM_DESCRIPTION');
    $description = vsprintf($description, array(
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => 'wall',
          'controller' => 'linkedin',
          'action' => 'index'
        ), 'default', true),
    ));
    $this->setDescription($description);


    $this->addElement('Checkbox', 'enabled', array(
      'description' => 'WALL_LINKEDIN_FORM_ENABLED',
      'onclick' => '$("linkedin-consumerkey").getParents("#consumerkey-wrapper, #consumersecret-wrapper").set("style", "opacity:" + (this.checked) ? 1 : 0.7);$("linkedin-consumerkey").set("disabled", !this.checked);$("linkedin-consumersecret").set("disabled", !this.checked);'
    ));

    $this->addElement('Text', 'consumerkey', array(
     'label' => 'WALL_LINKEDIN_CLIENT_ID',
     'description' => 'WALL_LINKEDIN_CLIENT_ID_DESCRIPTION',
    ));

    $this->addElement('Text', 'consumersecret', array(
      'label' => 'WALL_LINKEDIN_CLIENT_SECRET',
      'description' => 'WALL_LINKEDIN_CLIENT_SECRET_DESCRIPTION'
    ));

  }


  public function attachVisuals($values)
  {
    if (!empty($values['enabled'])){
      $this->consumerkey->getDecorator('HtmlTag2')->setOption('style', 'opacity:1');
      $this->consumersecret->getDecorator('HtmlTag2')->setOption('style', 'opacity:1');
      $this->consumerkey->setOptions(array('disabled' => null));
      $this->consumersecret->setOptions(array('disabled' => null));
    } else  {
      $this->consumerkey->getDecorator('HtmlTag2')->setOption('style', 'opacity:0.7');
      $this->consumersecret->getDecorator('HtmlTag2')->setOption('style', 'opacity:0.7');
      $this->consumerkey->setOptions(array('disabled' => 'disabled'));
      $this->consumersecret->setOptions(array('disabled' => 'disabled'));
    }


    return parent::setDefaults($values);
  }

  public function applyDefaults()
  {
    $setting = Engine_Api::_()->getDbTable('settings', 'core');

    $this->getElement('enabled')->setValue($setting->getSetting('wall.service.linkedin.enabled'));
    $this->getElement('consumerkey')->setValue($setting->getSetting('wall.service.linkedin.consumerkey'));
    $this->getElement('consumersecret')->setValue($setting->getSetting('wall.service.linkedin.consumersecret'));

    $values = $this->getValues();
    $this->attachVisuals($values['linkedin']);

  }

  public function saveValues()
  {
    $setting = Engine_Api::_()->getDbTable('settings', 'core');

    $setting->setSetting('wall.service.linkedin.enabled', (string) $this->getElement('enabled')->getValue());
    $setting->setSetting('wall.service.linkedin.consumerkey', (string) $this->getElement('consumerkey')->getValue());
    $setting->setSetting('wall.service.linkedin.consumersecret', (string) $this->getElement('consumersecret')->getValue());

    $values = $this->getValues();
    $this->attachVisuals($values['linkedin']);

  }


}