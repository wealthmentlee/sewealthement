<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Widget_WelcomeController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {

    $this->getElement()->removeDecorator('title');

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer && !$viewer->getIdentity()){
      return $this->setNoRender();
    }


  }

}