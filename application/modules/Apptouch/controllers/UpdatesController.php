<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SettingsController.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_UpdatesController extends Apptouch_Controller_Action_Bridge
{
	public function settingsInit()
	{
		$this->_helper->requireUser();
	}
	
  public function settingsIndexAction()
  {
  	if(
  			!$this->_helper->api()->user()->getViewer()->getIdentity()
  			||
  		 	!$this->_helper->requireAuth()->setAuthParams('updates', null, 'use')->isValid()
  		)
  		{
        $this->renderContent();
  			return;
  		}

    $user = Engine_Api::_()->user()->getViewer();
    Engine_Api::_()->core()->setSubject($user);

    $navigation = $this->_helper->api()
      ->getApi('menus', 'apptouch')
      ->getNavigation('user_settings', array());
    $this->add($this->component()->html("<h2>".$this->view->translate('UPDATES_My Settings')."</h2>"));
    $this->add($this->component()->navigation($navigation));

  	$form = new Updates_Form_Subscribe();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
 			$user->updates_subscribed = $values['subscribe'];
 			
      if ($user->save())
      {
      	$form->addNotice('UPDATES_Changes have been successfully saved.');
        $this->add($this->component()->form($form))->renderContent();
        return;
      }
      else
      {
      	$form->addError('UPDATES_An error has been occurred while subscribing!!!');
        $this->add($this->component()->form($form))->renderContent();
        return;
      }

    }
    
    $form->populate(array('subscribe'=>$user->updates_subscribed));
        $this->add($this->component()->form($form));
        $this->renderContent();
  }
}