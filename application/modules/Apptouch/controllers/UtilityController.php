<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: UtilityController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_UtilityController extends Core_Controller_Action_Standard
{
  public function refreshCaptchaAction()
  {
    $this->view->start = true;
    $this->view->json = false;
    $this->view->hascaptcha = false;
    if ($this->_getParam('format') != 'json')
      return;
    $class_name = $this->_getParam('class_name', 'Touch_Form_Signup_Account');

    $this->view->json = true;
    $this->view->class_name = $class_name;
    $this->view->class_exists = class_exists($class_name);
    $form = new $class_name();

    $captcha = $form->getElement('captcha')->getCaptcha();
    if (!$captcha)
      return;
    $this->view->hascaptcha = true;
    $this->view->id = $captcha->generate();
    $this->view->src = $captcha->getImgUrl() .
      $captcha->getId() .
      $captcha->getSuffix();
  }
}