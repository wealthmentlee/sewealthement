<?php

class Pinfeed_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.portal', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

    if( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      $subject = Engine_Api::_()->core()->getSubject();
      if( !$subject->authorization()->isAllowed($viewer, 'view') || !in_array($subject->getType(), Engine_Api::_()->wall()->getSupportedItems())) {
        return $this->setNoRender();
      }
    }
    
    // Render
    $this->_helper->content
      //->setNoRender()
      ->setEnabled();
  }


}

