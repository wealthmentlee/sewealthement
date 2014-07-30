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
    
class Mobile_Widget_ListAnnouncementsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $table = Engine_Api::_()->getDbtable('announcements', 'announcement');
    $select = $table->select()
      //->order('announcement_id DESC')
      ->order('creation_date DESC')
      ;

    $paginator = Zend_Paginator::factory($select);
    
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
    $this->view->announcements = $paginator;
  }
}