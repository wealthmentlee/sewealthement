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
    

class Mobile_Widget_PageTagCloudController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		$this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
		$params = array('page_id' => $subject->getIdentity());
		$api = Engine_Api::_()->getApi('core', 'page');

		$data = Engine_Api::_()->getDbTable('tags', 'page')->getCloud($params)->toArray();
		
		$cloud = array();
		foreach ($data as $item){
			$cloud[] = $api->defineTagClass($item);
		}

		if (empty($cloud)){
			return $this->setNoRender();
		}

		$this->view->cloud = $cloud;
  }
}