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

class Like_Widget_BoxController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	
  public function indexAction()
  {
    $this->view->widget = 'box';
    if (Engine_Api::_()->core()->hasSubject()) {
  	  $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    } else {
      $this->view->subject = $subject = Engine_Api::_()->user()->getViewer();
    }

    if (!$subject->getIdentity()) {
      $this->setNoRender();
      return ;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('like.likes_count', 9);
    $this->view->period = $period = $settings->getSetting('like.likes_period', 1);

    if ($period) {
      $this->view->week_likes = $week_likes = Engine_Api::_()->like()->getLikes($subject, 'week');
      $week_likes->setItemCountPerPage($ipp);

      $this->view->month_likes = $month_likes = Engine_Api::_()->like()->getLikes($subject, 'month');
      $month_likes->setItemCountPerPage($ipp);
    }

 		$this->view->all_likes = $all_likes = Engine_Api::_()->like()->getLikes($subject);
    if (!$all_likes) {
      $this->setNoRender();
 			return ;
    }

    $all_likes->setItemCountPerPage($ipp);

		if (!Engine_Api::_()->like()->isAllowed($subject)){
			$this->setNoRender();
 			return ;
		}
 		
 		if (!$all_likes || $all_likes->getTotalItemCount() == 0){
 			$this->setNoRender();
 			return ;
 		}
 		
    if( $this->_getParam('titleCount', false) && $all_likes->getTotalItemCount() > 0 ) {
      $this->_childCount = $all_likes->getTotalItemCount();
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $this->getElement()->setAttrib('class', 'like_widget_theme_' . $this->view->activeTheme());
  }
  
  public function getChildCount()
  {
    return $this->_childCount;
  }

  /*public function getCacheKey()
  {
    return Zend_Registry::get('Locale')->toString();
  }*/
}