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
    
class Mobile_Widget_MenuMainController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'mobile')
      ->getNavigation('core_main');

    $count = (int)$this->_getParam('count', 3);
		$navigation_tmp = array();
		if ($navigation->count() > $count)
		{
			$i = 0;
			foreach($navigation as $nav)
			{
				$i++;
				if ($i > $count)
				{
					$navigation_tmp[] = $nav;
				}
			}

			foreach($navigation_tmp as $nav_tmp)
			{
				$navigation->removePage($nav_tmp);
			}

			$otherPage = array(
				'label' => 'More+',
				'class' => 'menu_core_main core_main_more',
				'visible' => 1,
				'action' => 'more-main',
				'controller' => 'index',
				'module'=>'mobile',
				'route' => 'default',
			);

			$navigation->addPage($otherPage);
		}

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check && !$viewer->getIdentity()){
      $navigation->removePage($navigation->findOneBy('route','user_general'));
    }
  }

  public function getCacheKey()
  {
    //return Engine_Api::_()->user()->getViewer()->getIdentity();
  }
}