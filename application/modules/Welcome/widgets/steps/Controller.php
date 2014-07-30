<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Welcome_Widget_StepsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $slideshow_id = $this->_getParam( 'slideshow_id', 1 );
    $slideshow = Engine_Api::_()->getItem( 'welcome_slideshow', $slideshow_id );

    $paginator = Engine_Api::_()->welcome()->getWelcomePaginator( array('slideshow_id' => $slideshow_id ) );

    if (count($paginator) == 0) {
      return $this->setNoRender();
    }

    $this->getElement()->setTitle('');

    $this->view->slideshow_id = $slideshow_id;
    if( $slideshow->effect ){
      $this->view->animation = $slideshow->effect;
    }else{
      $this->view->animation = Engine_Api::_()->getApi('settings', 'core')->getSetting('welcome.animation', 'curtain');
    }







  }



}