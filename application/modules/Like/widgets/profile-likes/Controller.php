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

class Like_Widget_ProfileLikesController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    $this->view->widget = 'profile_likes';
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (Engine_Api::_()->core()->hasSubject()) {
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    } else {
      $this->view->subject = $subject = $viewer;
    }

    if ($subject->getType() != 'user') {
      $this->setNoRender();
      return ;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'interest')) {
      $this->setNoRender();
      return ;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('like.profile_count', 9);
    $this->view->period = $period = $settings->getSetting('like.profile_period', 1);

    $params = array('poster_type' => $subject->getType(), 'poster_id' => $subject->getIdentity());

    if ($period) { //for week and month
      $this->view->total_week = $total_week = Engine_Api::_()->like()->getLikedCount($subject, 'week');
      $this->view->items_week = Engine_Api::_()->like()->getLikedItems($subject, false, 'week');
      shuffle($this->view->items_week);

      $select_week = Engine_Api::_()->like()->getLikesSelect($params, 'week');
      $select_week->where('like1.resource_type IN ("page", "user")');
      $this->view->likedMembersAndPages_week = Engine_Api::_()->like()->getTable()->fetchAll($select_week)->count();

      $this->view->total_month = $total_month = Engine_Api::_()->like()->getLikedCount($subject, 'month');
      $this->view->items_month = Engine_Api::_()->like()->getLikedItems($subject, false, 'month');
      shuffle($this->view->items_month);

      $select_month = Engine_Api::_()->like()->getLikesSelect($params, 'month');
      $select_month->where('like1.resource_type IN ("page", "user")');
      $this->view->likedMembersAndPages_month = Engine_Api::_()->like()->getTable()->fetchAll($select_month)->count();

    }

    $this->view->items_all = Engine_Api::_()->like()->getLikedItems($subject);
    shuffle($this->view->items_all);

    $this->view->total_all = $total_all = Engine_Api::_()->like()->getLikedCount($subject);

    if( $this->_getParam('titleCount', false) && $total_all > 0 ) {
      $this->_childCount = $total_all;
    }

    $this->view->ipp = $ipp;

    if (!$total_all || !count($this->view->items_all)) {
      $this->setNoRender();
      return ;
    }

    $select = Engine_Api::_()->like()->getLikesSelect($params);
    $select->where('like1.resource_type IN ("page", "user")');

    $this->view->likedMembersAndPages_all = Engine_Api::_()->like()->getTable()->fetchAll($select)->count();

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