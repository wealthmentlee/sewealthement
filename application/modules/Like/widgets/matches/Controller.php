<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_Widget_MatchesController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	
  public function indexAction()
  {
		if (Engine_Api::_()->core()->hasSubject()){
			$subject = Engine_Api::_()->core()->getSubject();
		}else{
			$subject = Engine_Api::_()->user()->getViewer();
		}

		if ($subject->getType() != 'user'){
			$subject = Engine_Api::_()->user()->getViewer();
		}

		if (!$subject->getIdentity()){
			$this->setNoRender();
			return ;
		}

		if (!Engine_Api::_()->like()->isAllowed($subject)){
			$this->setNoRender();
 			return ;
		}

		$this->view->subject = $subject;
		$data = Engine_Api::_()->like()->getMatches($subject);
		
		$this->view->items = $items = $data['paginator'];

		if (!$items->getTotalItemCount()){
			$this->setNoRender();
			return ;
		}

		$settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('like.matches_count', 9);

		$this->view->counts = $data['counts'];
		$this->view->items->setItemCountPerPage($ipp);
  }
  
  public function getChildCount()
  {
    return $this->_childCount;
  }
}