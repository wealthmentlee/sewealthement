<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TabsController.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Welcome_TabsController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $slideshow_id = $this->_getParam( 'slideshow_id', 1 );

    $this->view->paginator = $paginator = Engine_Api::_()->welcome()->getWelcomePaginator( array('slideshow_id' => $slideshow_id) );


    $slideshow = Engine_Api::_()->getItem('welcome_slideshow',$slideshow_id);

    if( !$slideshow->width || !$slideshow->height ){
      $this->view->width = $width =  Engine_Api::_()->getApi('settings', 'core')->getSetting('welcome.width', 900);
      $this->view->height = Engine_Api::_()->getApi('settings', 'core')->getSetting('welcome.height', 150) - 62;
    }else{
      $this->view->width = $width = $slideshow->width;
      $this->view->height = $slideshow->height - 62;
    }

    $this->view->wmisc = $wmisc = 40;
    $this->view->settings = $slideshow->getSettings();
    
    $wmisc = ( $wmisc / ($paginator->getCurrentItemCount()) ) * ($paginator->getCurrentItemCount()); 
    $tab_width = (int)(($width - $wmisc) / ($paginator->getCurrentItemCount()));
    $this->view->width = $tab_width * ($paginator->getCurrentItemCount()) + $wmisc;

    // Html params
    $this->view->container = 'FC_container_' . $slideshow_id;
    $this->view->content = 'FC_content_' . $slideshow_id;
    $this->view->element = 'content_' . $slideshow_id;
    $this->view->tabs_id = 'FC_tabs_' . $slideshow_id;
    $this->view->tabs_class = 'tab_' . $slideshow_id;
    $this->view->selected = 'selected_' + $slideshow_id;
  }
}