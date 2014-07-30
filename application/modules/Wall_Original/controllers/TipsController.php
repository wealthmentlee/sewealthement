<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TipsController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_TipsController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {
	try {
		$this->view->subject = Engine_Api::_()->core()->getSubject();
	} catch (Exception $e){ 
		return ;
	}
  }

  public function likeAction()
  {
    $is_unlike = $this->_getParam('is_unlike');
    
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity() || !$subject){
      return ;
    }

    if ($is_unlike){
      $subject->likes()->removeLike($viewer);
    } else {
      $subject->likes()->addLike($viewer);
    }

    $this->view->result = true;

  }
  

}
