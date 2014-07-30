<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PopupController.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Welcome_PopupController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $slideshow_id = $this->_getParam( 'slideshow_id', 1 );

    $this->view->paginator = Engine_Api::_()->welcome()->getWelcomePaginator( array('slideshow_id' => $slideshow_id) );

    $slideshow = Engine_Api::_()->getItem('welcome_slideshow', $slideshow_id );

    if( !$slideshow->width || !$slideshow->height ){
      $this->view->width = Engine_Api::_()->getApi('settings', 'core')->getSetting('welcome.width', 900);
      $this->view->height = Engine_Api::_()->getApi('settings', 'core')->getSetting('welcome.height', 150);
    }else{
      $this->view->width = $slideshow->width;
      $this->view->height = $slideshow->height;
    }

    $this->view->settings = $slideshow->getSettings();
    $this->view->width -= 40 * $this->view->paginator->getCurrentItemCount();

    // Html attributes
    $this->view->containerId = 'popup_slides_' . $slideshow_id;
  }
}