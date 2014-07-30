<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-19 07:01:28 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Widget_HtmlBlockController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->data = $this->_getParam('data');
  }
}