<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WidgetController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Event_WidgetController extends Core_Controller_Action_Standard
{
  public function profileInfoAction() 
  {
    // Don't render this if not authorized
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() )
      return $this->_helper->viewRenderer->setNoRender(true);
  }

  public function profileRsvpAction()
  {

    $this->view->form = new Event_Form_Rsvp();
    $event = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$event->membership()->isMember($viewer, true))
    {
      return;
    }
    $row = $event->membership()->getRow($viewer);
    $this->view->viewer_id = $viewer->getIdentity();
    if ($row) {
      $this->view->rsvp = $row->rsvp;
    }
    else
    {
      return $this->_helper->viewRenderer->setNoRender(true);
    }
    if ($this->getRequest()->isPost())
    {
      $option_id = $this->getRequest()->getParam('option_id');

      $row->rsvp = $option_id;
      $row->save();
    }
  }

  public function requestEventAction()
  {
    $path = Engine_Api::_()->mobile()->getScriptPath('event');
    $this->view->addScriptPath($path);

    $this->view->notification = $notification = $this->_getParam('notification');
  }
}