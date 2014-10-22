<?php
/**
 * SocialEngine
 *
 * @category   Application_Apptouch
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: MenuItemEdit.php 15.11.12 12:21 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Apptouch
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Apptouch_Form_Admin_MenuItemEdit extends Apptouch_Form_Admin_MenuItemCreate
{
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Menu Item');
    $this->setAttrib('class', 'apptouch_edit_menuitems global_form_popup');
    $this->submit->setLabel('Edit Menu Item');
  }
}