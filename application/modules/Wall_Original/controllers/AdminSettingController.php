<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_AdminSettingController extends Core_Controller_Action_Admin
{
	public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('wall_admin_main', array(), 'wall_admin_main_setting');
  }

  public function indexAction()
  {

    $this->view->form = $form = new Engine_Form();

    $content = new Wall_Form_Admin_Content();
    $form->addSubForm($content, 'content');

    $list = new Wall_Form_Admin_List();
    $form->addSubForm($list, 'list');

    $list = new Wall_Form_Admin_Privacy();
    $form->addSubForm($list, 'privacy');

    $list = new Wall_Form_Admin_Tabs();
    $form->addSubForm($list, 'tabs');

    $list = new Wall_Form_Admin_Composers();
    $form->addSubForm($list, 'composers');

    $facebook = new Wall_Form_Service_Facebook();
    $form->addSubForm($facebook, 'facebook');

    $twitter = new Wall_Form_Service_Twitter();
    $form->addSubForm($twitter, 'twitter');

    $linkedin = new Wall_Form_Service_Linkedin();
    $form->addSubForm($linkedin, 'linkedin');

    foreach ($form->getSubForms() as $subform){
      $subform->getDecorator('Description')->setOption('escape', false);
      $subform->applyDefaults();
    }

    $form->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'ignore' => true,
      'type' => 'submit'
    ));

    $form->populate(array());

    if (!$this->getRequest()->isPost()){
      return ;
    }

    if (!$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    foreach ($form->getSubForms() as $subform){
      $subform->saveValues();
    }

    $values = $form->getValues();

    $form->populate($values);
    $form->addNotice('Your changes have been saved.');

  }



}