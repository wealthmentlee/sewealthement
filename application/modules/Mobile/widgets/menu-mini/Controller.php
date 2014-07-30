<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Widget_MenuMiniController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

		$require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if(!$require_check){
      if( $viewer->getIdentity()){
        $this->view->search_check = true;
      }
      else{
        $this->view->search_check = false;
      }
    }
    else $this->view->search_check = true;

		$this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'mobile')
      ->getNavigation('core_mini');
		//$navigation->removePage();

    $count = (int)$this->_getParam('count', 3);
		$navigation_tmp = array();
		if ($navigation->count() > $count)
		{
			$i = $navigation->count();
			foreach($navigation as $nav)
			{
				$i--;
				if ($i >= $count)
				{
					$navigation_tmp[] = $nav;
				}
			}

			foreach($navigation_tmp as $nav_tmp)
			{
				$navigation->removePage($nav_tmp);
			}

			$this->view->more = $more = new Zend_Navigation_Page_Mvc(array(
				'label' => 'More+',
				'class' => 'menu_core_main core_mini_more',
				'visible' => 1,
				'action' => 'more-mini',
				'controller' => 'index',
				'module'=>'mobile',
				'route' => 'default',
				'order' => '999'
			));
		}

		if( $viewer->getIdentity() )
    {
      $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
    }
  }
}