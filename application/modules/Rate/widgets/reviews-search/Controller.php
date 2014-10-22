<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-12-17 14:38:00 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Rate_Widget_ReviewsSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->filterForm = new Rate_Form_Review_Search();
  }
}